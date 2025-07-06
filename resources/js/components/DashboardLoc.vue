<template>
    <div class="flex min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white flex flex-col">
            <div class="p-6 text-xl font-bold border-b border-gray-700">
                Mon App
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2">
                <router-link
                    v-for="link in links"
                    :key="link.path"
                    :to="link.path"
                    class="block px-3 py-2 rounded hover:bg-blue-700 transition-colors duration-200"
                    active-class="bg-blue-600 font-semibold"
                >
                    {{ link.label }}
                </router-link>
            </nav>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col">
            <!-- Navbar -->
            <header
                class="bg-white shadow flex items-center justify-between px-6 h-16"
            >
                <h1 class="text-xl font-semibold text-gray-800">
                    Tableau de bord
                </h1>
                <div class="flex items-center space-x-4">
                    <router-link
                        to="/profile"
                        class="px-3 py-2 rounded hover:bg-blue-100 transition"
                    >
                        Profil
                    </router-link>
                    <button
                        @click="logout"
                        class="px-3 py-1 border rounded hover:bg-gray-100 transition"
                    >
                        Se déconnecter
                    </button>
                </div>
            </header>

            <!-- Contenu principal -->
            <main class="flex-1 p-6 overflow-auto">
                <!-- Statistiques du locataire -->
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8"
                >
                    <StatCard
                        label="Chambre louée"
                        :value="chambre ? 'Oui' : 'Non'"
                        color="blue"
                    />
                    <StatCard
                        label="Contrat actif"
                        :value="
                            contrat ? formatDate(contrat.date_fin) : 'Aucun'
                        "
                        color="green"
                    />
                    <StatCard
                        label="Problèmes signalés"
                        :value="problemes.length"
                        color="red"
                    />
                    <StatCard
                        label="Rendez-vous à venir"
                        :value="rendezVous.length"
                        color="indigo"
                    />
                </div>

                <!-- Vue dynamique -->
                <router-view />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import StatCard from "@/components/StatCard.vue";
import router from "@/router";

// Liens propres aux locataires
const links = [
    { path: "/chambres", label: "Chambres" },
    { path: "/contrats", label: "Contrats" },
    { path: "/problemes", label: "Problèmes" },
    { path: "/rendez-vous", label: "Rendez-vous" },
    { path: "/paiements", label: "Paiements"}
];

// États
const user = JSON.parse(localStorage.getItem("user"));
const contrat = ref(null);
const chambre = ref(null);
const problemes = ref([]);
const rendezVous = ref([]);

// Méthode de déconnexion
const logout = () => {
    localStorage.removeItem("token");
    delete axios.defaults.headers.common["Authorization"];
    router.push("/login");
};

// Charger les données du locataire
const fetchDashboardData = async () => {
    try {
        const [contratRes, problemeRes, rdvRes] = await Promise.all([
            axios.get(`/api/contrats?locataire_id=${user.id}`),
            axios.get(`/api/problemes?signale_par=${user.id}`),
            axios.get(`/api/rendez-vous?locataire_id=${user.id}`),
        ]);

        // Contrat actif
        contrat.value = contratRes.data.find((c) => c.statut === "actif");

        // Chambre louée (si contrat actif)
        if (contrat.value?.chambre_id) {
            const chambreRes = await axios.get(
                `/api/chambres/${contrat.value.chambre_id}`
            );
            chambre.value = chambreRes.data;
        }

        // Problèmes
        problemes.value = problemeRes.data;

        // Rendez-vous
        rendezVous.value = rdvRes.data;
    } catch (e) {
        console.error("Erreur lors du chargement des données", e);
    }
};

onMounted(fetchDashboardData);

// Fonction utilitaire
const formatDate = (date) => {
    return new Date(date).toLocaleDateString("fr-FR");
};
</script>

<style scoped>
.input {
    @apply w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500;
}

.btn {
    @apply bg-blue-600 text-white py-2 rounded-md font-semibold hover:bg-blue-700 transition;
}
</style>
