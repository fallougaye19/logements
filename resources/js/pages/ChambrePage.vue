<template>
    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Mes Chambres</h1>
            <button
                v-if="userRole === 'proprietaire'"
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
                Ajouter une chambre
            </button>
        </div>

        <div class="mb-6 flex gap-4">
            <div class="flex-1">
                <input
                    v-model="searchTerm"
                    @input="searchChambres"
                    type="text"
                    placeholder="Rechercher par titre ou description..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
            </div>
            <select
                v-model="filterDisponibilite"
                @change="searchChambres"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            >
                <option value="">Toutes les chambres</option>
                <option value="true">Disponibles</option>
                <option value="false">Non disponibles</option>
            </select>
        </div>

        <div v-if="loading" class="flex justify-center py-8">
            <div
                class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"
            ></div>
        </div>

        <template v-if="!loading">
            <div
                v-if="filteredChambres.length > 0"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
            >
                <ChambreCard
                    v-for="chambre in filteredChambres"
                    :key="chambre.id"
                    :chambre="chambre"
                    :user-role="userRole"
                    @view-details="viewChambre"
                    @edit="editChambre"
                    @toggle-disponibilite="toggleDisponibilite"
                    @delete="deleteChambre"
                    @reserve="reserveChambre"
                />
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
                    Aucune chambre trouvée
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    {{
                        userRole === "proprietaire"
                            ? "Commencez par ajouter une nouvelle chambre."
                            : "Aucune chambre disponible pour le moment."
                    }}
                </p>
            </div>
        </template>

        <ChambreForm
            :show="showCreateModal || showEditModal"
            :chambre="selectedChambre"
            :is-editing="showEditModal"
            @close="closeModals"
            @saved="onChambreSaved"
        />

        <ChambreDetails
            :show="showDetailsModal"
            :chambre="selectedChambre"
            :user-role="userRole"
            @close="showDetailsModal = false"
            @reserve="handleReserve"
        />
    </div>
</template>

<script>
import { ref, onMounted, computed } from "vue";
import ChambreForm from "@/components/chambres/ChambreForm.vue";
import ChambreDetails from "@/components/chambres/ChambreDetails.vue";
import ChambreCard from "@/components/chambres/ChambreCard.vue";

const chambreService = {
    async getChambresDisponibles(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const response = await fetch(`/api/chambres/disponibles?${queryString}`, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`
            }
        });
        return { data: await response.json() };
    },

    async getChambres(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const response = await fetch(`/api/chambres?${queryString}`, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${localStorage.getItem("token")}`,
            },
        });
        return { data: await response.json() };
    },

    async getMesChambres(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const response = await fetch(`/api/chambres/mes?${queryString}`, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${localStorage.getItem("token")}`,
            },
        });
        return { data: await response.json() };
    },

    async toggleDisponibilite(chambreId) {
        const response = await fetch(
            `/api/chambres/${chambreId}/toggle-disponibilite`,
            {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                },
            }
        );
        return { data: await response.json() };
    },

    async deleteChambre(chambreId) {
        const response = await fetch(`/api/chambres/${chambreId}`, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${localStorage.getItem("token")}`,
            },
        });
        return { data: await response.json() };
    },
};

