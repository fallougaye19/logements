<template>
    <div class="p-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Liste des maisons</h2>
            <button @click="openForm()" class="btn inline">
                <svg
                    class="w-4 h-4 mr-2"
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
                Ajouter une maison
            </button>
        </div>

        <!--Filtre-->
        <div class="mb-6 flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input
                    v-model="searchTerm"
                    placeholder="Rechercher une maison..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                />
            </div>
        </div>

        <!-- Loading state -->
        <div v-if="loading" class="text-center py-8">
            <div
                class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"
            ></div>
            <p class="mt-2 text-gray-600">Chargement...</p>
        </div>

        <!-- Message d'erreur -->
        <div
            v-if="error"
            class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"
        >
            {{ error }}
        </div>

        <!-- Message de succès -->
        <div
            v-if="successMessage"
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"
        >
            {{ successMessage }}
        </div>

        <!-- Liste des maisons -->
        <div
            v-if="!loading && filteredMaisons.length"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
        >
            <div
                v-for="maison in paginatedMaisons"
                :key="maison.id"
                class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
            >
                <!-- Image -->
                <div class="relative h-48 bg-gray-200">
                    <img
                        v-if="maison.image_principale"
                        :src="maison.image_principale"
                        class="w-full h-full object-cover"
                    />
                    <div
                        v-else
                        class="w-full h-full flex items-center justify-center"
                    >
                        <svg
                            class="w-16 h-16 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                            />
                        </svg>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 truncate">
                        {{ maison.adresse }}
                    </h3>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                        {{ maison.description || "Aucune description" }}
                    </p>
                    <p>
                        Propriétaire :
                        {{ chambre.proprietaire?.nom || "Inconnu" }}
                    </p>
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span
                            class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs"
                            >Propriétaire: {{ maison.proprietaire?.nom }}</span
                        >
                        <span
                            class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs"
                            >Chambres: {{ maison.chambres_count }}</span
                        >
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-2">
                            <button
                                @click="viewMaison(maison)"
                                class="btn-icon text-blue-600"
                                title="Voir les détails"
                            >
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z"
                                    />
                                </svg>
                            </button>
                            <button
                                @click="editMaison(maison)"
                                class="btn-icon text-yellow-600"
                                title="Modifier"
                            >
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                    />
                                </svg>
                            </button>
                            <button
                                @click="deleteMaison(maison.id)"
                                class="btn-icon text-red-600"
                                title="Supprimer"
                            >
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                    />
                                </svg>
                            </button>
                        </div>
                        <span class="text-xs text-gray-500">{{
                            formatDate(maison.created_at)
                        }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!--Aucun resultat-->
        <div
            v-if="!loading && !filteredMaisons.length"
            class="text-center py-12"
        >
            <svg
                class="w-16 h-16 text-gray-400 mx-auto mb-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                />
            </svg>
            <p class="text-gray-600">Aucune maison trouvée</p>
            <button @click="openForm()" class="btn mt-4">
                Ajouter la première maison
            </button>
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex justify-center space-x-2" v-if="totalPages > 1">
            <button
                class="btn-secondary"
                :disabled="currentPage === 1"
                @click="currentPage--"
            >
                Précédent
            </button>
            <template v-for="page in paginationRange" :key="page">
                <button
                    v-if="page !== '...'"
                    @click="currentPage = page"
                    :class="currentPage === page ? 'btn' : 'btn-secondary'"
                    class="px-3 py-2"
                >
                    {{ page }}
                </button>
                <span v-else class="px-3 py-2 text-gray-500">...</span>
            </template>
            <button
                class="btn-secondary"
                :disabled="currentPage === totalPages"
                @click="currentPage++"
            >
                Suivant
            </button>
        </div>

        <!-- Modal -->
        <div
            v-if="showForm"
            class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
        >
            <div
                class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
            >
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">
                            {{
                                editingMaison
                                    ? "Modifier la maison"
                                    : "Ajouter une maison"
                            }}
                        </h3>
                        <button
                            @click="closeForm"
                            class="text-gray-500 hover:text-gray-700"
                        >
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                    <MaisonForm
                        :maison="editingMaison"
                        @created="handleCreated"
                        @updated="handleUpdated"
                        @cancel="closeForm"
                    />
                </div>
            </div>
        </div>

        <!-- Modal de visualisation -->
        <div
            v-if="viewingMaison"
            class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
        >
            <div
                class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
            >
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">
                            Détails de la maison
                        </h3>
                        <button
                            @click="viewingMaison = null"
                            class="text-gray-500 hover:text-gray-700"
                        >
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Adresse</label
                            >
                            <p class="text-lg">{{ viewingMaison?.adresse }}</p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Description</label
                            >
                            <p>
                                {{
                                    viewingMaison?.description ||
                                    "Aucune description"
                                }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Propriétaire</label
                            >
                            <p>
                                {{
                                    viewingMaison?.proprietaire?.nom ||
                                    "Inconnu"
                                }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Coordonnées</label
                            >
                            <p>
                                Lat: {{ viewingMaison?.latitude }}, Long:
                                {{ viewingMaison?.longitude }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from "vue";
import axios from "axios";
import MaisonForm from "@/components/MaisonForm.vue";

const maisons = ref([]);
const showForm = ref(false);
const editingMaison = ref(null);
const viewingMaison = ref(null);
const loading = ref(false);
const error = ref("");
const successMessage = ref("");
const searchTerm = ref("");
// Pagination
const currentPage = ref(1);
const perPage = 10;

const filteredMaisons = computed(() => {
    return maisons.value.filter(
        (maison) =>
            maison.adresse
                .toLowerCase()
                .includes(searchTerm.value.toLowerCase()) ||
            (maison.description &&
                maison.description
                    .toLowerCase()
                    .includes(searchTerm.value.toLowerCase()))
    );
});

const paginatedMaisons = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    return filteredMaisons.value.slice(start, start + perPage);
});

const totalPages = computed(() =>
    Math.ceil(filteredMaisons.value.length / perPage)
);

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
    } else if (totalPages.value > 1) {
        rangeWithDots.push(totalPages.value);
    }

    return rangeWithDots;
});

