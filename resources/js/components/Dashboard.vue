<template>
    <div class="flex min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 text-gray-800">
        <!-- Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-gray-600 to-gray-700 shadow-xl flex flex-col">
            <div class="px-6 py-6 text-2xl font-bold text-white border-b border-blue-500/30 backdrop-blur-sm">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                        🏠
                    </div>
                    <span class="bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent">
                        RésidencePlus
                    </span>
                </div>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2">
                <router-link
                    v-for="link in links"
                    :key="link.path"
                    :to="link.path"
                    class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-all duration-200 hover:bg-white/10 hover:translate-x-1 text-blue-100 hover:text-white group"
                    active-class="bg-white/20 text-white font-semibold shadow-lg"
                >
                    <component :is="link.icon" class="w-5 h-5 group-hover:scale-110 transition-transform" />
                    {{ link.label }}
                </router-link>
            </nav>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col">
            <!-- Topbar -->
            <header class="sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-gray-200/60 px-6 h-16 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-4">
                    <h1 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Tableau de bord
                    </h1>
                </div>

                <!-- Profile dropdown -->
                <div class="relative" v-click-outside="closeProfileMenu">
                    <button
                        @click="toggleProfileMenu"
                        class="flex items-center gap-2 p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                    >
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            👤
                        </div>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div
                        v-if="showProfileMenu"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-20"
                    >
                        <router-link to="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            👤 Mon Profil
                        </router-link>
                        <hr class="my-1 border-gray-200" />
                        <button @click="logout" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            ⛔ Déconnexion
                        </button>
                    </div>
                </div>
            </header>

            <!-- Dashboard content -->
            <main class="flex-1 p-6 overflow-y-auto">
                <!-- Loading -->
                <div v-if="loading" class="flex items-center justify-center h-64">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                </div>

                <!-- Error -->
                <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center gap-2 text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M4.93 19h14.14a2 2 0 001.72-2.74L13.72 4a2 2 0 00-3.44 0L3.2 16.26A2 2 0 004.93 19z" />
                        </svg>
                        <span class="font-medium">Erreur</span>
                    </div>
                    <p class="text-red-600 mt-1">{{ error }}</p>
                    <button @click="fetchData" class="mt-3 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Réessayer
                    </button>
                </div>

                <!-- Content -->
                <div v-else>
                    <!-- Welcome -->
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-6 mb-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-2xl font-bold mb-2">
                                    Bonjour {{ user.nom }} ! 👋
                                </h2>
                                <p class="text-green-100">Voici vos revenus locatifs</p>
                            </div>
                            <div class="hidden md:block text-right">
                                <p class="text-sm text-green-100">{{ currentDate }}</p>
                                <p class="text-xs text-green-200">{{ currentTime }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick actions -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <button
                                @click="goTo('/paiements')"
                                class="flex items-center gap-3 p-4 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors group"
                            >
                                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center group-hover:scale-110">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m0 0v4a2 2 0 002 2h6a2 2 0 002-2v-4m-6-4h6m-6 4h6" />
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <p class="font-medium text-gray-900">Voir les paiements</p>
                                    <p class="text-sm text-gray-600">Tous les loyers</p>
                                </div>
                            </button>
                            <button
                                @click="goTo('/chambres')"
                                class="flex items-center gap-3 p-4 bg-green-50 hover:bg-green-100 rounded-xl transition-colors group"
                            >
                                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center group-hover:scale-110">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21v-4a2 2 0 012-2h4a2 2 0 012 2v4" />
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <p class="font-medium text-gray-900">Gérer mes chambres</p>
                                    <p class="text-sm text-gray-600">Occupation et loyers</p>
                                </div>
                            </button>
                            <button
                                @click="relancerImpayes"
                                class="flex items-center gap-3 p-4 bg-red-50 hover:bg-red-100 rounded-xl transition-colors group"
                            >
                                <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center group-hover:scale-110">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM12 22c-4.97 0-9-4.03-9-9s4.03-9 9-9 9 4.03 9 9-4.03 9-9 9z" />
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <p class="font-medium text-gray-900">Relancer impayés</p>
                                    <p class="text-sm text-gray-600">Envoyer un rappel</p>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Recent payments -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Loyer payé / impayé -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Paiements récents</h3>
                                <router-link to="/paiements" class="text-sm text-blue-600 hover:text-blue-700">
                                    Voir tout
                                </router-link>
                            </div>
                            <div v-if="recentPayments.length === 0" class="text-center py-8 text-gray-500">
                                <p>Aucun paiement enregistré</p>
                            </div>
                            <div v-else class="space-y-3">
                                <div
                                    v-for="p in recentPayments"
                                    :key="p.id"
                                    class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                                >
                                    <div class="w-2 h-2 rounded-full" :class="p.statut === 'payé' ? 'bg-green-500' : 'bg-red-500'"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            Loyer - {{ p.mois }} {{ p.annee }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            Chambre {{ p.chambre_numero }} - {{ formatDate(p.date_paiement) }}
                                        </p>
                                    </div>
                                    <span class="text-sm font-semibold" :class="p.statut === 'payé' ? 'text-green-600' : 'text-red-600'">
                                        {{ formatCurrency(p.montant) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Upcoming rent -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Impayés</h3>
                                <router-link to="/paiements?statut=impayé" class="text-sm text-red-600 hover:text-red-700">
                                    Voir tous
                                </router-link>
                            </div>
                            <div v-if="overduePayments.length === 0" class="text-center py-8 text-gray-500">
                                <p>Aucun impayé</p>
                            </div>
                            <div v-else class="space-y-3">
                                <div
                                    v-for="p in overduePayments"
                                    :key="p.id"
                                    class="flex items-center gap-3 p-3 bg-red-50 rounded-lg hover:bg-red-100 transition-colors"
                                >
                                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                        ⚠️
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            Loyer - {{ p.mois }} {{ p.annee }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            Chambre {{ p.chambre_numero }} - {{ formatDate(p.date_limite) }}
                                        </p>
                                    </div>
                                    <span class="text-sm font-semibold text-red-600">
                                        {{ formatCurrency(p.montant) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic content -->
                    <div class="mt-8">
                        <router-view v-slot="{ Component }">
                            <transition name="fade" mode="out-in">
                                <component :is="Component" />
                            </transition>
                        </router-view>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, onUnmounted } from "vue";
import axios from "axios";
import router from "@/router";

// --- STATE ---
const loading = ref(false);
const error = ref(null);
const showProfileMenu = ref(false);
const currentTime = ref(new Date().toLocaleTimeString("fr-FR"));

// --- USER ---
const user = JSON.parse(localStorage.getItem("user") || "{}");

// --- DATA ---
const stats = ref({
    revenus_mensuels: 0,
    impayes: 0,
    chambres_louees: 0,
    maisons: 0,
});
const paymentsList = ref([]);

// --- COMPUTED ---
const currentDate = computed(() => {
    return new Date().toLocaleDateString("fr-FR", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    });
});

const recentPayments = computed(() => {
    return paymentsList.value
        .filter(p => p.statut === "payé")
        .sort((a, b) => new Date(b.date_paiement) - new Date(a.date_paiement))
        .slice(0, 5);
});

const overduePayments = computed(() => {
    return paymentsList.value
        .filter(p => p.statut === "impayé")
        .sort((a, b) => new Date(a.date_limite) - new Date(b.date_limite))
        .slice(0, 5);
});

// --- METHODS ---
const fetchData = async () => {
    loading.value = true;
    error.value = null;
    try {
        const [statsRes, paymentsRes] = await Promise.all([
            axios.get(`/api/proprietaires/${user.id}/stats`),
            axios.get(`/api/paiements?proprietaire_id=${user.id}`),
        ]);
        stats.value = statsRes.data;
        paymentsList.value = Array.isArray(paymentsRes.data?.data) ? paymentsRes.data.data : [];
    } catch (e) {
        console.error("Erreur chargement données propriétaire", e);
        error.value = "Impossible de charger les données";
    } finally {
        loading.value = false;
    }
};

const toggleProfileMenu = () => {
    showProfileMenu.value = !showProfileMenu.value;
};

const closeProfileMenu = () => {
    showProfileMenu.value = false;
};

const logout = () => {
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    delete axios.defaults.headers.common["Authorization"];
    router.push("/login");
};

const goTo = (path) => {
    router.push(path);
};

const relancerImpayes = () => {
    alert(`Relance envoyée aux locataires avec impayés (${overduePayments.value.length} locataires)`);
    // Appel API pour envoyer une notification ou email
};

const formatDate = (date) => {
    return date ? new Date(date).toLocaleDateString("fr-FR") : "N/A";
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat("fr-FR", { style: "currency", currency: "EUR" }).format(amount || 0);
};

const getTrend = (key) => {
    if (key === "revenus") return stats.value.revenus_mensuels > 0 ? "up" : "neutral";
    if (key === "occupancy") return stats.value.chambres_louees > 0 ? "up" : "neutral";
    return "neutral";
};

// --- ICONS ---
// --- ICONS ---
const UserGroupIcon = {
    template: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h2a2 2 0 002-2V9.897a2 2 0 00-1.108-1.796l-7-3.929a2 2 0 00-1.784 0l-7 3.929A2 2 0 003 9.897V18a2 2 0 002 2h2m0-6V8.117a2 2 0 011.108-1.796l7-3.929a2 2 0 011.784 0l7 3.929a2 2 0 011.108 1.796V12"/>
    </svg>`
};
const HomeIcon = {
    template: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
    </svg>`
};
const BedIcon = {
    template: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>`
};
const DocumentIcon = {
    template: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0120 9v12a2 2 0 01-2 2z"/>
    </svg>`
};
const ExclamationIcon = {
    template: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M4.93 19h14.14a2 2 0 001.72-2.74L13.72 4a2 2 0 00-3.44 0L3.2 16.26A2 2 0 004.93 19z"/>
    </svg>`
};
const CalendarIcon = {
    template: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>`
};
const AlertTriangleIcon = {
    template: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856a2 2 0 001.72-2.74L13.72 4a2 2 0 00-3.44 0L3.2 16.26A2 2 0 004.938 19z"/>
    </svg>`
};

// --- NAVIGATION ---
const links = [
    { path: "/utilisateurs", label: "Utilisateurs", icon: UserGroupIcon },
    { path: "/maisons", label: "Maisons", icon: HomeIcon },
    { path: "/chambres", label: "Chambres", icon: BedIcon },
    { path: "/contrats", label: "Contrats", icon: DocumentIcon },
    { path: "/paiements", label: "Paiements", icon: ExclamationIcon },
    { path: "/rendez-vous", label: "Rendez-vous", icon: CalendarIcon },
    { path: "/problemes", label: "Problèmes", icon: AlertTriangleIcon },
    { path: "/reservations", label: "Reservations", icon: ExclamationIcon },
];

// --- LIFECYCLE ---
onMounted(() => {
    fetchData();
    const timeInterval = setInterval(() => {
        currentTime.value = new Date().toLocaleTimeString("fr-FR");
    }, 60000);
    onUnmounted(() => clearInterval(timeInterval));
});

// --- DIRECTIVE ---
const vClickOutside = {
    mounted(el, binding) {
        el.clickOutsideEvent = (event) => {
            if (!(el === event.target || el.contains(event.target))) {
                binding.value();
            }
        };
        document.addEventListener("click", el.clickOutsideEvent);
    },
    unmounted(el) {
        document.removeEventListener("click", el.clickOutsideEvent);
    },
};
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: #f1f1f1; }
::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }
</style>
