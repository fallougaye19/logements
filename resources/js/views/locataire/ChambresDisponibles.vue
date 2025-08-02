<template>
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">
            Chambres Disponibles
        </h1>

        <!-- Barre de recherche -->
        <div class="mb-6">
            <div class="relative">
                <input
                    v-model="searchTerm"
                    @input="searchChambres"
                    type="text"
                    placeholder="Rechercher par adresse, titre ou description..."
                    class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
                <svg
                    class="absolute left-4 top-3.5 w-5 h-5 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                    ></path>
                </svg>
            </div>
        </div>

        <!-- Filtres supplémentaires -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"
                    >Prix max</label
                >
                <input
                    v-model="maxPrice"
                    type="number"
                    min="0"
                    @change="searchChambres"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                    placeholder="Prix maximum"
                />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"
                    >Taille min (m²)</label
                >
                <input
                    v-model="minSize"
                    type="number"
                    min="0"
                    @change="searchChambres"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                    placeholder="Taille minimum"
                />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"
                    >Type de chambre</label
                >
                <select
                    v-model="selectedType"
                    @change="searchChambres"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                >
                    <option value="">Tous types</option>
                    <option value="Chambre simple">Chambre simple</option>
                    <option value="Chambre double">Chambre double</option>
                    <option value="Studio">Studio</option>
                    <option value="Appartement">Appartement</option>
                </select>
            </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="flex justify-center py-12">
            <div
                class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"
            ></div>
        </div>

        <!-- Résultats -->
        <template v-if="!loading">
            <div v-if="chambres.length > 0">
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <ChambreCard
                        v-for="chambre in chambres"
                        :key="chambre.id"
                        :chambre="chambre"
                        @view-details="viewDetails"
                        @contact="onContact"
                    />
                </div>
            </div>
            <div v-else class="text-center py-12 bg-gray-50 rounded-lg">
                <svg
                    class="mx-auto h-16 w-16 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    ></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">
                    Aucune chambre disponible
                </h3>
                <p class="mt-2 text-sm text-gray-500">
                    Essayez de modifier vos critères de recherche.
                </p>
            </div>
        </template>

        <!-- Détails de la chambre -->
        <ChambreDetailsModal
            :show="showDetailsModal"
            :chambre="selectedChambre"
            @close="showDetailsModal = false"
            @contact="onContact"
        />
    </div>
</template>

<script>
import { ref, onMounted } from "vue";
import ChambreCard from "@/components/chambres/ChambreCard.vue";
import ChambreDetailsModal from "@/components/chambres/ChambreDetailsModal.vue";

const chambreService = {
    async getChambresDisponibles(params = {}) {
        const query = Object.entries(params)
            .filter(([_, value]) => value !== "")
            .map(([key, value]) => `${key}=${encodeURIComponent(value)}`)
            .join("&");

        const response = await fetch(`/api/chambres/disponibles?${query}`, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${localStorage.getItem("token")}`,
            },
        });
        return { data: await response.json() };
    },
};

export default {
    name: "ChambresDisponiblesPage",
    components: {
        ChambreCard,
        ChambreDetailsModal,
    },
    setup() {
        const chambres = ref([]);
        const loading = ref(true);
        const searchTerm = ref("");
        const maxPrice = ref("");
        const minSize = ref("");
        const selectedType = ref("");
        const showDetailsModal = ref(false);
        const selectedChambre = ref(null);

        const loadChambres = async () => {
            loading.value = true;
            try {
                const params = {
                    search: searchTerm.value,
                    max_price: maxPrice.value,
                    min_size: minSize.value,
                    type: selectedType.value,
                };

                const response = await chambreService.getChambresDisponibles(
                    params
                );
                if (response.data.success) {
                    chambres.value = response.data.data;
                }
            } catch (error) {
                console.error("Erreur lors du chargement des chambres", error);
            } finally {
                loading.value = false;
            }
        };

        const searchChambres = () => {
            loadChambres();
        };

        const viewDetails = (chambre) => {
            selectedChambre.value = chambre;
            showDetailsModal.value = true;
        };

        const onContact = (chambre) => {
            console.log("Contacter propriétaire pour la chambre:", chambre.id);
            // Rediriger vers la page de contact
            // ou ouvrir un modal de contact
        };

        onMounted(() => {
            loadChambres();
        });

        return {
            chambres,
            loading,
            searchTerm,
            maxPrice,
            minSize,
            selectedType,
            showDetailsModal,
            selectedChambre,
            searchChambres,
            viewDetails,
            onContact,
        };
    },
};
</script>
