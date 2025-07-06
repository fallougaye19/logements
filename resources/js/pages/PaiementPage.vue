<template>
    <div class="p-4">
        <!-- Titre + bouton ajouter -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                Liste des paiements
            </h2>
            <button v-if="!isLocataire" @click="openForm()" class="btn inline">
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
                Nouveau paiement
            </button>
        </div>

        <!-- Filtres et recherche -->
        <div class="mb-6 flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input
                    v-model="searchTerm"
                    placeholder="Rechercher un paiement..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                />
            </div>
            <select
                v-model="filterStatut"
                class="px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500"
            >
                <option disabled value="">-- Statut --</option>
                <option value="">Tous</option>
                <option value="payé">Payé</option>
                <option value="en attente">En attente</option>
                <option value="retard">Retard</option>
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

        <!-- Grille des paiements -->
        <div
            v-if="!loading && filteredPaiements.length"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
        >
            <div
                v-for="paiement in paginatedPaiements"
                :key="paiement.id"
                class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
            >
                <div class="p-4">
                    <h3 class="text-lg font-semibold truncate">
                        Paiement #{{ paiement.id }}
                    </h3>
                    <p class="text-sm text-gray-600 line-clamp-2">
                        Contrat : {{ paiement.contrat?.id || "Aucun" }}
                    </p>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span
                            class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs"
                        >
                            Montant : {{ formatPrice(paiement.montant) }}
                        </span>
                        <span
                            class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs"
                        >
                            Échéance : {{ formatDate(paiement.date_echeance) }}
                        </span>
                    </div>

                    <!-- Statut -->
                    <div class="mt-3">
                        <span
                            :class="{
                                'bg-green-100 text-green-800':
                                    paiement.statut === 'payé',
                                'bg-yellow-100 text-yellow-800':
                                    paiement.statut === 'en attente',
                                'bg-red-100 text-red-800':
                                    paiement.statut === 'retard',
                            }"
                            class="px-2 py-1 rounded-full text-xs font-medium"
                        >
                            {{ paiement.statut }}
                        </span>
                    </div>

                    <!-- Actions -->
                    <div class="mt-4 flex justify-between items-center">
                        <div class="flex space-x-2">
                            <button
                                @click="viewPaiement(paiement)"
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
                                v-if="isProprietaire"
                                @click="editPaiement(paiement)"
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
                                v-if="isProprietaire"
                                @click="deletePaiement(paiement.id)"
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
                                        d="M19 7l-.867 12.142A2 0 0116.138 21H7.862a2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1H7a1 1 0 00-1 1v3M4 7h16"
                                    />
                                </svg>
                            </button>
                        </div>
                        <span class="text-xs text-gray-500">{{
                            formatDate(paiement.created_at)
                        }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aucun résultat -->
        <div
            v-if="!loading && !filteredPaiements.length"
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
            <p class="text-gray-600">Aucun paiement trouvé</p>
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
                                editingPaiement
                                    ? "Modifier le paiement"
                                    : "Nouveau paiement"
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
                    <PaiementForm
                        :paiement="editingPaiement"
                        @created="handleCreated"
                        @updated="handleUpdated"
                        @cancel="closeForm"
                    />
                </div>
            </div>
        </div>

        <!-- Modal Visualisation -->
        <div
            v-if="viewingPaiement"
            class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
        >
            <div
                class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
            >
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">
                            Détails du paiement
                        </h3>
                        <button
                            @click="viewingPaiement = null"
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
                                >Contrat</label
                            >
                            <p>
                                Contrat #{{
                                    viewingPaiement.contrat?.id || "Inconnu"
                                }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Montant</label
                            >
                            <p>{{ formatPrice(viewingPaiement.montant) }}</p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Date d'échéance</label
                            >
                            <p>
                                {{ formatDate(viewingPaiement.date_echeance) }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Date de paiement</label
                            >
                            <p>
                                {{
                                    viewingPaiement.date_paiement
                                        ? formatDate(
                                                viewingPaiement.date_paiement
                                            )
                                        : "Non payé"
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
                                            viewingPaiement.statut === 'payé',
                                        'bg-yellow-100 text-yellow-800':
                                            viewingPaiement.statut ===
                                            'en attente',
                                        'bg-red-100 text-red-800':
                                            viewingPaiement.statut === 'retard',
                                    }"
                                    class="px-2 py-1 rounded-full text-xs font-medium"
                                >
                                    {{ viewingPaiement.statut }}
                                </span>
                            </p>
                        </div>
                        <div
                            v-if="
                                isProprietaire &&
                                viewingPaiement.statut !== 'payé'
                            "
                        >
                            <button
                                @click="confirmerPaiement(viewingPaiement.id)"
                                class="btn mt-4 bg-green-600 hover:bg-green-700"
                            >
                                Confirmer le paiement
                            </button>
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
import PaiementForm from "@/components/PaiementForm.vue";

const paiements = ref([]);
const showForm = ref(false);
const editingPaiement = ref(null);
const viewingPaiement = ref(null);
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
const isProprietaire = computed(() => user?.role === "proprietaire");


const confirmerPaiement = async (id) => {
    if (!confirm("Voulez-vous confirmer ce paiement ?")) return;
        try {
            await axios.put(`/api/paiements/${id}`, { statut: "payé" });
            fetchPaiements();
            showSuccessMessage("Paiement confirmé !");
        } catch (e) {
            error.value = "Erreur lors de la confirmation";
            console.error(e);
        }
};

// Calcul des données filtrées
const filteredPaiements = computed(() => {
    let filtered = [...paiements.value];

    // Recherche par texte
    if (searchTerm.value) {
        const term = searchTerm.value.toLowerCase();
        filtered = filtered.filter(
            (p) =>
                p.contrat?.id.toString().includes(term) ||
                p.statut.includes(term)
        );
    }

    // Filtre par statut
    if (filterStatut.value) {
        filtered = filtered.filter((p) => p.statut === filterStatut.value);
    }

    return filtered;
});

const paginatedPaiements = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    return filteredPaiements.value.slice(start, start + perPage);
});

