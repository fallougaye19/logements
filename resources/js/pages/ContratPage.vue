<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Toast amélioré -->
        <transition name="fade">
            <div
                v-if="toast.show"
                :class="toastClasses"
                class="fixed top-6 right-6 z-50 px-6 py-3 text-white rounded-xl shadow-xl flex items-center gap-3 transition-all duration-300"
            >
                <span>{{ toast.message }}</span>
                <button
                    @click="toast.show = false"
                    class="text-white hover:text-gray-200"
                >
                    &times;
                </button>
            </div>
        </transition>

        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">
                        Mes Contrats
                    </h1>
                    <p class="text-gray-600 mt-2">
                        Gérez vos contrats de location
                    </p>
                </div>
                <button
                    @click="$router.back()"
                    class="btn-secondary flex items-center"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 mr-2"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    Retour
                </button>
            </div>

            <!-- Chargement -->
            <div v-if="loading" class="flex justify-center py-16">
                <div
                    class="animate-spin h-12 w-12 border-4 border-blue-500 border-t-transparent rounded-full"
                ></div>
            </div>

            <!-- Liste des contrats -->
            <div
                v-else-if="contrats.length"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
            >
                <ContratCard
                    v-for="contrat in contrats"
                    :key="contrat.id"
                    :contrat="contrat"
                    @view="goToDetails"
                    @historique="ouvrirHistorique"
                    class="transition-transform duration-300 hover:-translate-y-1"
                />
            </div>

            <!-- Aucune donnée -->
            <div v-else class="text-center py-16 bg-white rounded-xl shadow-sm">
                <div class="inline-block p-4 bg-blue-50 rounded-full mb-4">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-12 w-12 text-blue-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">
                    Aucun contrat trouvé
                </h3>
                <p class="text-gray-600 mb-6">
                    Vous n'avez aucun contrat actif
                </p>
            </div>
        </div>
        <!-- Historique des paiements -->
        <HistoriquePaiements
            v-if="contratSelectionne"
            :contrat-id="contratSelectionne.id"
            :user-role="userRole"
            @ajouter-paiement="ouvrirModalePaiement"
            @close="contratSelectionne = null"
        />
    </div>
</template>

<script>
import axios from "axios";
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import ContratCard from "@/components/contrats/ContratCard.vue"; // ⬅️ Chemin à adapter si besoin

export default {
    components: {
        ContratCard,
    },
    setup() {
        const contrats = ref([]);
        const loading = ref(true);
        const userRole = localStorage.getItem("userRole") || "";
        const token = localStorage.getItem("token");
        const router = useRouter();
        const contratSelectionne = ref(null);

        const ouvrirHistorique = (contrat) => {
            contratSelectionne.value = contrat;
        };

        const toast = ref({
            show: false,
            message: "",
            type: "info",
        });

        const showToast = (message, type = "info") => {
            toast.value = { show: true, message, type };
            setTimeout(() => (toast.value.show = false), 5000);
        };

        const loadContrats = async () => {
            if (!token) {
                showToast("Token manquant", "error");
                loading.value = false;
                return;
            }

            try {
                const res = await axios.get("/api/contrats", {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });

                if (res.data.success) {
                    contrats.value = res.data.data;
                } else {
                    showToast("Erreur lors du chargement", "error");
                }
            } catch (error) {
                console.error("Erreur chargement contrats:", error);
                showToast("Erreur serveur", "error");
            } finally {
                loading.value = false;
            }
        };

        const goToDetails = (id) => {
            router.push(`/contrats/${id}`);
        };

        onMounted(() => {
            loadContrats();
        });

        return {
            contrats,
            loading,
            toast,
            userRole,
            showToast,
            goToDetails,
            ouvrirHistorique,
            contratSelectionne,
            ouvrirHistorique,
            loadContrats,
        };
    },
};
</script>
<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter,
.fade-leave-to {
    opacity: 0;
}
.toast-success {
    @apply bg-green-500;
}
.toast-error {
    @apply bg-red-500;
}
.toast-info {
    @apply bg-blue-500;
}
.toast-warning {
    @apply bg-yellow-500;
}
.toastClasses {
    @apply fixed top-6 right-6 z-50 px-6 py-3 text-white rounded-xl shadow-xl flex items-center gap-3 transition-all duration-300;
}

.btn-secondary {
    @apply bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition-colors flex items-center;
}
</style>
