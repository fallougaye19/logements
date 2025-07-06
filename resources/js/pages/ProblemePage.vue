<template>
    <div class="p-4">
        <!-- Titre + bouton ajouter -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                Liste des problèmes
            </h2>
            <button v-if="isLocataire" @click="openForm()" class="btn inline">
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
                Signaler un problème
            </button>
        </div>

        <!-- Filtres -->
        <div class="mb-6 flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input
                    v-model="searchTerm"
                    placeholder="Rechercher un problème..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                />
            </div>
            <select
                v-model="filterType"
                class="px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500"
            >
                <option disabled value="">-- Type --</option>
                <option value="">Tous</option>
                <option value="entretien">Entretien</option>
                <option value="plomberie">Plomberie</option>
                <option value="electricite">Électricité</option>
                <option value="autre">Autre</option>
            </select>
            <select
                v-model="filterStatus"
                class="px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500"
            >
                <option disabled value="">-- Statut --</option>
                <option value="">Tous</option>
                <option value="true">Résolu</option>
                <option value="false">Non résolu</option>
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

        <!-- Grille des problèmes -->
        <div
            v-if="!loading && filteredProblemes.length"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
        >
            <div
                v-for="probleme in paginatedProblemes"
                :key="probleme.id"
                class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
            >
                <div class="p-4">
                    <h3 class="text-lg font-semibold truncate">
                        {{ probleme.type }}
                    </h3>
                    <p class="text-sm text-gray-600 line-clamp-2">
                        Auteur : {{ probleme.auteur?.nom || "Inconnu" }}
                    </p>
                    <p class="text-sm text-gray-600 mt-1">
                        Contrat : #{{ probleme.contrat_id }}
                    </p>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span
                            class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs"
                        >
                            Signalé par : {{ probleme.signale_par }}
                        </span>
                        <span
                            class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs"
                        >
                            Résolu : {{ probleme.resolu ? "Oui" : "Non" }}
                        </span>
                    </div>

                    <!-- Statut résolu -->
                    <div class="mt-3">
                        <span
                            :class="{
                                'bg-green-100 text-green-800': probleme.resolu,
                                'bg-red-100 text-red-800': !probleme.resolu,
                            }"
                            class="px-2 py-1 rounded-full text-xs font-medium"
                        >
                            {{ probleme.resolu ? "Résolu" : "Non résolu" }}
                        </span>
                    </div>

                    <!-- Actions -->
                    <div class="mt-4 flex justify-between items-center">
                        <div class="flex space-x-2">
                            <button
                                @click="viewProbleme(probleme)"
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
                            <button v-if="isLocataire"
                                @click="editProbleme(probleme)"
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
                                @click="deleteProbleme(probleme.id)"
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
                                        d="M19 7l-.867 12.142A2 0 0116.138 21H7.862a2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                    />
                                </svg>
                            </button>
                        </div>
                        <span class="text-xs text-gray-500">{{
                            formatDate(probleme.created_at)
                        }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aucun résultat -->
        <div
            v-if="!loading && !filteredProblemes.length"
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
                    d="M9 12h6m-6 0h6m2 12H7a2 2 0 01-2-2V6a2 2 0 012-2h16a2 2 0 012 2v12a2 2 0 01-2 2z"
                />
            </svg>
            <p class="text-gray-600">Aucun problème trouvé</p>
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
                                editingProbleme
                                    ? "Modifier le problème"
                                    : "Nouveau problème"
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
                    <ProblemeForm
                        :probleme="editingProbleme"
                        @created="handleCreated"
                        @updated="handleUpdated"
                        @cancel="closeForm"
                    />
                </div>
            </div>
        </div>

        <!-- Modal Visualisation -->
        <div
            v-if="viewingProbleme"
            class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
        >
            <div
                class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
            >
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">
                            Détails du problème
                        </h3>
                        <button
                            @click="viewingProbleme = null"
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
                                >Type</label
                            >
                            <p>{{ viewingProbleme.type }}</p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Description</label
                            >
                            <p>{{ viewingProbleme.description }}</p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Signalé par</label
                            >
                            <p>
                                {{
                                    viewingProbleme.auteur?.nom ||
                                    "Utilisateur ID: " +
                                        viewingProbleme.signale_par
                                }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Responsable</label
                            >
                            <p>
                                {{
                                    viewingProbleme.responsable || "Non assigné"
                                }}
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
                                            viewingProbleme.resolu,
                                        'bg-red-100 text-red-800':
                                            !viewingProbleme.resolu,
                                    }"
                                    class="px-2 py-1 rounded-full text-xs font-medium"
                                >
                                    {{
                                        viewingProbleme.resolu
                                            ? "Résolu"
                                            : "Non résolu"
                                    }}
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
import ProblemeForm from "@/components/ProblemeForm.vue";

const problemes = ref([]);
const showForm = ref(false);
const editingProbleme = ref(null);
const viewingProbleme = ref(null);
const loading = ref(false);
const error = ref("");
const successMessage = ref("");

// Filtres
const searchTerm = ref("");
const filterType = ref("");
const filterStatus = ref("");

// Pagination
const currentPage = ref(1);
const perPage = 10;

const user = JSON.parse(localStorage.getItem("user"));
const isProprietaire = computed(() => user?.role === 'proprietaire');
const isLocataire = computed(() => user?.role === 'locataire');

// Calcul des données filtrées
const filteredProblemes = computed(() => {
    return problemes.value.filter((p) => {
        const termMatch =
            p.type.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
            (p.description &&
                p.description
                    .toLowerCase()
                    .includes(searchTerm.value.toLowerCase())) ||
            p.responsable
                ?.toLowerCase()
                .includes(searchTerm.value.toLowerCase());

        const typeMatch = filterType.value ? p.type === filterType.value : true;
        const statusMatch =
            filterStatus.value !== ""
                ? String(p.resolu) === filterStatus.value
                : true;

        return termMatch && typeMatch && statusMatch;
    });
});

const paginatedProblemes = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    return filteredProblemes.value.slice(start, start + perPage);
});

