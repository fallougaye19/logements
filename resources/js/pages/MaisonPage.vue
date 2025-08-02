<template>
    <div class="container mx-auto p-6">
        <!-- En-tête -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Mes Maisons</h1>
            <button
                @click="showCreateModal = true"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2"
            >
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
                        d="M12 4v16m8-8H4"
                    ></path>
                </svg>
                Ajouter une maison
            </button>
        </div>

        <!-- Barre de recherche et filtres -->
        <div class="mb-6 flex gap-4">
            <div class="flex-1">
                <input
                    v-model="searchTerm"
                    @input="searchMaisons"
                    type="text"
                    placeholder="Rechercher par adresse ou description..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
            </div>
            <select
                v-model="filterStatus"
                @change="searchMaisons"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            >
                <option value="">Toutes les maisons</option>
                <option value="true">Maisons actives</option>
                <option value="false">Maisons inactives</option>
            </select>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="flex justify-center py-8">
            <div
                class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"
            ></div>
        </div>

        <!-- Liste des maisons ou message si aucune maison -->
        <template v-if="!loading">
            <div
                v-if="maisons.length > 0"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
            >
                <div
                    v-for="maison in maisons"
                    :key="maison.id"
                    class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
                >
                    <!-- Image de la maison -->
                    <div class="h-48 bg-gray-200 relative">
                        <img
                            v-if="maison.image_url"
                            :src="maison.image_url"
                            :alt="maison.adresse"
                            class="w-full h-full object-cover"
                        />
                        <div
                            v-else
                            class="w-full h-full flex items-center justify-center text-gray-500"
                        >
                            <svg
                                class="w-12 h-12"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"
                                ></path>
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8.5 13.5l2.5-3 2.5 3"
                                ></path>
                            </svg>
                        </div>

                        <!-- Badge de statut -->
                        <div class="absolute top-2 right-2">
                            <span
                                :class="
                                    maison.active
                                        ? 'bg-green-500'
                                        : 'bg-red-500'
                                "
                                class="px-2 py-1 text-xs text-white rounded-full"
                            >
                                {{ maison.active ? "Active" : "Inactive" }}
                            </span>
                        </div>
                    </div>

                    <!-- Contenu de la carte -->
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2 text-gray-800">
                            {{ maison.adresse }}
                        </h3>
                        <p
                            v-if="maison.description"
                            class="text-gray-600 text-sm mb-3 line-clamp-2"
                        >
                            {{ maison.description }}
                        </p>

                        <div
                            class="flex items-center gap-4 text-sm text-gray-500 mb-3"
                        >
                            <span class="flex items-center gap-1">
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
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                    ></path>
                                </svg>
                                {{ maison.nombre_chambres }} chambre(s)
                            </span>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-between items-center">
                            <!-- Actions gauche : Voir & Modifier -->
                            <div class="flex gap-3">
                                <!-- Voir détails -->
                                <button
                                    @click="viewMaison(maison)"
                                    class="flex items-center gap-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition-all duration-200 group"
                                    title="Voir les détails de la maison"
                                    aria-label="Voir les détails"
                                >
                                    <svg
                                        class="w-4 h-4 opacity-80 group-hover:scale-105"
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
                                    <span class="text-sm font-medium"
                                        >Voir</span
                                    >
                                </button>

                                <!-- Modifier -->
                                <button
                                    @click="editMaison(maison)"
                                    class="flex items-center gap-1.5 text-green-600 hover:text-green-800 hover:bg-green-50 px-3 py-1.5 rounded-lg transition-all duration-200 group"
                                    title="Modifier cette maison"
                                    aria-label="Modifier"
                                >
                                    <svg
                                        class="w-4 h-4 opacity-80 group-hover:rotate-12 transition-transform"
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
                                    <span class="text-sm font-medium"
                                        >Modifier</span
                                    >
                                </button>
                            </div>

                            <!-- Actions droite : Activer/Désactiver & Supprimer -->
                            <div class="flex gap-3">
                                <!-- Activer / Désactiver -->
                                <button
                                    @click="toggleStatus(maison)"
                                    :class="
                                        maison.active
                                            ? 'text-red-600 hover:text-red-800 hover:bg-red-50'
                                            : 'text-green-600 hover:text-green-800 hover:bg-green-50'
                                    "
                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition-all duration-200 group"
                                    :title="
                                        maison.active
                                            ? 'Désactiver la maison'
                                            : 'Activer la maison'
                                    "
                                    aria-label="Activer/Désactiver"
                                >
                                    <svg
                                        v-if="maison.active"
                                        class="w-4 h-4 opacity-80 group-hover:scale-110"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 18.364z"
                                        />
                                    </svg>
                                    <svg
                                        v-else
                                        class="w-4 h-4 opacity-80 group-hover:scale-110"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 13l4 4L19 7"
                                        />
                                    </svg>
                                    <span class="text-sm font-medium">
                                        {{
                                            maison.active
                                                ? "Désactiver"
                                                : "Activer"
                                        }}
                                    </span>
                                </button>

                                <!-- Supprimer -->
                                <button
                                    @click="deleteMaison(maison)"
                                    class="flex items-center gap-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-all duration-200 group"
                                    title="Supprimer cette maison"
                                    aria-label="Supprimer"
                                >
                                    <svg
                                        class="w-4 h-4 opacity-80 group-hover:scale-110"
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
                                    <span class="text-sm font-medium"
                                        >Supprimer</span
                                    >
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="text-center py-12">
                <svg
                    class="mx-auto h-12 w-12 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                    ></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">
                    Aucune maison trouvée
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    Commencez par ajouter une nouvelle maison.
                </p>
            </div>
        </template>

        <!-- Modal de création/modification -->
        <MaisonForm
            :show="showCreateModal || showEditModal"
            :maison="selectedMaison"
            :is-editing="showEditModal"
            :user-role="userRole"
            @close="closeModals"
            @saved="onMaisonSaved"
        />

        <!-- Modal de détails -->
        <MaisonDetails
            :show="showDetailsModal"
            :maison="selectedMaison"
            :user-role="userRole"
            @close="showDetailsModal = false"
        />
    </div>
