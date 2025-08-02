<template>
    <div class="p-8 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto">
            <h1 class="text-3xl font-bold mb-6">Mes Rendez-vous</h1>

            <div class="flex flex-wrap gap-4 mb-6">
                <select
                    v-model="selectedStatut"
                    class="px-4 py-2 rounded-xl border border-gray-300"
                >
                    <option value="">Tous les statuts</option>
                    <option value="en_attente">En attente</option>
                    <option value="confirmé">Confirmé</option>
                    <option value="annulé">Annulé</option>
                </select>
                <input
                    type="date"
                    v-model="selectedDate"
                    class="px-4 py-2 rounded-xl border border-gray-300"
                />
            </div>

            <div v-if="loading" class="text-center py-10 text-gray-600">
                Chargement...
            </div>

            <div v-else class="grid gap-6">
                <div
                    v-for="rdv in filteredRendezVous"
                    :key="rdv.id"
                    class="p-6 bg-white rounded-2xl shadow border-l-4"
                    :class="{
                        'border-yellow-400': rdv.statut === 'en_attente',
                        'border-green-500': rdv.statut === 'confirmé',
                        'border-red-500': rdv.statut === 'annulé',
                    }"
                >
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">
                                Chambre: {{ rdv.chambre?.titre || "N/A" }}
                            </h2>
                            <p class="text-sm text-gray-500">
                                Date : {{ formatDate(rdv.date_heure) }}
                            </p>
                            <p class="text-sm mt-1">
                                Statut :
                                <span class="font-semibold capitalize">{{
                                    rdv.statut
                                }}</span>
                            </p>
                        </div>

                        <div
                            v-if="
                                user.role === 'proprietaire' &&
                                rdv.statut === 'en_attente'
                            "
                            class="flex gap-2"
                        >
                            <button
                                @click="changerStatut(rdv.id, 'confirmé')"
                                class="px-3 py-1 bg-green-600 text-white rounded-xl"
                            >
                                Confirmer
                            </button>
                            <button
                                @click="changerStatut(rdv.id, 'annulé')"
                                class="px-3 py-1 bg-red-600 text-white rounded-xl"
                            >
                                Annuler
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, computed } from "vue";
import axios from "axios";

const rendezVous = ref([]);
const loading = ref(true);
const selectedStatut = ref("");
const selectedDate = ref("");
const user = JSON.parse(localStorage.getItem("user") || "{}");

const api = axios.create({
    baseURL: "http://localhost:8000/api",
    headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
    },
});

const fetchRendezVous = async () => {
    loading.value = true;
    try {
        const res = await api.get("/rendez-vous");
        rendezVous.value = res.data.data;
    } catch (e) {
        console.error("Erreur lors du chargement", e);
    } finally {
        loading.value = false;
    }
};

const changerStatut = async (id, statut) => {
    try {
        await api.patch(`/rendez-vous/${id}/statut`, { statut });
        fetchRendezVous();
    } catch (e) {
        console.error("Erreur de mise à jour du statut", e);
    }
};

const formatDate = (date) => new Date(date).toLocaleString();

const filteredRendezVous = computed(() => {
    return rendezVous.value.filter((rdv) => {
        const matchStatut =
            !selectedStatut.value || rdv.statut === selectedStatut.value;
        const matchDate =
            !selectedDate.value ||
            rdv.date_heure.startsWith(selectedDate.value);
        return matchStatut && matchDate;
    });
});

onMounted(fetchRendezVous);
</script>

<style scoped>
select,
input[type="date"] {
    min-width: 200px;
}
</style>
