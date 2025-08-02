<template>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Détails du Contrat</h1>

        <!-- Retour -->
        <button
            @click="$router.back()"
            class="mb-4 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded"
        >
            Retour
        </button>

        <!-- Chargement -->
        <div v-if="loading" class="flex justify-center py-10">
            <div
                class="animate-spin h-8 w-8 border-2 border-blue-600 border-t-transparent rounded-full"
            ></div>
        </div>

        <!-- Erreur -->
        <div v-else-if="!contrat" class="text-center text-red-600">
            <p>Contrat introuvable.</p>
        </div>

        <!-- Détails -->
        <div v-else class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">
                Contrat #{{ contrat.id }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p>
                        <strong>Statut :</strong>
                        <span
                            :class="{
                                'text-green-600': contrat.statut === 'actif',
                                'text-red-600': contrat.statut === 'resilie',
                                'text-yellow-600': contrat.statut === 'expiré',
                            }"
                        >
                            {{ contrat.statut }}
                        </span>
                    </p>
                    <p>
                        <strong>Date de début :</strong>
                        {{ formatDate(contrat.date_debut) }}
                    </p>
                    <p v-if="contrat.date_fin">
                        <strong>Date de fin :</strong>
                        {{ formatDate(contrat.date_fin) }}
                    </p>
                    <p>
                        <strong>Loyer :</strong>
                        {{ formatPrix(contrat.chambre?.prix) }}/mois
                    </p>
                    <p>
                        <strong>Caution :</strong>
                        {{ formatPrix(contrat.montant_caution) }}
                        <span v-if="contrat.mois_caution"
                            >({{ contrat.mois_caution }} mois)</span
                        >
                    </p>
                    <p>
                        <strong>Mode de paiement :</strong>
                        {{ contrat.mode_paiement }}
                    </p>
                    <p>
                        <strong>Périodicité :</strong>
                        {{ periodiciteLabel(contrat.periodicite) }}
                    </p>
                </div>

                <div>
                    <h3 class="font-bold">Locataire</h3>
                    <p>{{ contrat.locataire?.nom }}</p>
                    <p>{{ contrat.locataire?.email }}</p>
                    <p>{{ contrat.locataire?.telephone }}</p>

                    <h3 class="mt-4 font-bold">Propriétaire</h3>
                    <p>{{ contrat.proprietaire?.nom }}</p>
                    <p>{{ contrat.proprietaire?.email }}</p>
                    <p>{{ contrat.proprietaire?.telephone }}</p>

                    <h3 class="mt-4 font-bold">Chambre</h3>
                    <p>{{ contrat.chambre?.titre }}</p>
                    <p>{{ contrat.chambre?.maison?.adresse }}</p>
                </div>
            </div>

            <!-- Résilier -->
            <div
                class="mt-6"
                v-if="userRole === 'proprietaire' || userRole === 'locataire'"
            >
                <button
                    @click="resilierContrat"
                    :disabled="contrat.statut !== 'actif'"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded disabled:opacity-50"
                >
                    Résilier le contrat
                </button>
            </div>
            <div
                class="mt-4"
                v-if="userRole === 'locataire' && contrat.statut === 'actif'"
            >
                <button
                    @click="payerContrat"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                >
                    Payer le contrat
                </button>

                <!-- Signaler un problème -->
                <button
                    v-if="userRole === 'locataire'"
                    @click="showProblemeForm = true"
                    class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded"
                >
                    Signaler un problème
                </button>
            </div>
            <!-- Modale de paiement -->
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

                    <PaiementForm
                        :contrat-id="contrat.id"
                        @created="onPaiementCreated"
                    />
                </div>
            </div>
            <!-- Modale de problème -->
            <div
                v-if="showProblemeForm"
                class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50"
            >
                <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg relative">
                    <button
                        @click="showProblemeForm = false"
                        class="absolute top-2 right-2 text-gray-600 hover:text-red-600 text-xl font-bold"
                    >
                        &times;
                    </button>
                    <ProblemeForm
                        :contrat-id="contrat.id"
                        @probleme-signe="onProblemeSigne"
                        @close="showProblemeForm = false"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import PaiementForm from "@/components/paiements/PaiementForm.vue";
import ProblemeForm from "@/components/problemes/ProblemeForm.vue";

export default {
    components: { 
        PaiementForm,
        ProblemeForm
    },
    setup() {
        const route = useRoute();
        const contrat = ref(null);
        const loading = ref(true);
        const showPaiementForm = ref(false);
        const showProblemeForm = ref(false);
        const token = localStorage.getItem("token");
        const userRole = localStorage.getItem("userRole") || "";

        const formatDate = (date) => new Date(date).toLocaleDateString("fr-FR");

        const formatPrix = (prix) =>
            new Intl.NumberFormat("fr-FR", {
                style: "currency",
                currency: "XOF",
            }).format(prix || 0);

        const periodiciteLabel = (periodicite) => {
            const labels = {
                mensuel: "Mensuel",
                trimestriel: "Trimestriel",
                annuel: "Annuel",
            };
            return labels[periodicite] || periodicite;
        };

        const locataire = () => contrat.value?.locataire || {};
        const proprietaire = () => contrat.value?.proprietaire || {};

        const loadContrat = async () => {
            try {
                const res = await axios.get(
                    `/api/contrats/${route.params.id}`,
                    {
                        headers: { Authorization: `Bearer ${token}` },
                    }
                );
                if (res.data.success) {
                    contrat.value = res.data.data;
                }
            } catch (error) {
                console.error("Erreur lors du chargement du contrat", error);
            } finally {
                loading.value = false;
            }
        };

        const resilierContrat = async () => {
            if (!confirm("Confirmer la résiliation de ce contrat ?")) return;

            try {
                const res = await axios.post(
                    `/api/contrats/${contrat.value.id}/resilier`,
                    {},
                    { headers: { Authorization: `Bearer ${token}` } }
                );
                if (res.data.success) {
                    contrat.value.statut = "resilie";
                    alert("Contrat résilié.");
                }
            } catch (error) {
                alert("Erreur lors de la résiliation.");
            }
        };

        // Affiche la modale de paiement
        const payerContrat = () => {
            showPaiementForm.value = true;
        };

        const onPaiementCreated = (paiement) => {
            if (!contrat.value.paiements) contrat.value.paiements = [];
            contrat.value.paiements.push(paiement);
            showPaiementForm.value = false;
            alert("Paiement ajouté avec succès !");
        };

        // Problème
        const onProblemeSigne = (message) => {
            alert(message);
            showProblemeForm.value = false;
            loadContrat();
        };

        onMounted(() => {
            loadContrat();
        });

        return {
            contrat,
            loading,
            userRole,
            formatDate,
            formatPrix,
            periodiciteLabel,
            resilierContrat,
            payerContrat,
            showPaiementForm,
            onPaiementCreated,
            showProblemeForm,
            onProblemeSigne,
            locataire,
            proprietaire,
        };
    },
};
</script>