</template>

<script>
import { ref, onMounted } from "vue";
import MaisonForm from "@/components/maisons/MaisonForm.vue";
import MaisonDetails from "@/components/maisons/MaisonDetails.vue";

// Service pour les appels API
const maisonService = {
    async getMaisons(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const response = await fetch(`/api/maisons?${queryString}`, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${localStorage.getItem("token")}`,
            },
        });
        return { data: await response.json() };
    },

    async toggleStatus(maisonId) {
        const response = await fetch(`/api/maisons/${maisonId}/toggle-status`, {
            method: "PATCH",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${localStorage.getItem("token")}`,
            },
        });
        return { data: await response.json() };
    },

    async deleteMaison(maisonId) {
        const response = await fetch(`/api/maisons/${maisonId}`, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${localStorage.getItem("token")}`,
            },
        });
        return { data: await response.json() };
    },
};

// Composable pour les notifications
const useToast = () => {
    const showToast = (message, type = "info") => {
        // Implémentation simple avec alert, remplacez par votre système de toast
        if (type === "error") {
            console.error(message);
            alert(`Erreur: ${message}`);
        } else if (type === "success") {
            console.log(message);
            alert(`Succès: ${message}`);
        } else {
            console.info(message);
            alert(message);
        }
    };

    return { showToast };
};

export default {
    name: "MaisonsIndex",
    components: {
        MaisonForm,
        MaisonDetails,
    },
    setup() {
        const { showToast } = useToast();

        // État réactif
        const maisons = ref([]);
        const loading = ref(false);
        const searchTerm = ref("");
        const filterStatus = ref("");

        // Modales
        const showCreateModal = ref(false);
        const showEditModal = ref(false);
        const showDetailsModal = ref(false);
        const selectedMaison = ref(null);

        const userRole = ref(localStorage.getItem("userRole") || "");

        // Méthodes
        const loadMaisons = async () => {
            loading.value = true;
            try {
                let response;
                if (userRole.value === "proprietaire") {
                    response = await maisonService.getMaisons({
                        proprietaire: true,
                    });
                } else if (userRole.value === "locataire") {
                    response = await maisonService.getMaisons({
                        disponibles: true,
                    });
                } else {
                    response = await maisonService.getMaisons();
                }
                if (response.data.success) {
                    maisons.value = response.data.data;
                }
            } catch (error) {
                console.error("Erreur:", error);
                showToast("Erreur lors du chargement des maisons", "error");
            } finally {
                loading.value = false;
            }
        };

        const searchMaisons = async () => {
            loading.value = true;
            try {
                const params = {};
                if (searchTerm.value) params.search = searchTerm.value;
                if (filterStatus.value !== "")
                    params.active = filterStatus.value;

                if (userRole.value === "proprietaire") {
                    params.proprietaire = true;
                } else if (userRole.value === "locataire") {
                    params.disponibles = true;
                }

                const response = await maisonService.getMaisons(params);
                if (response.data.success) {
                    maisons.value = response.data.data;
                }
            } catch (error) {
                console.error("Erreur:", error);
                showToast("Erreur lors de la recherche", "error");
            } finally {
                loading.value = false;
            }
        };

        const viewMaison = (maison) => {
            selectedMaison.value = maison;
            showDetailsModal.value = true;
        };

        const editMaison = (maison) => {
            selectedMaison.value = maison;
            showEditModal.value = true;
        };

        const toggleStatus = async (maison) => {
            try {
                const response = await maisonService.toggleStatus(maison.id);
                if (response.data.success) {
                    maison.active = !maison.active;
                    showToast(response.data.message, "success");
                }
            } catch (error) {
                console.error("Erreur:", error);
                showToast("Erreur lors du changement de statut", "error");
            }
        };

        const deleteMaison = async (maison) => {
            if (!confirm("Êtes-vous sûr de vouloir supprimer cette maison ?")) {
                return;
            }

            try {
                const response = await maisonService.deleteMaison(maison.id);
                if (response.data.success) {
                    maisons.value = maisons.value.filter(
                        (m) => m.id !== maison.id
                    );
                    showToast("Maison supprimée avec succès", "success");
                }
            } catch (error) {
                console.error("Erreur:", error);
                showToast("Erreur lors de la suppression", "error");
            }
        };

        const closeModals = () => {
            showCreateModal.value = false;
            showEditModal.value = false;
            selectedMaison.value = null;
        };

        const onMaisonSaved = (maison) => {
            if (showEditModal.value) {
                // Mise à jour
                const index = maisons.value.findIndex(
                    (m) => m.id === maison.id
                );
                if (index !== -1) {
                    maisons.value[index] = maison;
                }
            } else {
                // Création
                maisons.value.unshift(maison);
            }
            closeModals();
            showToast("Maison sauvegardée avec succès", "success");
        };

        // Lifecycle
        onMounted(() => {
            loadMaisons();
        });

        return {
            maisons,
            loading,
            searchTerm,
            filterStatus,
            showCreateModal,
            showEditModal,
            showDetailsModal,
            selectedMaison,
            userRole,
            loadMaisons,
            searchMaisons,
            viewMaison,
            editMaison,
            toggleStatus,
            deleteMaison,
            closeModals,
            onMaisonSaved,
        };
    },
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