const totalPages = computed(() =>
    Math.ceil(filteredPaiements.value.length / perPage)
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
const fetchPaiements = async () => {
    try {
        loading.value = true;
        error.value = "";
        const { data } = await axios.get("/api/paiements");
        paiements.value = data;
    } catch (err) {
        error.value = "Erreur lors du chargement des paiements";
        console.error(err);
    } finally {
        loading.value = false;
    }
};



const openForm = (paiement = null) => {
    editingPaiement.value = paiement;
    showForm.value = true;
};

const closeForm = () => {
    showForm.value = false;
    editingPaiement.value = null;
};

const handleCreated = () => {
    fetchPaiements();
    closeForm();
    showSuccessMessage("Paiement créé avec succès");
};

const handleUpdated = () => {
    fetchPaiements();
    closeForm();
    showSuccessMessage("Paiement modifié avec succès");
};

const showSuccessMessage = (message) => {
    successMessage.value = message;
    setTimeout(() => (successMessage.value = ""), 3000);
};

const deletePaiement = async (id) => {
    if (confirm("Êtes-vous sûr de vouloir supprimer ce paiement ?")) {
        try {
            await axios.post(`/api/paiements/${id}`, { _method: "DELETE" });
            fetchPaiements();
            showSuccessMessage("Paiement supprimé avec succès");
        } catch (err) {
            error.value = "Erreur lors de la suppression";
            console.error(err);
        }
    }
};

const viewPaiement = (paiement) => {
    viewingPaiement.value = paiement;
};

const editPaiement = (paiement) => {
    openForm(paiement);
};

const formatPrice = (price) => {
    return new Intl.NumberFormat("fr-FR", {
        style: "currency",
        currency: "XOF",
    }).format(price);
};

const formatDate = (date) => {
    return date ? new Date(date).toLocaleDateString("fr-FR") : "N/A";
};

watch([searchTerm, filterStatut], () => {
    currentPage.value = 1;
});

onMounted(fetchPaiements);
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
