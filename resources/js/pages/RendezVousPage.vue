<template>
    <div class="p-4">
        <!-- Titre + bouton ajouter -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                Liste des rendez-vous
            </h2>
            <button v-if="isProprietaire" @click="openForm()" class="btn inline">
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
                Nouveau rendez-vous
            </button>
        </div>

        <!-- Filtres et recherche -->
        <div class="mb-6 flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input
                    v-model="searchTerm"
                    placeholder="Rechercher un rendez-vous..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                />
            </div>
            <select
                v-model="filterStatut"
                class="px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500"
            >
                <option disabled value="">-- Statut --</option>
                <option value="">Tous</option>
                <option value="confirmé">Confirmé</option>
                <option value="en attente">En attente</option>
                <option value="annulé">Annulé</option>
            </select>
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

        <!-- Grille des rendez-vous -->
        <div
            v-if="!loading && filteredRendezVous.length"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
        >
            <div
                v-for="rendezVous in paginatedRendezVous"
                :key="rendezVous.id"
                class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
            >
                <div class="p-4">
                    <h3 class="text-lg font-semibold truncate">
                        Rendez-vous #{{ rendezVous.id }}
                    </h3>
                    <p class="text-sm text-gray-600 line-clamp-2">
                        Locataire : {{ rendezVous.locataire?.nom || "Inconnu" }}
                    </p>
                    <p class="text-sm text-gray-600 mt-1">
                        Chambre : {{ rendezVous.chambre?.titre || "Aucune" }}
                    </p>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span
                            class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs"
                        >
                            Date : {{ formatDate(rendezVous.date_heure) }}
                        </span>
                        <span
                            :class="{
                                'bg-green-100 text-green-800':
                                    rendezVous.statut === 'confirmé',
                                'bg-yellow-100 text-yellow-800':
                                    rendezVous.statut === 'en attente',
                                'bg-red-100 text-red-800':
                                    rendezVous.statut === 'annulé',
                            }"
                            class="px-2 py-1 rounded-full text-xs font-medium"
                        >
                            {{ rendezVous.statut }}
                        </span>
                    </div>

                    <!-- Actions -->
                    <div class="mt-4 flex justify-between items-center">
                        <div class="flex space-x-2">
                            <button
                                @click="viewRendezVous(rendezVous)"
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
                            <button v-if="isProprietaire"
                                @click="editRendezVous(rendezVous)"
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
                                @click="deleteRendezVous(rendezVous.id)"
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
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1H7a1 1 0 00-1 1v3M4 7h16"
                                    />
                                </svg>
                            </button>
                        </div>
                        <span class="text-xs text-gray-500">{{
                            formatDate(rendezVous.created_at)
                        }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aucun résultat -->
        <div
            v-if="!loading && !filteredRendezVous.length"
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
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                />
            </svg>
            <p class="text-gray-600">Aucun rendez-vous trouvé</p>
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

        <!-- Modal Formulaire -->
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
                                editingRendezVous
                                    ? "Modifier le rendez-vous"
                                    : "Nouveau rendez-vous"
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
                    <RendezVousForm
                        :rendez-vous="editingRendezVous"
                        @created="handleCreated"
                        @updated="handleUpdated"
                        @cancel="closeForm"
                    />
                </div>
            </div>
        </div>

        <!-- Modal Visualisation -->
        <div
            v-if="viewingRendezVous"
            class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
        >
            <div
                class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
            >
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">
                            Détails du rendez-vous
                        </h3>
                        <button
                            @click="viewingRendezVous = null"
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
                                >Locataire</label
                            >
                            <p>
                                {{
                                    viewingRendezVous.locataire?.nom || "Aucun"
                                }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Chambre</label
                            >
                            <p>
                                {{
                                    viewingRendezVous.chambre?.titre || "Aucune"
                                }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Date et heure</label
                            >
                            <p>
                                {{ formatDate(viewingRendezVous.date_heure) }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Statut</label
                            >
                            <p>
                                <span
                                    :class="{
                                        'bg-green-100 text-green-800':
                                            viewingRendezVous.statut ===
                                            'confirmé',
                                        'bg-yellow-100 text-yellow-800':
                                            viewingRendezVous.statut ===
                                            'en attente',
                                        'bg-red-100 text-red-800':
                                            viewingRendezVous.statut ===
                                            'annulé',
                                    }"
                                    class="px-2 py-1 rounded-full text-xs font-medium"
                                >
                                    {{ viewingRendezVous.statut }}
                                </span>
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
import RendezVousForm from "@/components/RendezVousForm.vue";

const rendezVous = ref([]);
const showForm = ref(false);
const editingRendezVous = ref(null);
const viewingRendezVous = ref(null);
const loading = ref(false);
const error = ref("");
const successMessage = ref("");

// Filtres
const searchTerm = ref("");
const filterStatut = ref("");

// Pagination
const currentPage = ref(1);
const perPage = 10;

const user = JSON.parse(localStorage.getItem("user"));
const isProprietaire = computed(() => user?.role === 'proprietaire');
const isLocataire = computed(() => user?.role === 'locataire');

// Calcul des données filtrées
const filteredRendezVous = computed(() => {
    let filtered = [...rendezVous.value];

    // Recherche par texte
    if (searchTerm.value) {
        const term = searchTerm.value.toLowerCase();
        filtered = filtered.filter(
            (r) =>
                r.locataire?.nom.toLowerCase().includes(term) ||
                r.chambre?.titre.toLowerCase().includes(term)
        );
    }

    // Filtre par statut
    if (filterStatut.value) {
        filtered = filtered.filter((r) => r.statut === filterStatut.value);
    }

    return filtered;
});

const paginatedRendezVous = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    return filteredRendezVous.value.slice(start, start + perPage);
});

const totalPages = computed(() =>
    Math.ceil(filteredRendezVous.value.length / perPage)
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

// Méthodes
const fetchRendezVous = async () => {
    try {
        loading.value = true;
        error.value = "";
        const { data } = await axios.get("/api/rendez-vous");
        rendezVous.value = data;
    } catch (err) {
        error.value = "Erreur lors du chargement des rendez-vous";
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const openForm = (rendezVousItem = null) => {
    editingRendezVous.value = rendezVousItem;
    showForm.value = true;
};

const closeForm = () => {
    showForm.value = false;
    editingRendezVous.value = null;
};

const handleCreated = () => {
    fetchRendezVous();
    closeForm();
    showSuccessMessage("Rendez-vous créé avec succès");
};

const handleUpdated = () => {
    fetchRendezVous();
    closeForm();
    showSuccessMessage("Rendez-vous modifié avec succès");
};

const showSuccessMessage = (message) => {
    successMessage.value = message;
    setTimeout(() => (successMessage.value = ""), 3000);
};

const deleteRendezVous = async (id) => {
    if (confirm("Êtes-vous sûr de vouloir supprimer ce rendez-vous ?")) {
        try {
            await axios.post(`/api/rendez-vous/${id}`, { _method: "DELETE" });
            fetchRendezVous();
            showSuccessMessage("Rendez-vous supprimé avec succès");
        } catch (err) {
            error.value = "Erreur lors de la suppression";
            console.error(err);
        }
    }
};

const viewRendezVous = (rendezVousItem) => {
    viewingRendezVous.value = rendezVousItem;
};

const editRendezVous = (rendezVousItem) => {
    openForm(rendezVousItem);
};

const formatDate = (date) => {
    return new Date(date).toLocaleString("fr-FR");
};

watch([searchTerm, filterStatut], () => {
    currentPage.value = 1;
});

onMounted(fetchRendezVous);
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
