<template>
    <div
        class="flex min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 text-gray-800"
    >
        <!-- Sidebar -->
        <aside
            class="w-64 bg-gradient-to-b from-gray-600 to-gray-700 shadow-xl flex flex-col transition-all duration-300 ease-in-out"
        >
            <div
                class="px-6 py-6 text-2xl font-bold text-white border-b border-blue-500/30 backdrop-blur-sm"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center"
                    >
                        🏠
                    </div>
                    <span
                        class="bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent"
                    >
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
                    <component
                        :is="link.icon"
                        class="w-5 h-5 group-hover:scale-110 transition-transform"
                    />
                    {{ link.label }}
                </router-link>
            </nav>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col">
            <!-- Topbar -->
            <header
                class="sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-gray-200/60 px-6 h-16 flex items-center justify-between shadow-sm"
            >
                <div class="flex items-center gap-4">
                    <h1
                        class="text-xl font-semibold text-black-600 flex items-center gap-2"
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
                                d="M9 19V9a2 2 0 012-2h2a2 2 0 012 2v10m6 0V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"
                            />
                        </svg>
                        Tableau de bord
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Profile dropdown -->
                    <div class="relative" v-click-outside="closeProfileMenu">
                        <button
                            @click="toggleProfileMenu"
                            class="flex items-center gap-2 p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                        >
                            <div
                                class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center"
                            >
                                👤
                            </div>
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
                                    d="M19 9l-7 7-7-7"
                                />
                            </svg>
                        </button>

                        <div
                            v-if="showProfileMenu"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1"
                        >
                            <router-link
                                to="/profile"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            >
                                👤 Mon Profil
                            </router-link>
                            <hr class="my-1 border-gray-200" />
                            <button
                                @click="logout"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                            >
                                ⛔ Déconnexion
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Dashboard content -->
            <main class="flex-1 p-6 overflow-y-auto">
                <!-- Loading state -->
                <div
                    v-if="loading"
                    class="flex items-center justify-center h-64"
                >
                    <div
                        class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"
                    ></div>
                </div>

                <!-- Error state -->
                <div
                    v-else-if="error"
                    class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6"
                >
                    <div class="flex items-center gap-2 text-red-700">
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
                                d="M12 9v2m0 4h.01M4.93 19h14.14a2 2 0 001.72-2.74L13.72 4a2 2 0 00-3.44 0L3.2 16.26A2 2 0 004.93 19z"
                            />
                        </svg>
                        <span class="font-medium">Erreur de chargement</span>
                    </div>
                    <p class="text-red-600 mt-1">{{ error }}</p>
                    <button
                        @click="fetchDashboardData"
                        class="mt-3 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                    >
                        Réessayer
                    </button>
                </div>

                <!-- Content -->
                <div v-else>
                    <!-- Welcome message -->
                    <div
                        class="bg-gradient-to-r from-gray-500 to-gray-600 rounded-2xl p-6 mb-6 text-white"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-2xl font-bold mb-2">
                                    Bonjour {{ user?.nom || "Utilisateur" }} !
                                    👋
                                </h2>
                                <p class="text-blue-100">
                                    Voici un aperçu de votre situation locative
                                </p>
                            </div>
                            <div class="hidden md:block">
                                <div class="text-right">
                                    <p class="text-sm text-blue-100">
                                        {{ currentDate }}
                                    </p>
                                    <p class="text-xs text-blue-200">
                                        {{ currentTime }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stat cards -->
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8"
                    >
                        <StatCard
                            label="Chambre louée"
                            :value="chambre ? 'Oui' : 'Non'"
                            :color="chambre ? 'green' : 'gray'"
                            icon="home"
                            :trend="chambre ? 'up' : 'neutral'"
                        />
                        <StatCard
                            label="Contrat actif"
                            :value="
                                contrat ? formatDate(contrat.date_debut) : 'Aucun'
                            "
                            :color="contrat ? 'blue' : 'gray'"
                            icon="file-text"
                            :subtitle="contrat ? 'Expire le' : 'Pas de contrat'"
                        />
                        <StatCard
                            label="Problèmes signalés"
                            :value="problemes.length"
                            :color="problemes.length > 0 ? 'red' : 'green'"
                            icon="alert-triangle"
                            :trend="problemes.length > 0 ? 'down' : 'neutral'"
                        />
                        <StatCard
                            label="Rendez-vous à venir"
                            :value="rendezVous.length"
                            color="indigo"
                            icon="calendar"
                            :subtitle="nextAppointment"
                        />
                    </div>

                    <!-- Quick actions -->
                    <div
                        class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6"
                    >
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Actions rapides
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <button
                                @click="redirectToProblemes"
                                class="flex items-center gap-3 p-4 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors group"
                            >
                                <div
                                    class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform"
                                >
                                    <svg
                                        class="w-5 h-5 text-white"
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
                                </div>
                                <div class="text-left">
                                    <p class="font-medium text-gray-900">
                                        Signaler un problème
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Maintenance ou réparation
                                    </p>
                                </div>
                            </button>

                            <button
                                @click="redirectToRendezVous"
                                class="flex items-center gap-3 p-4 bg-green-50 hover:bg-green-100 rounded-xl transition-colors group"
                            >
                                <div
                                    class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform"
                                >
                                    <svg
                                        class="w-5 h-5 text-white"
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
                                <div class="text-left">
                                    <p class="font-medium text-gray-900">
                                        Prendre RDV
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Planifier une visite
                                    </p>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Recent activity -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Recent problems -->
                        <div
                            class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6"
                        >
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Problèmes récents
                                </h3>
                                <router-link
                                    to="/problemes"
                                    class="text-sm text-blue-600 hover:text-blue-700"
                                >
                                    Voir tout
                                </router-link>
                            </div>

                            <div
                                v-if="recentProblems.length === 0"
                                class="text-center py-8 text-gray-500"
                            >
                                <svg
                                    class="w-12 h-12 mx-auto mb-2 text-gray-300"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                                <p>Aucun problème signalé</p>
                            </div>

                            <div v-else class="space-y-3">
                                <div
                                    v-for="problem in recentProblems"
                                    :key="problem.id"
                                    class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                                >
                                    <div
                                        class="w-2 h-2 rounded-full"
                                        :class="getStatusColor(problem.statut)"
                                    ></div>
                                    <div class="flex-1 min-w-0">
                                        <p
                                            class="text-sm font-medium text-gray-900 truncate"
                                        >
                                            {{ problem.titre }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{
                                                formatDate(
                                                    problem.date_creation
                                                )
                                            }}
                                        </p>
                                    </div>
                                    <span
                                        class="text-xs px-2 py-1 rounded-full"
                                        :class="getStatusBadge(problem.statut)"
                                    >
                                        {{ problem.statut }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Upcoming appointments -->
                        <div
                            class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6"
                        >
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Prochains RDV
                                </h3>
                                <router-link
                                    to="/rendez-vous"
                                    class="text-sm text-blue-600 hover:text-blue-700"
                                >
                                    Voir tout
                                </router-link>
                            </div>

                            <div
                                v-if="upcomingAppointments.length === 0"
                                class="text-center py-8 text-gray-500"
                            >
                                <svg
                                    class="w-12 h-12 mx-auto mb-2 text-gray-300"
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
                                <p>Aucun rendez-vous planifié</p>
                            </div>

                            <div v-else class="space-y-3">
                                <div
                                    v-for="rdv in upcomingAppointments"
                                    :key="rdv.id"
                                    class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                                >
                                    <div
                                        class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center"
                                    >
                                        <svg
                                            class="w-5 h-5 text-indigo-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p
                                            class="text-sm font-medium text-gray-900 truncate"
                                        >
                                            {{ rdv.objet }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ formatDateTime(rdv.date_heure) }}
                                        </p>
                                    </div>
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
import StatCard from "@/components/Dashboard/StatCard.vue";
import router from "@/router";

// State
const loading = ref(false);
const error = ref(null);
const showProfileMenu = ref(false);
const currentTime = ref(new Date().toLocaleTimeString("fr-FR"));

// Data
const user = JSON.parse(localStorage.getItem("user") || "{}");
const contrat = ref(null);
const chambre = ref(null);
const problemes = ref([]);
const rendezVous = ref([]);

// Computed properties
const currentDate = computed(() => {
    return new Date().toLocaleDateString("fr-FR", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    });
});

