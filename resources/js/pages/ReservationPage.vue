<template>
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Réservations en attente</h1>

        <div v-if="loading" class="flex justify-center py-8">
            <div class="animate-spin h-8 w-8 border-b-2 border-blue-600"></div>
        </div>

        <template v-else>
            <div
                v-if="reservations.length === 0"
                class="text-center py-12 text-gray-500"
            >
                <p>Aucune réservation en attente.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="r in reservations"
                    :key="r.id"
                    class="border rounded-lg p-4 bg-white shadow"
                >
                    <h3 class="font-bold">{{ r.chambre.titre }}</h3>
                    <p class="text-sm text-gray-600">
                        {{ r.chambre.maison.adresse }}
                    </p>
                    <p class="text-sm text-gray-500">
                        {{ formatPrix(r.chambre.prix) }}/mois
                    </p>

                    <div class="mt-4 p-3 bg-gray-50 rounded">
                        <p>
                            <strong>Locataire :</strong> {{ r.locataire.nom }}
                        </p>
                        <p><strong>Email :</strong> {{ r.locataire.email }}</p>
                        <p>
                            <strong>Téléphone :</strong>
                            {{ r.locataire.telephone || "Non renseigné" }}
                        </p>
                    </div>

                    <div class="flex gap-2 mt-4">
                        <button
                            @click="openModal(r)"
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded text-sm"
                        >
                            Voir & Accepter
                        </button>
                        <button
                            @click="refuser(r)"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 rounded text-sm"
                        >
                            Refuser
                        </button>
                    </div>
                </div>
            </div>
        </template>
        <!-- Modale -->
        <ReservationModal
            v-if="showModal"
            :show="showModal"
            :chambre="selectedReservation?.chambre"
            :locataire="selectedReservation?.locataire"
            @close="showModal = false"
            @refresh="loadReservations"
        />
    </div>
</template>

<script>
import axios from "axios";
import { ref, onMounted } from "vue";
import ReservationModal from "@/components/reservations/ReservationModal.vue";

const showToast = (message, type = "info") => {
    const msg =
        type === "error"
            ? `${message}`
            : type === "success"
            ? `${message}`
            : `${message}`;
    alert(msg);
};

export default {
    components: {
        ReservationModal,
    },
    setup() {
        const reservations = ref([]);
        const loading = ref(false);
        const showModal = ref(false);
        const selectedReservation = ref(null);

        const loadReservations = async () => {
            loading.value = true;
            try {
                const response = await axios.get("/api/reservations", {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem(
                            "token"
                        )}`,
                    },
                });
                if (response.data.success) {
                    reservations.value = response.data.data;
                } else {
                    showToast("Erreur", "error");
                }
            } catch (error) {
                showToast("Erreur de chargement", "error");
            } finally {
                loading.value = false;
            }
        };

        const openModal = (reservation) => {
            selectedReservation.value = reservation;
            showModal.value = true;
        };

        const refuser = async (reservation) => {
            if (!confirm("Refuser cette réservation ?")) return;
            try {
                const response = await axios.post(
                    `/api/chambres/${reservation.chambre.id}/refuser`,
                    {},
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                        },
                    }
                );
                if (response.data.success) {
                    showToast("Refusée.", "info");
                    loadReservations();
                }
            } catch (error) {
                showToast("Erreur", "error");
            }
        };

        onMounted(() => {
            loadReservations();
        });

        return {
            reservations,
            loading,
            refuser,
            showModal,
            selectedReservation,
            openModal,
            loadReservations,
            formatPrix(prix) {
                return new Intl.NumberFormat("fr-FR", {
                    style: "currency",
                    currency: "XOF",
                }).format(prix);
            },
        };
    },
};
</script>