const totalPages = computed(() =>
    Math.ceil(filteredProblemes.value.length / perPage)
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
    } else {
        rangeWithDots.push(totalPages.value);
    }

    return rangeWithDots;
});

// Méthodes
const fetchProblemes = async () => {
    try {
        loading.value = true;
        error.value = "";
        const { data } = await axios.get("/api/problemes");
        problemes.value = data;
    } catch (err) {
        error.value = "Erreur lors du chargement des problèmes";
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const openForm = (probleme = null) => {
    editingProbleme.value = probleme;
    showForm.value = true;
};

const closeForm = () => {
    showForm.value = false;
    editingProbleme.value = null;
};

const handleCreated = () => {
    fetchProblemes();
    closeForm();
    showSuccessMessage("Problème signalé avec succès");
};

const handleUpdated = () => {
    fetchProblemes();
    closeForm();
    showSuccessMessage("Problème mis à jour");
};

const showSuccessMessage = (message) => {
    successMessage.value = message;
    setTimeout(() => (successMessage.value = ""), 3000);
};

const deleteProbleme = async (id) => {
    if (confirm("Êtes-vous sûr de vouloir supprimer ce problème ?")) {
        try {
            await axios.post(`/api/problemes/${id}`, { _method: "DELETE" });
            fetchProblemes();
            showSuccessMessage("Problème supprimé avec succès");
        } catch (err) {
            error.value = "Erreur lors de la suppression";
            console.error(err);
        }
    }
};

const viewProbleme = (probleme) => {
    viewingProbleme.value = probleme;
};

const editProbleme = (probleme) => {
    openForm(probleme);
};

const formatDate = (date) => {
    return date ? new Date(date).toLocaleDateString("fr-FR") : "N/A";
};

watch([searchTerm, filterType, filterStatus], () => {
    currentPage.value = 1;
});

onMounted(fetchProblemes);
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