const fetchMaisons = async () => {
    try {
        loading.value = true;
        const { data } = await axios.get(
            "/api/maisons?with=proprietaire,chambres"
        );
        maisons.value = data;
    } catch (err) {
        error.value = "Erreur lors du chargement des maisons";
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const openForm = (maison = null) => {
    editingMaison.value = maison;
    showForm.value = true;
};

const closeForm = () => {
    showForm.value = false;
    editingMaison.value = null;
};

const handleCreated = () => {
    fetchMaisons();
    closeForm();
    showSuccessMessage("Maison créée avec succès");
};

const handleUpdated = () => {
    fetchMaisons();
    closeForm();
    showSuccessMessage("Maison modifiée avec succès");
};

const showSuccessMessage = (msg) => {
    successMessage.value = msg;
    setTimeout(() => (successMessage.value = ""), 3000);
};

const deleteMaison = async (id) => {
    if (confirm("Êtes-vous sûr de vouloir supprimer cette maison ?")) {
        try {
            await axios.delete(`/api/maisons/${id}`);
            fetchMaisons();
            showSuccessMessage("Maison supprimée avec succès");
        } catch (err) {
            error.value = "Erreur lors de la suppression";
            console.error(err);
        }
    }
};

const viewMaison = (maison) => {
    viewingMaison.value = maison;
};

const editMaison = (maison) => {
    openForm(maison);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("fr-FR");
};

watch([searchTerm], () => {});

onMounted(fetchMaisons);
</script>

<style scoped>
.btn {
    @apply bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200 flex items-center;
}
.btn-secondary {
    @apply bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors duration-200;
}
.btn-icon {
    @apply p-2 rounded-full hover:bg-gray-100 transition-colors duration-200;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
