<template>
    <div class="p-4">
        <!-- Titre + bouton ajouter -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Liste des chambres</h2>
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
                Ajouter une chambre
            </button>
        </div>

        <!-- Filtres et recherche -->
        <div class="mb-6 flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input
                    v-model="searchTerm"
                    placeholder="Rechercher une chambre..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                />
            </div>
            <select
                v-model="filterType"
                class="px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
            >
                <option value="">Tous les types</option>
                <option value="simple">Chambre simple</option>
                <option value="appartement">Appartement</option>
                <option value="maison">Maison</option>
            </select>
            <select v-if="isProprietaire"
                v-model="filterStatus"
                class="px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
            >
                <option value="">Tous les statuts</option>
                <option value="true">Disponible</option>
                <option value="false">Occupé</option>
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

        <!-- Grille des chambres -->
        <div
            v-if="!loading && filteredChambres.length"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
        >
            <div
                v-for="chambre in paginatedChambres"
                :key="chambre.id"
                class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
            >
                <!-- Image -->
                <div class="relative h-48 bg-gray-200">
                    <img
                        v-if="chambre.image_principale"
                        :src="chambre.image_principale"
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
                    <!-- Statut -->
                    <div v-if="isProprietaire" class="absolute top-3 right-3">
                        <span
                            :class="
                                chambre.disponible
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-red-100 text-red-800'
                            "
                            class="px-2 py-1 rounded-full text-xs font-medium"
                        >
                            {{ chambre.disponible ? "Disponible" : "Occupé" }}
                        </span>
                    </div>
                </div>

                <!-- Contenu -->
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3
                            class="text-lg font-semibold text-gray-900 truncate"
                        >
                            {{ chambre.titre }}
                        </h3>
                        <span class="text-lg font-bold text-blue-600">{{
                            formatPrice(chambre.prix)
                        }}</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                        {{ chambre.description || "Aucune description" }}
                    </p>
                    <!-- Informations -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span
                            class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs"
                            >{{ chambre.type }}</span
                        >
                        <span
                            v-if="chambre.taille"
                            class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs"
                            >{{ chambre.taille }}</span
                        >
                        <span
                            v-if="chambre.meublee"
                            class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs"
                            >Meublé</span
                        >
                        <span
                            v-if="chambre.salle_de_bain"
                            class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs"
                            >SDB privée</span
                        >
                    </div>
                    <!-- Actions -->
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-2">
                            <button
                                @click="viewChambre(chambre)"
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
                                @click="editChambre(chambre)"
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
                                @click="deleteChambre(chambre.id)"
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
                            formatDate(chambre.cree_le)
                        }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aucun résultat -->
        <div
            v-if="!loading && !filteredChambres.length"
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
            <p class="text-gray-600">Aucune chambre trouvée</p>
            <button @click="openForm()" class="btn mt-4">
                Ajouter la première chambre
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
                                editingChambre
                                    ? "Modifier la chambre"
                                    : "Ajouter une chambre"
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
                    <ChambreForm
                        :chambre="editingChambre"
                        @created="handleCreated"
                        @updated="handleUpdated"
                        @cancel="closeForm"
                    />
                </div>
            </div>
        </div>
    </div>
    <!-- Modal de visualisation -->
    <div
        v-if="viewingChambre"
        class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
    >
        <div
            class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
        >
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Détails de la chambre</h3>
                    <button
                        @click="viewingChambre = null"
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
                        <label class="block text-sm font-medium text-gray-500"
                            >Titre</label
                        >
                        <p class="text-lg">{{ viewingChambre?.titre }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500"
                            >Description</label
                        >
                        <p>
                            {{
                                viewingChambre?.description ||
                                "Aucune description"
                            }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500"
                            >Prix</label
                        >
                        <p>{{ formatPrice(viewingChambre?.prix) }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500"
                            >Type</label
                        >
                        <p>{{ viewingChambre?.type }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500"
                            >Statut</label
                        >
                        <span
                            :class="
                                viewingChambre?.disponible
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-red-100 text-red-800'
                            "
                            class="px-2 py-1 rounded-full text-xs font-medium"
                        >
                            {{
                                viewingChambre?.disponible
                                    ? "Disponible"
                                    : "Occupé"
                            }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from "vue";
import axios from "axios";
import ChambreForm from "@/components/ChambreForm.vue";

const chambres = ref([]);
const showForm = ref(false);
const editingChambre = ref(null);
const loading = ref(false);
const error = ref("");
const successMessage = ref("");
const viewingChambre = ref(null);

// Filtres
const searchTerm = ref("");
const filterType = ref("");
const filterStatus = ref("");

// Pagination
const currentPage = ref(1);
const perPage = 3;

const user = JSON.parse(localStorage.getItem("user"));
const isProprietaire = computed(() => user?.role === 'proprietaire');
const isLocataire = computed(() => user?.role === 'locataire');

// Calcul des données filtrées
const filteredChambres = computed(() => {
    let filtered = [...chambres.value];

    // Recherche par texte
    if (searchTerm.value) {
        filtered = filtered.filter(
            (chambre) =>
                chambre.titre
                    .toLowerCase()
                    .includes(searchTerm.value.toLowerCase()) ||
                (chambre.description &&
                    chambre.description
                        .toLowerCase()
                        .includes(searchTerm.value.toLowerCase()))
        );
    }

    // Filtrer par type
    if (filterType.value) {
        filtered = filtered.filter((c) => c.type === filterType.value);
    }

    // Filtrer par statut
    if (filterStatus.value !== "") {
        filtered = filtered.filter(
            (c) => c.disponible.toString() === filterStatus.value
        );
    }

    return filtered;
});

// Pagination
const paginatedChambres = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    return filteredChambres.value.slice(start, start + perPage);
});

const totalPages = computed(() =>
    Math.ceil(filteredChambres.value.length / perPage)
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
const fetchChambres = async () => {
    try {
        loading.value = true;
        error.value = "";
        const { data } = await axios.get("/api/chambres");
        chambres.value = data;
    } catch (err) {
        error.value = "Erreur lors du chargement des chambres";
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const openForm = (chambre = null) => {
    editingChambre.value = chambre;
    showForm.value = true;
};

const closeForm = () => {
    showForm.value = false;
    editingChambre.value = null;
};

const handleCreated = () => {
    fetchChambres();
    closeForm();
    showSuccessMessage("Chambre créée avec succès");
};

const handleUpdated = () => {
    fetchChambres();
    closeForm();
    showSuccessMessage("Chambre modifiée avec succès");
};

const showSuccessMessage = (msg) => {
    successMessage.value = msg;
    setTimeout(() => (successMessage.value = ""), 3000);
};

const deleteChambre = async (id) => {
    if (confirm("Êtes-vous sûr de vouloir supprimer cette chambre ?")) {
        try {
            await axios.post(`/api/chambres/${id}`, { _method: "DELETE" });
            fetchChambres();
            showSuccessMessage("Chambre supprimée avec succès");
        } catch (err) {
            error.value = "Erreur lors de la suppression";
            console.error(err);
        }
    }
};

const viewChambre = (chambre) => {
    viewingChambre.value = chambre;
};

const editChambre = (chambre) => {
    openForm(chambre);
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

watch([searchTerm, filterType, filterStatus], () => {
    currentPage.value = 1;
});

onMounted(fetchChambres);
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
