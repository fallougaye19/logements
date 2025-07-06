<template>
    <div class="p-4">
        <!-- Titre + bouton ajouter -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Liste des contrats</h2>
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
                Nouveau contrat
            </button>
        </div>

        <!-- Filtres -->
        <div class="mb-6 flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input
                    v-model="searchTerm"
                    placeholder="Rechercher un contrat..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                />
            </div>
            <select v-if="isProprietaire"
                v-model="filterStatut"
                class="px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500"
            >
                <option disabled value="">-- Statut --</option>
                <option value="">Tous</option>
                <option value="actif">Actif</option>
                <option value="termine">Terminé</option>
                <option value="expirant">Expirant</option>
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

        <!-- Grille des contrats -->
        <div
            v-if="!loading && filteredContrats.length"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
        >
            <div
                v-for="contrat in paginatedContrats"
                :key="contrat.id"
                class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
            >
                <div class="p-4">
                    <h3 class="text-lg font-semibold truncate">
                        Contrat #{{ contrat.id }}
                    </h3>
                    <p class="text-sm text-gray-600 line-clamp-2">
                        Locataire : {{ contrat.locataire?.nom || "Inconnu" }}
                    </p>
                    <p class="text-sm text-gray-600 mt-1">
                        Chambre : {{ contrat.chambre?.titre || "Aucune" }}
                    </p>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span
                            class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs"
                        >
                            Du {{ formatDate(contrat.date_debut) }}
                        </span>
                        <span
                            class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs"
                        >
                            Au {{ formatDate(contrat.date_fin) }}
                        </span>
                    </div>

                    <!-- Statut -->
                    <div class="mt-3">
                        <span
                            :class="{
                                'bg-green-100 text-green-800':
                                    contrat.statut === 'actif',
                                'bg-red-100 text-red-800':
                                    contrat.statut === 'termine',
                                'bg-yellow-100 text-yellow-800':
                                    contrat.statut === 'expirant',
                            }"
                            class="px-2 py-1 rounded-full text-xs font-medium"
                        >
                            {{ contrat.statut }}
                        </span>
                    </div>

                    <!-- Actions -->
                    <div class="mt-4 flex justify-between items-center">
                        <div class="flex space-x-2">
                            <button
                                @click="viewContrat(contrat)"
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
                                @click="editContrat(contrat)"
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
                            <button v-if="isProprietaire"
                                @click="deleteContrat(contrat.id)"
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
                                        d="M19 7l-.867 12.142A2 0 0116.138 21H7.862a2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 0 00-1-1h-4a1 0 00-1 1v3M4 7h16"
                                    />
                                </svg>
                            </button>
                        </div>
                        <span class="text-xs text-gray-500">{{
                            formatDate(contrat.created_at)
                        }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aucun résultat -->
        <div
            v-if="!loading && !filteredContrats.length"
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
            <p class="text-gray-600">Aucun contrat trouvé</p>
            <button v-if="isProprietaire" @click="openForm()" class="btn mt-4">
                Ajouter le premier contrat
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
                                editingContrat
                                    ? "Modifier le contrat"
                                    : "Nouveau contrat"
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
                    <ContratForm
                        :contrat="editingContrat"
                        @created="handleCreated"
                        @updated="handleUpdated"
                        @cancel="closeForm"
                    />
                </div>
            </div>
        </div>

        <!-- Modal Visualisation -->
        <div
            v-if="viewingContrat"
            class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
        >
            <div
                class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
            >
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">
                            Détails du contrat
                        </h3>
                        <button
                            @click="viewingContrat = null"
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
                            <p class="text-lg">
                                {{ viewingContrat.locataire?.nom || "Aucun" }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Chambre</label
                            >
                            <p>
                                {{ viewingContrat.chambre?.titre || "Aucune" }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Période</label
                            >
                            <p>
                                du
                                {{ formatDate(viewingContrat.date_debut) }} au
                                {{ formatDate(viewingContrat.date_fin) }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Montant de caution</label
                            >
                            <p>
                                {{
                                    formatPrice(viewingContrat.montant_caution)
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
                                            viewingContrat.statut === 'actif',
                                        'bg-red-100 text-red-800':
                                            viewingContrat.statut === 'termine',
                                        'bg-yellow-100 text-yellow-800':
                                            viewingContrat.statut ===
                                            'expirant',
                                    }"
                                    class="px-2 py-1 rounded-full text-xs font-medium"
                                >
                                    {{ viewingContrat.statut }}
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
import ContratForm from "@/components/ContratForm.vue";

const contrats = ref([]);
const showForm = ref(false);
const editingContrat = ref(null);
const viewingContrat = ref(null);
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

// Calcul des données filtrées
const filteredContrats = computed(() => {
    let filtered = [...contrats.value];

    // Recherche par texte
    if (searchTerm.value) {
        const term = searchTerm.value.toLowerCase();
        filtered = filtered.filter(
            (c) =>
                c.locataire?.nom.toLowerCase().includes(term) ||
                c.chambre?.titre.toLowerCase().includes(term)
        );
    }

    // Filtre par statut
    if (filterStatut.value) {
        filtered = filtered.filter((c) => c.statut === filterStatut.value);
    }

    return filtered;
});

const paginatedContrats = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    return filteredContrats.value.slice(start, start + perPage);
});

const totalPages = computed(() =>
    Math.ceil(filteredContrats.value.length / perPage)
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
const fetchContrats = async () => {
    try {
        loading.value = true;
        error.value = "";
        const { data } = await axios.get("/api/contrats");
        contrats.value = data;
    } catch (err) {
        error.value = "Erreur lors du chargement des contrats";
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const openForm = (contrat = null) => {
    editingContrat.value = contrat;
    showForm.value = true;
};

const closeForm = () => {
    showForm.value = false;
    editingContrat.value = null;
};

const handleCreated = () => {
    fetchContrats();
    closeForm();
    showSuccessMessage("Contrat créé avec succès");
};

const handleUpdated = () => {
    fetchContrats();
    closeForm();
    showSuccessMessage("Contrat modifié avec succès");
};

const showSuccessMessage = (message) => {
    successMessage.value = message;
    setTimeout(() => (successMessage.value = ""), 3000);
};

const deleteContrat = async (id) => {
    if (confirm("Êtes-vous sûr de vouloir supprimer ce contrat ?")) {
        try {
            await axios.post(`/api/contrats/${id}`, { _method: "DELETE" });
            fetchContrats();
            showSuccessMessage("Contrat supprimé avec succès");
        } catch (err) {
            error.value = "Erreur lors de la suppression";
            console.error(err);
        }
    }
};

const viewContrat = (contrat) => {
    viewingContrat.value = contrat;
};

const editContrat = (contrat) => {
    openForm(contrat);
};

const formatPrice = (price) => {
    return new Intl.NumberFormat("fr-FR", {
        style: "currency",
        currency: "XOF",
    }).format(price);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("fr-FR");
};

watch([searchTerm, filterStatut], () => {
    currentPage.value = 1;
});

onMounted(fetchContrats);
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
