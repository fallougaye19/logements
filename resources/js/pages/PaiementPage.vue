<template>
    <div
        class="bg-white shadow-xl rounded-xl p-6 transition-all duration-300 hover:shadow-2xl"
    >
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-blue-700">
                Historique des Paiements
            </h3>
            <div class="flex items-center space-x-4">
                <div
                    class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-medium"
                >
                    {{ paiements.length }} paiement(s)
                </div>
                <!-- Bouton "Payer le contrat" visible uniquement pour les locataires -->
                <button
                    v-if="userRole === 'locataire'"
                    @click="showPaiementForm = true"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm shadow transition-all"
                >
                    Payer le contrat
                </button>
            </div>
        </div>

        <div v-if="paiements.length > 0">
            <div class="grid grid-cols-1 gap-4">
                <div
                    v-for="p in paiements"
                    :key="p.id"
                    class="border border-gray-200 rounded-xl p-4 hover:border-blue-300 transition-colors"
                >
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center mb-2">
                                <div
                                    class="w-3 h-3 rounded-full mr-2"
                                    :class="{
                                        'bg-green-500': p.statut === 'payé',
                                        'bg-red-500': p.statut === 'impayé',
                                        'bg-yellow-500': p.statut === 'retard',
                                    }"
                                ></div>
                                <span
                                    class="font-medium"
                                    :class="{
                                        'text-green-600': p.statut === 'payé',
                                        'text-red-600': p.statut === 'impayé',
                                        'text-yellow-600':
                                            p.statut === 'retard',
                                    }"
                                    >{{ p.statut }}</span
                                >
                            </div>
                            <div class="text-gray-500 text-sm mb-1">
                                <span class="font-medium">Échéance:</span>
                                {{ formatDate(p.date_echeance) }}
                            </div>
                            <div class="text-xl font-bold text-gray-800">
                                {{ formatPrix(p.montant) }}
                            </div>
                        </div>

                        <!-- Le bouton "Marquer payé" est maintenant correctement affiché pour les propriétaires -->
                        <div
                            v-if="p.statut === 'impayé' && userRole === 'proprietaire'"
                            class="flex flex-col items-end"
                        >
                            <button
                                @click="marquerPaye(p)"
                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-sm font-medium flex items-center transition-colors"
                                :disabled="loading"
                            >
                                <svg
                                    v-if="!loading"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 mr-1"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <span v-if="!loading">Marquer payé</span>
                                <span v-else class="flex items-center">
                                    <svg
                                        class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle
                                            class="opacity-25"
                                            cx="12"
                                            cy="12"
                                            r="10"
                                            stroke="currentColor"
                                            stroke-width="4"
                                        ></circle>
                                        <path
                                            class="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                        ></path>
                                    </svg>
                                    En cours...
                                </span>
                            </button>
                        </div>
                    </div>

                    <div
                        v-if="p.date_paiement"
                        class="mt-3 pt-3 border-t border-gray-100 text-sm"
                    >
                        <div class="text-gray-500">
                            <span class="font-medium">Payé le:</span>
                            {{ formatDate(p.date_paiement) }}
                        </div>
                        <div v-if="p.mode_paiement" class="text-gray-500">
                            <span class="font-medium">Moyen:</span>
                            {{ p.mode_paiement }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-8">
            <div class="inline-block p-4 bg-gray-100 rounded-full mb-3">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-12 w-12 text-gray-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
            </div>
            <p class="text-gray-500">
                Aucun paiement enregistré pour ce contrat.
            </p>
        </div>
    </div>

    <!-- Modale de paiement qui s'affiche lorsque showPaiementForm est vrai -->
    <div
        v-if="showPaiementForm"
        class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50"
    >
        <div
            class="bg-white p-6 rounded-lg shadow-lg w-full max-w-xl relative"
        >
            <button
                @click="showPaiementForm = false"
                class="absolute top-2 right-2 text-gray-600 hover:text-red-600 text-xl font-bold"
            >
                &times;
            </button>

            <!-- Le formulaire de paiement existant -->
            <PaiementForm
                :contrat-id="contratId"
                @created="onPaiementCreated"
            />
        </div>
    </div>
</template>

<script>
import axios from "axios";
import PaiementForm from "@/components/paiements/PaiementForm.vue";

export default {
    components: { PaiementForm },
    data() {
        return {
            paiements: [],
            loading: false,
            showPaiementForm: false,
            // Récupération de l'ID du contrat directement depuis l'URL
            contratId: this.$route.params.id,
            // Récupération du rôle de l'utilisateur depuis le localStorage
            userRole: localStorage.getItem('userRole'),
        };
    },
    async mounted() {
        await this.loadPaiements();
    },
    methods: {
        async loadPaiements() {
            try {
                const response = await axios.get(
                    `/api/paiements?contrat_id=${this.contratId}`,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                        },
                    }
                );
                this.paiements = response.data.data;
            } catch (error) {
                console.error("Erreur chargement paiements:", error);
            }
        },

        async marquerPaye(paiement) {
            this.loading = true;
            try {
                const response = await axios.post(
                    `/api/paiements/${paiement.id}/marquer-payé`,
                    {},
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                        },
                    }
                );

                if (response.data.success) {
                    paiement.statut = "payé";
                    paiement.date_paiement = response.data.data.date_paiement;
                    this.$emit("paiement-updated", response.data.data);
                }
            } catch (error) {
                alert("Erreur lors du paiement.");
            } finally {
                this.loading = false;
            }
        },

        formatDate(date) {
            return date ? new Date(date).toLocaleDateString("fr-FR") : "N/A";
        },

        formatPrix(prix) {
            return new Intl.NumberFormat("fr-FR", {
                style: "currency",
                currency: "XOF",
            }).format(prix);
        },

        onPaiementCreated(newPaiement) {
            this.paiements.unshift(newPaiement);
            this.showPaiementForm = false;
        },
    },
};
</script>

<style scoped>
/* Animation pour l'entrée des éléments */
.card-enter-active {
    transition: all 0.3s ease;
}
.card-enter-from {
    opacity: 0;
    transform: translateY(10px);
}
</style>