const totalNotifications = computed(() => {
    return (
        problemes.value.filter((p) => p.statut === "nouveau").length +
        rendezVous.value.filter((r) => new Date(r.date_heure) > new Date())
            .length
    );
});

const recentProblems = computed(() => {
    return problemes.value.slice(0, 3);
});

const upcomingAppointments = computed(() => {
    const now = new Date();
    return rendezVous.value
        .filter((rdv) => new Date(rdv.date_heure) > now)
        .sort((a, b) => new Date(a.date_heure) - new Date(b.date_heure))
        .slice(0, 3);
});

const nextAppointment = computed(() => {
    const next = upcomingAppointments.value[0];
    if (!next) return "Aucun";

    const date = new Date(next.date_heure);
    const now = new Date();
    const diffTime = Math.abs(date - now);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 0) return "Aujourd'hui";
    if (diffDays === 1) return "Demain";
    return `Dans ${diffDays} jours`;
});

// Icons
const HomeIcon = {
    template: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M8 21v-4a2 2 0 012-2h4a2 2 0 012 2v4"/>
    </svg>`,
};

const DocumentIcon = {
    template: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0120 9v12a2 2 0 01-2 2z"/>
    </svg>`,
};

const ExclamationIcon = {
    template: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M12 9v2m0 4h.01M4.93 19h14.14a2 2 0 001.72-2.74L13.72 4a2 2 0 00-3.44 0L3.2 16.26A2 2 0 004.93 19z"/>
    </svg>`,
};

const CalendarIcon = {
    template: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>`,
};

