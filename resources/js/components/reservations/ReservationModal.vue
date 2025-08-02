<!-- resources/js/components/reservations/ReservationModal.vue -->

<template>
    <div
        v-if="show"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <h3 class="text-xl font-bold mb-4">Demande de réservation</h3>
            <p><strong>Chambre :</strong> {{ chambre.titre }}</p>
            <p><strong>Locataire :</strong> {{ locataire.nom }}</p>
            <p><strong>Email :</strong> {{ locataire.email }}</p>
            <p>
                <strong>Téléphone :</strong>
                {{ locataire.telephone || "Non renseigné" }}
            </p>

            <div class="mt-6 flex gap-3 justify-end">
                <button
                    @click="handleRefuse"
                    :disabled="loading"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded"
                >
                    {{ loading && action === "refuse" ? "..." : "Refuser" }}
                </button>
                <button
                    @click="handleAccept"
                    :disabled="loading"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded"
                >
                    {{ loading && action === "accept" ? "..." : "Accepter" }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "ReservationModal",
    props: {
        show: { type: Boolean, required: true },
        chambre: { type: Object, required: true },
        locataire: { type: Object, required: true },
    },
    emits: ["close", "refresh"],
    data() {
        return {
            loading: false,
            action: null, // 'accept' ou 'refuse'
        };
    },
    methods: {
        closeModal() {
            if (!this.loading) {
                this.$emit("close");
            }
        },
        async handleAccept() {
            //if (this.loading) return;
            this.loading = true;
            this.action = "accept";
            try {
                const response = await axios.post(
                    `/api/chambres/${this.chambre.id}/accepter`,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                        },
                    }
                );
                if (response.data.success) {
                    this.$emit("close");
                    this.$emit("refresh");
                    alert("Réservation acceptée ! Contrat généré.");
                } else {
                    const message =
                        response.data.message || "Échec de l’acceptation";
                    alert(message);
                }
            } catch (error) {
                let message = "Erreur réseau";
                if (error.response?.data?.message) {
                    message = error.response.data.message;
                } else if (error.response?.data?.error) {
                    message = error.response.data.error;
                }
                alert(message);
                console.error("Erreur acceptation:", error);
            } finally {
                this.loading = false;
                this.action = null;
            }
        },
        async handleRefuse() {
            this.loading = true;
            this.action = "refuse";
            try {
                const response = await axios.post(
                    `/api/chambres/${this.chambre.id}/refuser`,
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
                    this.$emit("close");
                    this.$emit("refresh");
                    alert("Réservation refusée.");
                } else {
                    const message = response.data.message || "Échec du refus";
                    alert(message);
                }
            } catch (error) {
                let message = "Erreur réseau";
                if (error.response?.data?.message) {
                    message = error.response.data.message;
                }
                alert(message);
                console.error("Erreur refus:", error);
            } finally {
                this.loading = false;
                this.action = null;
            }
        },
    },
};
</script>
