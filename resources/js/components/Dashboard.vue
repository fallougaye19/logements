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
                    class="block px-3 py-2 rounded hover:bg-blue-700 transition"
                    active-class="bg-blue-600 font-semibold"
                >
                    {{ link.label }}
                </router-link>
            </nav>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col">
            <!-- Navbar -->
            <header class="bg-white shadow flex items-center justify-between px-6 h-16">
                <h1 class="text-xl font-semibold text-gray-800">Tableau de bord</h1>
                <div class="flex items-center space-x-4">
                    <router-link to="/profile" class="px-3 py-2 rounded hover:bg-blue-100">Profil</router-link>
                    <button @click="logout" class="px-3 py-1 border rounded hover:bg-gray-100">Se déconnecter</button>
                </div>
            </header>

            <!-- Contenu principal -->
            <main class="flex-1 p-6 overflow-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 w-full mb-8">
                    <StatCard label="Utilisateurs" :value="stats.utilisateurs" color="blue" />
                    <StatCard label="Maisons" :value="stats.maisons" color="green" />
                    <StatCard label="Chambres" :value="stats.chambres" color="yellow" />
                    <StatCard label="Contrats actifs" :value="stats.contrats_actifs" color="purple" />
                    <StatCard label="Paiements en retard" :value="stats.paiements_en_retard" color="red" />
                    <StatCard label="Rendez-vous à venir" :value="stats.rendez_vous_a_venir" color="pink" />
                    <StatCard label="Problèmes non résolus" :value="stats.problemes_non_resolus" color="indigo" />
                    <StatCard label="Locataires" :value="stats.locataires" color="teal" />
                </div>

                <!-- Vue dynamique (pour les autres pages) -->
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

const links = [
    { path: "/utilisateurs", label: "Utilisateurs" },
    { path: "/maisons", label: "Maisons" },
    { path: "/chambres", label: "Chambres" },
    { path: "/contrats", label: "Contrats" },
    { path: "/paiements", label: "Paiements" },
    { path: "/rendez-vous", label: "Rendez-vous" },
    { path: "/medias", label: "Médias" },
    { path: "/problemes", label: "Problèmes" }
];

// État pour les statistiques
const stats = ref({
    utilisateurs: 0,
    maisons: 0,
    chambres: 0,
    contrats_actifs: 0,
    rendez_vous_a_venir: 0,
    problemes_non_resolus: 0,
    locataires: 0,
    proprietaires: 0
});

// Méthode de déconnexion
const logout = () => {
    localStorage.removeItem("token");
    delete axios.defaults.headers.common["Authorization"];
    router.push("/login");
};

// Charger les stats
const fetchStats = async () => {
    try {
        const res = await axios.get("/api/stats");
        stats.value = res.data;
    } catch (e) {
        console.error("Erreur lors du chargement des statistiques", e);
    }
};

onMounted(fetchStats);
</script>
<style scoped>
.input {
    @apply w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500;
}

.btn {
    @apply w-full bg-blue-600 text-white py-2 rounded-md font-semibold hover:bg-blue-700 transition;
}
</style>