const useToast = () => {
    const showToast = (message, type = "info") => {
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
    name: "ChambresIndex",
    components: {
        ChambreForm,
        ChambreDetails,
        ChambreCard,
    },
    setup() {
        const { showToast } = useToast();

        const chambres = ref([]);
        const loading = ref(false);
        const searchTerm = ref("");
        const filterDisponibilite = ref("");

        const showCreateModal = ref(false);
        const showEditModal = ref(false);
        const showDetailsModal = ref(false);
        const selectedChambre = ref(null);

        const userRole = ref(localStorage.getItem("userRole") || "");

        // Fonction pour charger les chambres selon le rôle
        const loadChambres = async () => {
            loading.value = true;
            try {
                let response;
                if (userRole.value === "proprietaire") {
                    response = await chambreService.getMesChambres();
                } else if (userRole.value === "locataire") {
                    // CORRECTION: Utiliser la méthode spécifique pour les chambres disponibles
                    response = await chambreService.getChambresDisponibles();
                } else {
                    // Admin ou autres rôles
                    response = await chambreService.getChambres();
                }

                if (response.data.success) {
                    chambres.value = response.data.data;
                } else {
                    console.error("Erreur API:", response.data.message);
                    showToast(
                        response.data.message || "Erreur lors du chargement",
                        "error"
                    );
                }
            } catch (error) {
                console.error("Erreur:", error);
                showToast("Erreur lors du chargement des chambres", "error");
            } finally {
                loading.value = false;
            }
        };

        const searchChambres = async () => {
            loading.value = true;
            try {
                const params = {};
                if (searchTerm.value) params.search = searchTerm.value;
                if (filterDisponibilite.value !== "")
                    params.disponible = filterDisponibilite.value;

                let response;
                if (userRole.value === "locataire") {
                    // CORRECTION: Utiliser la méthode spécifique pour les locataires
                    response = await chambreService.getChambresDisponibles(
                        params
                    );
                } else {
                    response = await chambreService.getChambres(params);
                }

                if (response.data.success) {
                    chambres.value = response.data.data;
                } else {
                    console.error("Erreur API:", response.data.message);
                    showToast(
                        response.data.message || "Erreur lors de la recherche",
                        "error"
                    );
                }
            } catch (error) {
                console.error("Erreur:", error);
                showToast("Erreur lors de la recherche", "error");
            } finally {
                loading.value = false;
            }
        };

        const viewChambre = (chambre) => {
            selectedChambre.value = chambre;
            showDetailsModal.value = true;
        };

        const editChambre = (chambre) => {
            selectedChambre.value = chambre;
            showEditModal.value = true;
        };

        const toggleDisponibilite = async (chambre) => {
            try {
                const response = await chambreService.toggleDisponibilite(
                    chambre.id
                );
                if (response.data.success) {
                    // Mise à jour locale
                    const index = chambres.value.findIndex(
                        (c) => c.id === chambre.id
                    );
                    if (index !== -1) {
                        chambres.value[index].disponible =
                            !chambres.value[index].disponible;
                    }
                    showToast(response.data.message, "success");
                }
            } catch (error) {
                console.error("Erreur:", error);
                showToast("Erreur lors du changement de statut", "error");
            }
        };

        const deleteChambre = async (chambre) => {
            if (
                !confirm("Êtes-vous sûr de vouloir supprimer cette chambre ?")
            ) {
                return;
            }

            try {
                const response = await chambreService.deleteChambre(chambre.id);
                if (response.data.success) {
                    chambres.value = chambres.value.filter(
                        (c) => c.id !== chambre.id
                    );
                    showToast("Chambre supprimée avec succès", "success");
                }
            } catch (error) {
                console.error("Erreur:", error);
                showToast("Erreur lors de la suppression", "error");
            }
        };

        const closeModals = () => {
            showCreateModal.value = false;
            showEditModal.value = false;
            selectedChambre.value = null;
        };

        const onChambreSaved = (chambre) => {
            if (showEditModal.value) {
                const index = chambres.value.findIndex(
                    (c) => c.id === chambre.id
                );
                if (index !== -1) {
                    chambres.value[index] = chambre;
                }
            } else {
                chambres.value.unshift(chambre);
            }
            closeModals();
            showToast("Chambre sauvegardée avec succès", "success");
        };

        const formatPrix = (prix) => {
            return new Intl.NumberFormat("fr-FR", {
                style: "currency",
                currency: "EUR",
            }).format(prix);
        };

        // Réserver une chambre
        const reserveChambre = async (chambre) => {
            try {
                const response = await fetch(
                    `/api/chambres/${chambre.id}/reserve`,
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                        },
                    }
                );

                const data = await response.json();

                if (data.success) {
                    showToast("Chambre réservée avec succès!", "success");
                    // Recharger les chambres
                    loadChambres();
                } else {
                    showToast(
                        data.message || "Erreur lors de la réservation",
                        "error"
                    );
                }
            } catch (error) {
                console.error("Erreur:", error);
                showToast("Erreur lors de la réservation", "error");
            }
        };

        // Filtrer les chambres selon le rôle
        const filteredChambres = computed(() => {
            if (userRole.value === "locataire") {
                return chambres.value.filter((c) => c.disponible);
            }
            return chambres.value;
        });

        const accepterReservation = async (chambre) => {
            try {
                const response = await fetch(
                    `/api/chambres/${chambre.id}/accepter`,
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                        },
                    }
                );
                const data = await response.json();
                if (data.success) {
                    showToast(
                        "Réservation acceptée ! Contrat généré.",
                        "success"
                    );
                    loadChambres(); // Recharger
                }
            } catch (error) {
                showToast("Erreur", "error");
            }
        };

        const refuserReservation = async (chambre) => {
            try {
                const response = await fetch(
                    `/api/chambres/${chambre.id}/refuser`,
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                        },
                    }
                );
                const data = await response.json();
                if (data.success) {
                    showToast("Réservation refusée.", "info");
                    loadChambres();
                }
            } catch (error) {
                showToast("Erreur", "error");
            }
        };

        onMounted(() => {
            loadChambres();
        });

        return {
            chambres,
            loading,
            searchTerm,
            filterDisponibilite,
            showCreateModal,
            showEditModal,
            showDetailsModal,
            selectedChambre,
            userRole,
            loadChambres,
            searchChambres,
            viewChambre,
            editChambre,
            toggleDisponibilite,
            deleteChambre,
            closeModals,
            onChambreSaved,
            formatPrix,
            filteredChambres,
            reserveChambre,
            accepterReservation,
            refuserReservation,

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
