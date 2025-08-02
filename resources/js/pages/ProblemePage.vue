<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- En-tête -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    Problèmes signalés
                </h2>
                <p class="text-gray-600 mt-1">Gérez les problèmes techniques</p>
            </div>
            <button v-if="isLocataire" @click="openForm()" class="btn-primary">
                <svg
                    class="w-5 h-5 mr-2"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                    />
                </svg>
                Signaler un problème
            </button>
        </div>

        <!-- Filtres -->
        <div
            class="bg-white p-4 rounded-xl shadow-sm mb-6 grid grid-cols-1 md:grid-cols-4 gap-4"
        >
            <input
                v-model="searchTerm"
                type="text"
                placeholder="Rechercher..."
                class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
            />
            <select v-model="filterType" class="px-4 py-2 border rounded-lg">
                <option value="">Tous les types</option>
                <option value="plomberie">Plomberie</option>
                <option value="electricite">Électricité</option>
                <option value="serrurerie">Serrurerie</option>
                <option value="autre">Autre</option>
            </select>
            <select v-model="filterStatus" class="px-4 py-2 border rounded-lg">
                <option value="">Tous les statuts</option>
                <option value="0">Non résolu</option>
                <option value="1">Résolu</option>
            </select>
            <button
                @click="fetchProblemes"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
            >
                Filtrer
            </button>
        </div>

        <!-- Messages -->
        <div
            v-if="successMessage"
            class="bg-green-100 text-green-800 p-3 rounded mb-4"
        >
            {{ successMessage }}
        </div>
        <div v-if="error" class="bg-red-100 text-red-800 p-3 rounded mb-4">
            {{ error }}
        </div>

        <!-- Chargement -->
        <div v-if="loading" class="flex justify-center py-10">
            <div
                class="animate-spin h-10 w-10 border-4 border-blue-500 border-t-transparent rounded-full"
            ></div>
        </div>

        <!-- Liste des problèmes -->
        <div v-else class="grid gap-4">
            <div
                v-for="probleme in paginatedProblemes"
                :key="probleme.id"
                class="bg-white p-5 rounded-xl shadow hover:shadow-md transition-shadow border-l-4"
                :class="{
                    'border-red-500': !probleme.resolu,
                    'border-green-500': probleme.resolu,
                }"
            >
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-800">
                            {{ probleme.type }}
                        </h3>
                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                            {{ probleme.description }}
                        </p>
                        <div class="flex gap-4 mt-2 text-xs text-gray-500">
                            <span
                                >Chambre:
                                {{ probleme.contrat?.chambre?.titre }}</span
                            >
                            <span
                                >Signalé:
                                {{ formatDate(probleme.created_at) }}</span
                            >
                            <span
                                >Responsable:
                                {{ probleme.responsable || "Non défini" }}</span
                            >
                        </div>
                    </div>
                    <div class="flex gap-2 ml-4">
                        <button
                            @click="openView(probleme)"
                            class="text-blue-600 hover:text-blue-800 text-sm"
                        >
                            Voir
                        </button>
                        <button
                            v-if="isProprietaire && !probleme.resolu"
                            @click="marquerResolu(probleme)"
                            class="text-green-600 hover:text-green-800 text-sm"
                        >
                            Résoudre
                        </button>
                        <button
                            v-if="isLocataire && !probleme.resolu"
                            @click="editProbleme(probleme)"
                            class="text-yellow-600 hover:text-yellow-800 text-sm"
                        >
                            Modifier
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div
            v-if="!loading && problemes.length > 0"
            class="flex justify-center mt-8"
        >
            <div class="inline-flex rounded-md shadow">
                <button
                    @click="currentPage--"
                    :disabled="currentPage === 1"
                    class="px-4 py-2 bg-white border border-gray-300 text-sm font-medium text-gray-500 disabled:opacity-50"
                >
                    Précédent
                </button>
                <button
                    v-for="page in paginationRange"
                    :key="page"
                    @click="currentPage = page"
                    v-if="page !== '...'"
                    :class="{
                        'bg-blue-600 text-white': page === currentPage,
                        'bg-white text-gray-700': page !== currentPage,
                    }"
                    class="px-4 py-2 border border-gray-300 text-sm font-medium hover:bg-gray-50"
                >
                    {{ page }}
                </button>
                <span
                    v-else
                    class="px-4 py-2 bg-white border border-gray-300 text-sm text-gray-500"
                    >...</span
                >
                <button
                    @click="currentPage++"
                    :disabled="currentPage === totalPages"
                    class="px-4 py-2 bg-white border border-gray-300 text-sm font-medium text-gray-500 disabled:opacity-50"
                >
                    Suivant
                </button>
            </div>
        </div>

        <!-- Aucun problème -->
        <div
            v-if="!loading && problemes.length === 0"
            class="text-center py-16 bg-white rounded-xl shadow-sm"
        >
            <p class="text-gray-500">Aucun problème signalé.</p>
        </div>

        <!-- Modale de détail -->
        <div
            v-if="viewingProbleme"
            class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50"
        >
            <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">Détail du problème</h3>
                    <button
                        @click="viewingProbleme = null"
                        class="text-gray-500 hover:text-gray-700 text-xl"
                    >
                        &times;
                    </button>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-500"
                            >Type</label
                        >
                        <p>{{ viewingProbleme.type }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500"
                            >Description</label
                        >
                        <p>{{ viewingProbleme.description }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500"
                            >Signalé par</label
                        >
                        <p>
                            {{
                                viewingProbleme.auteur?.nom ||
                                "Utilisateur inconnu"
                            }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500"
                            >Responsable</label
                        >
                        <p>
                            {{ viewingProbleme.responsable || "Non assigné" }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500"
                            >Statut</label
                        >
                        <span
                            :class="{
                                'bg-green-100 text-green-800':
                                    viewingProbleme.resolu,
                                'bg-red-100 text-red-800':
                                    !viewingProbleme.resolu,
                            }"
                            class="px-2 py-1 rounded-full text-xs font-medium"
                        >
                            {{
                                viewingProbleme.resolu ? "Résolu" : "Non résolu"
                            }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modale d'ajout/modification -->
        <div
            v-if="showForm"
            class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50"
        >
            <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-lg">
                <ProblemeForm
                    :probleme="editingProbleme"
                    @close="closeForm"
                    @probleme-signe="onProblemeSigne"
                />
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { ref, computed, onMounted, watch } from "vue";
import ProblemeForm from "@/components/problemes/ProblemeForm.vue";

export default {
    components: { ProblemeForm },
    setup() {
        const problemes = ref([]);
        const loading = ref(true);
        const successMessage = ref("");
        const error = ref("");
        const searchTerm = ref("");
        const filterType = ref("");
        const filterStatus = ref("");
        const currentPage = ref(1);
        const perPage = 10;
        const viewingProbleme = ref(null);
        const showForm = ref(false);
        const editingProbleme = ref(null);

        const user = JSON.parse(localStorage.getItem("user") || "{}");
        const isLocataire = computed(() => user.role === "locataire");
        const isProprietaire = computed(() => user.role === "proprietaire");

        const fetchProblemes = async () => {
            loading.value = true;
            error.value = "";
            try {
                const response = await axios.get("/api/problemes", {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem(
                            "token"
                        )}`,
                    },
                });
                if (response.data.success) {
                    problemes.value = response.data.data;
                } else {
                    error.value =
                        response.data.message || "Erreur lors du chargement.";
                }
            } catch (err) {
                error.value = "Erreur de connexion au serveur.";
                console.error(err);
            } finally {
                loading.value = false;
            }
        };

        const marquerResolu = async (probleme) => {
            try {
                const response = await axios.post(
                    `/api/problemes/${probleme.id}/resoudre`,
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
                    probleme.resolu = true;
                    successMessage.value = "Problème marqué comme résolu.";
                    setTimeout(() => (successMessage.value = ""), 3000);
                }
            } catch (err) {
                error.value = "Erreur lors de la mise à jour.";
                console.error(err);
            }
        };

        const editProbleme = (probleme) => {
            openForm(probleme);
        };

        const openView = (probleme) => {
            viewingProbleme.value = probleme;
        };

        const openForm = (probleme = null) => {
            editingProbleme.value = probleme;
            showForm.value = true;
        };

        const closeForm = () => {
            showForm.value = false;
            editingProbleme.value = null;
        };

        const onProblemeSigne = (message) => {
            successMessage.value = message;
            setTimeout(() => (successMessage.value = ""), 3000);
            closeForm();
            fetchProblemes(); // Recharge la liste
        };

        const formatDate = (date) => {
            return date ? new Date(date).toLocaleDateString("fr-FR") : "N/A";
        };

        // Filtrage
        const filteredProblemes = computed(() => {
            return problemes.value.filter((p) => {
                const matchSearch =
                    !searchTerm.value ||
                    p.description
                        .toLowerCase()
                        .includes(searchTerm.value.toLowerCase()) ||
                    p.type
                        .toLowerCase()
                        .includes(searchTerm.value.toLowerCase());
                const matchType =
                    !filterType.value || p.type === filterType.value;
                const matchStatus =
                    filterStatus.value === "" ||
                    p.resolu === (filterStatus.value === "1");
                return matchSearch && matchType && matchStatus;
            });
        });

        const totalPages = computed(() =>
            Math.ceil(filteredProblemes.value.length / perPage)
        );
        const paginatedProblemes = computed(() => {
            const start = (currentPage.value - 1) * perPage;
            return filteredProblemes.value.slice(start, start + perPage);
        });

        const paginationRange = computed(() => {
            const delta = 2;
            const range = [];
            const rangeWithDots = [];
            for (
                let i = Math.max(2, currentPage.value - delta);
                i <= Math.min(totalPages.value - 1, currentPage.value + delta);
                i++
            ) {
                range.push(i);
            }
            if (currentPage.value - delta > 2) {
                rangeWithDots.push(1, "...");
            } else {
                rangeWithDots.push(1);
            }
            rangeWithDots.push(...range);
            if (currentPage.value + delta < totalPages.value - 1) {
                rangeWithDots.push("...", totalPages.value);
            } else {
                rangeWithDots.push(totalPages.value);
            }
            return rangeWithDots;
        });

        watch([searchTerm, filterType, filterStatus], () => {
            currentPage.value = 1;
        });

        onMounted(() => {
            fetchProblemes();
        });

        return {
            problemes,
            loading,
            successMessage,
            error,
            searchTerm,
            filterType,
            filterStatus,
            currentPage,
            perPage,
            viewingProbleme,
            showForm,
            editingProbleme,
            isLocataire,
            isProprietaire,
            fetchProblemes,
            marquerResolu,
            openView,
            openForm,
            closeForm,
            onProblemeSigne,
            formatDate,
            filteredProblemes,
            totalPages,
            paginatedProblemes,
            paginationRange,
            editProbleme,
        };
    },
};
</script>

<style scoped>
.btn-primary {
    @apply bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2;
}
</style>