const links = [
    { path: "/chambres", label: "Chambres", icon: HomeIcon },
    { path: "/contrats", label: "Contrats", icon: DocumentIcon },
    { path: "/paiements", label: "Paiements", icon: DocumentIcon },
    { path: "/problemes", label: "Problèmes", icon: ExclamationIcon },
    { path: "/rendez-vous", label: "Rendez-vous", icon: CalendarIcon },
];

// Methods
const fetchDashboardData = async () => {
    loading.value = true;
    error.value = null;

    try {
        const [contratRes, problemeRes, rdvRes] = await Promise.all([
            axios.get(`/api/contrats`),
            axios.get(`/api/problemes?signale_par=${user.id}`),
            axios.get(`/api/rendez-vous?locataire_id=${user.id}`),
        ]);

        const problemesData = Array.isArray(problemeRes.data?.data) ? problemeRes.data.data : [];
        const rendezVousData = Array.isArray(rdvRes.data?.data) ? rdvRes.data.data : [];
        const contrats = Array.isArray(contratRes.data?.data) ? contratRes.data.data : [];
        contrat.value = contrats.find((c) => c.statut === "actif") || null;

        if (contrat.value?.chambre_id) {
            const chambreRes = await axios.get(`/api/chambres/${contrat.value.chambre_id}`);
            if (chambreRes.data.success) {
                chambre.value = chambreRes.data.data;
            }
        }

        problemes.value = problemesData;
        rendezVous.value = rendezVousData;
    } catch (e) {
        console.error("Erreur lors du chargement des données", e);
        error.value = "Impossible de charger les données du tableau de bord";
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

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("fr-FR");
};

const formatDateTime = (dateTime) => {
    return new Date(dateTime).toLocaleString("fr-FR");
};

const getStatusColor = (status) => {
    const colors = {
        nouveau: "bg-red-400",
        en_cours: "bg-yellow-400",
        resolu: "bg-green-400",
        ferme: "bg-gray-400",
    };
    return colors[status] || "bg-gray-400";
};

const getStatusBadge = (status) => {
    const badges = {
        nouveau: "bg-red-100 text-red-800",
        en_cours: "bg-yellow-100 text-yellow-800",
        resolu: "bg-green-100 text-green-800",
        ferme: "bg-gray-100 text-gray-800",
    };
    return badges[status] || "bg-gray-100 text-gray-800";
};

// Lifecycle
onMounted(() => {
    fetchDashboardData();

    // Update time every minute
    const timeInterval = setInterval(() => {
        currentTime.value = new Date().toLocaleTimeString("fr-FR");
    }, 60000);

    onUnmounted(() => {
        clearInterval(timeInterval);
    });
});

// Directive for click outside
const vClickOutside = {
    mounted(el, binding) {
        el.clickOutsideEvent = function (event) {
            if (!(el === event.target || el.contains(event.target))) {
                binding.value(event);
            }
        };
        document.addEventListener("click", el.clickOutsideEvent);
    },
    unmounted(el) {
        document.removeEventListener("click", el.clickOutsideEvent);
    },
};

const redirectToProblemes = () => {
    router.push("/problemes");
};
const redirectToRendezVous = () => {
    router.push("/rendez-vous");
};

</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
