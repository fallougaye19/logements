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
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
        >
            <div
                v-for="user in paginatedLocataires"
                :key="user.id"
                class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl hover:scale-105 transition-all duration-300 transform"
            >
                <!-- Header coloré par rôle -->
                <div
                    class="h-2 bg-gradient-to-r from-blue-500 to-indigo-600"
                ></div>

                <div class="p-6">
                    <!-- Avatar + Nom -->
                    <div class="flex items-center gap-4 mb-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md"
                        >
                            {{ user.nom.charAt(0).toUpperCase() }}
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">
                                {{ user.nom }}
                            </h3>
                            <p class="text-sm text-gray-500">
                                {{ user.role?.toUpperCase() }}
                            </p>
                        </div>
                    </div>

                    <!-- Email -->
                    <div
                        class="flex items-center gap-2 text-sm text-gray-600 mb-3"
                    >
                        <svg
                            class="w-4 h-4 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                            />
                        </svg>
                        {{ user.email }}
                    </div>

                    <!-- Téléphone -->
                    <div
                        v-if="user.telephone"
                        class="flex items-center gap-2 text-sm text-gray-600 mb-4"
                    >
                        <svg
                            class="w-4 h-4 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                            />
                        </svg>
                        {{ user.telephone }}
                    </div>

                    <!-- Badge : Statut ou rôle -->
                    <div
                        class="flex justify-between items-center mt-4 pt-3 border-t border-gray-100"
                    >
                        <span
                            class="text-xs font-medium px-3 py-1 rounded-full bg-blue-100 text-blue-800"
                        >
                            🏠
                            {{
                                user.chambre_numero
                                    ? `Chambre ${user.chambre_numero}`
                                    : "Non attribuée"
                            }}
                        </span>
                        <button
                            @click="viewUtilisateur(user)"
                            class="text-indigo-600 hover:text-indigo-800 font-medium text-sm flex items-center gap-1"
                        >
                            Détails
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
                                    d="M9 5l7 7-7 7"
                                />
                            </svg>
                        </button>
                    </div>
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
                class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all scale-100 opacity-100 duration-300"
            >
                <!-- Header avec couleur et avatar -->
                <div
                    class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-white rounded-t-2xl"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <!-- Avatar avec initiale -->
                            <div
                                class="w-12 h-12 bg-white/30 backdrop-blur-sm rounded-full flex items-center justify-center text-xl font-bold"
                            >
                                {{
                                    viewingUtilisateur.nom
                                        .charAt(0)
                                        .toUpperCase()
                                }}
                            </div>
                            <div>
                                <h3 class="text-xl font-bold">
                                    {{ viewingUtilisateur.nom }}
                                </h3>
                                <p class="text-blue-100">Locataire</p>
                            </div>
                        </div>
                        <button
                            @click="viewingUtilisateur = null"
                            class="text-white/80 hover:text-white transition-colors"
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
                </div>

                <!-- Corps de la modale -->
                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Email -->
                        <div class="flex items-start gap-3">
                            <div class="mt-1 text-gray-400">
                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-500"
                                    >Email</label
                                >
                                <p class="text-gray-900 break-all">
                                    {{ viewingUtilisateur.email }}
                                </p>
                            </div>
                        </div>

                        <!-- Téléphone -->
                        <div class="flex items-start gap-3">
                            <div class="mt-1 text-gray-400">
                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-500"
                                    >Téléphone</label
                                >
                                <p class="text-gray-900">
                                    {{
                                        viewingUtilisateur.telephone ||
                                        "Non renseigné"
                                    }}
                                </p>
                            </div>
                        </div>

                        <!-- CNI -->
                        <div class="flex items-start gap-3">
                            <div class="mt-1 text-gray-400">
                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0120 9v12a2 2 0 01-2 2z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-500"
                                    >CNI</label
                                >
                                <p class="text-gray-900">
                                    {{
                                        viewingUtilisateur.cni ||
                                        "Non renseigné"
                                    }}
                                </p>
                            </div>
                        </div>

                        <!-- Date d'inscription -->
                        <div class="flex items-start gap-3">
                            <div class="mt-1 text-gray-400">
                                <svg
                                    class="w-5 h-5"
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
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-500"
                                    >Inscrit le</label
                                >
                                <p class="text-gray-900">
                                    {{
                                        formatDate(
                                            viewingUtilisateur.created_at
                                        )
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Section complémentaire (optionnelle) -->
                    <div
                        v-if="viewingUtilisateur.chambre_numero"
                        class="mt-4 p-4 bg-blue-50 rounded-xl border border-blue-100"
                    >
                        <div class="flex items-center gap-2 text-blue-800">
                            <svg
                                class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 21v-4a2 2 0 012-2h4a2 2 0 012 2v4"
                                />
                            </svg>
                            <span class="font-medium"
                                >Chambre attribuée :
                                {{ viewingUtilisateur.chambre_numero }}</span
                            >
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
                        <h3 class="text-lg font-semibold">Nouveau locataire</h3>
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
import UtilisateurForm from "@/components/utilisateurs/UtilisateurForm.vue";

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
    document.addEventListener("visibilitychange", handleVisibilityChange);
});

// Nettoyer l'écouteur d'événement
onUnmounted(() => {
    document.removeEventListener("visibilitychange", handleVisibilityChange);
});
</script>

<style scoped>
/* Amélioration des boutons */
.btn {
    @apply bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700 transition-all duration-200 flex items-center shadow-sm hover:shadow-md transform hover:-translate-y-0.5;
}
.btn-secondary {
    @apply bg-gray-200 text-gray-700 px-4 py-2 rounded-xl hover:bg-gray-300 transition-all duration-200 shadow-sm hover:shadow hover:translate-y-0.5;
}
.btn-icon {
    @apply p-2 rounded-full hover:bg-gray-100 transition-all duration-200 hover:scale-110;
}

/* Cartes de locataires avec effet de profondeur */
.bg-white.rounded-lg.shadow-md {
    @apply rounded-xl shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1;
}

/* Animation d'entrée des cartes */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.grid.grid-cols-1\.md\:grid-cols-2\.lg\:grid-cols-3 > div {
    animation: fadeInUp 0.4s ease-out;
}

@keyframes modalIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
.animate-modal {
    animation: modalIn 0.3s ease-out;
}
</style>
