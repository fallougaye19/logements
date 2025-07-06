<template>
    <div class="p-4">
        <!-- Titre -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                Liste des locataires
            </h2>
            <div class="flex gap-2">
                <button @click="fetchUtilisateurs" class="btn-secondary">
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
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                        />
                    </svg>
                    Actualiser
                </button>
                <button @click="showForm = true" class="btn">
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
                    Nouveau locataire
                </button>
            </div>
        </div>

        <!-- Filtres -->
        <div class="mb-6 flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input
                    v-model="searchTerm"
                    placeholder="Rechercher un locataire..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
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

        <!-- Liste des locataires -->
        <div
            v-if="!loading && filteredLocataires.length"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
        >
            <div
                v-for="user in paginatedLocataires"
                :key="user.id"
                class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
            >
                <div class="p-4">
                    <h3 class="text-lg font-semibold truncate">
                        {{ user.nom }}
                    </h3>
                    <p class="text-sm text-gray-600 line-clamp-2">
                        {{ user.email }}
                    </p>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span
                            class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs"
                            >Téléphone :
                            {{ user.telephone || "Non renseigné" }}</span
                        >
                        <span
                            class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs"
                            >Rôle : {{ user.role || "Aucun" }}</span
                        >
                    </div>

                    <!-- Bouton voir détails -->
                    <button
                        @click="viewUtilisateur(user)"
                        class="btn-icon text-blue-600 mt-4"
                    >
                        Voir les détails
                    </button>
                </div>
            </div>
        </div>

        <!-- Aucun résultat -->
        <div
            v-if="!loading && !filteredLocataires.length"
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
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0z"
                />
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 11v6m0 0l-2-2m2 2l2-2M9 11H7a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-8a2 2 0 00-2-2H9z"
                />
            </svg>
            <p class="text-gray-600">Aucun locataire trouvé</p>
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

        <!-- Modal Visualisation -->
        <div
            v-if="viewingUtilisateur"
            class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
        >
            <div
                class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
            >
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">
                            Détails du locataire
                        </h3>
                        <button
                            @click="viewingUtilisateur = null"
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
                                >Nom</label
                            >
                            <p>{{ viewingUtilisateur.nom }}</p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Email</label
                            >
                            <p>{{ viewingUtilisateur.email }}</p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Téléphone</label
                            >
                            <p>
                                {{
                                    viewingUtilisateur.telephone ||
                                    "Non renseigné"
                                }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >CNI</label
                            >
                            <p>
                                {{ viewingUtilisateur.cni || "Non renseigné" }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-500"
                                >Date d'inscription</label
                            >
                            <p>
                                {{ formatDate(viewingUtilisateur.created_at) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
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
                            Nouveau locataire
                        </h3>
                        <button
                            @click="showForm = false"
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
                    <UtilisateurForm @created="onUserCreated" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from "vue";
import axios from "axios";
import UtilisateurForm from "@/components/UtilisateurForm.vue";

const utilisateurs = ref([]);
const loading = ref(false);
const error = ref("");
const viewingUtilisateur = ref(null);
const showForm = ref(false);

// Filtres
const searchTerm = ref("");

// Pagination
const currentPage = ref(1);
const perPage = 10;

// 🔥 Filtre par rôle = "locataire"
const locataires = computed(() => {
    //return utilisateurs.value.filter((u) => u.role === "locataire");
    return Array.isArray(utilisateurs.value)
        ? utilisateurs.value.filter((u) => u.role === "locataire")
        : [];
});

// 🔍 Recherche par nom ou email
const filteredLocataires = computed(() => {
    const term = searchTerm.value.toLowerCase();
    if (!term) return locataires.value;
    // return locataires.value.filter(
    //     (u) =>
    //         u.nom.toLowerCase().includes(term) ||
    //         u.email.toLowerCase().includes(term)
    // );
    return Array.isArray(locataires.value)
        ? locataires.value.filter(
            (u) =>
                u.nom?.toLowerCase().includes(term) ||
                u.email?.toLowerCase().includes(term)
        )
        : [];
});

const paginatedLocataires = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    return filteredLocataires.value.slice(start, start + perPage);
});

const totalPages = computed(() =>
    Math.ceil(filteredLocataires.value.length / perPage)
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
const fetchUtilisateurs = async () => {
    try {
        loading.value = true;
        error.value = "";
        const res = await axios.get("/api/utilisateurs");
        utilisateurs.value = res.data;
    } catch (err) {
        error.value = "Erreur lors du chargement des utilisateurs";
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const viewUtilisateur = (user) => {
    viewingUtilisateur.value = user;
};

const onUserCreated = () => {
    showForm.value = false;
    fetchUtilisateurs();
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("fr-FR");
};

watch([searchTerm], () => {
    currentPage.value = 1;
});

// Recharger les données quand la page devient visible
const handleVisibilityChange = () => {
    if (!document.hidden) {
        fetchUtilisateurs();
    }
};

onMounted(() => {
    fetchUtilisateurs();
    document.addEventListener('visibilitychange', handleVisibilityChange);
});

// Nettoyer l'écouteur d'événement
onUnmounted(() => {
    document.removeEventListener('visibilitychange', handleVisibilityChange);
});
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
