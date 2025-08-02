<template>
    <div class="bg-gray-50 min-h-screen p-8">
        <div
            class="max-w-4xl mx-auto bg-white rounded-3xl shadow-2xl p-8 md:p-12"
        >
            <!-- Header du profil avec icône et titre -->
            <div class="flex items-center mb-10">
                <svg
                    class="w-12 h-12 mr-4 text-blue-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                    ></path>
                </svg>
                <h1 class="text-4xl font-extrabold text-gray-900">
                    Mon Profil
                </h1>
            </div>

            <!-- Loader stylisé -->
            <div v-if="loading" class="text-center py-20">
                <div
                    class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500 mx-auto"
                ></div>
                <p class="mt-4 text-lg text-gray-600">
                    Chargement de votre profil...
                </p>
            </div>

            <!-- Message d'erreur stylisé -->
            <div
                v-else-if="error"
                class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-inner"
                role="alert"
            >
                <p class="font-bold">Erreur de chargement</p>
                <p>{{ error }}</p>
            </div>

            <!-- Affichage du profil ou du formulaire d'édition -->
            <transition name="fade" mode="out-in">
                <div v-if="!editing" :key="'profile-view'">
                    <div v-if="user" class="space-y-8">
                        <!-- Carte du profil -->
                        <div
                            class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100 transition-transform transform hover:scale-105"
                        >
                            <div class="flex items-center space-x-6 mb-6">
                                <div
                                    class="w-20 h-20 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white text-3xl font-bold shadow-lg"
                                >
                                    {{ getInitials(user) }}
                                </div>
                                <h2 class="text-2xl font-bold text-gray-900">
                                    {{ user.nom }}
                                </h2>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p
                                        class="text-sm text-gray-500 font-medium flex items-center mb-1"
                                    >
                                        <svg
                                            class="w-5 h-5 mr-2 text-gray-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                            ></path>
                                        </svg>
                                        Email
                                    </p>
                                    <p class="text-lg text-gray-800">
                                        {{ user.email || "Non renseigné" }}
                                    </p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p
                                        class="text-sm text-gray-500 font-medium flex items-center mb-1"
                                    >
                                        <svg
                                            class="w-5 h-5 mr-2 text-gray-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                                            ></path>
                                        </svg>
                                        Téléphone
                                    </p>
                                    <p class="text-lg text-gray-800">
                                        {{ user.telephone || "Non renseigné" }}
                                    </p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p
                                        class="text-sm text-gray-500 font-medium flex items-center mb-1"
                                    >
                                        <svg
                                            class="w-5 h-5 mr-2 text-gray-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 6h16M4 12h16M4 18h16"
                                            ></path>
                                        </svg>
                                        CNI
                                    </p>
                                    <p class="text-lg text-gray-800">
                                        {{ user.cni || "Non renseigné" }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-8 flex space-x-4">
                                <button
                                    @click="editProfile"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-xl shadow-md transition-colors duration-200 flex items-center"
                                >
                                    <svg
                                        class="w-5 h-5 mr-2"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                                        ></path>
                                    </svg>
                                    Modifier le profil
                                </button>
                                <button
                                    @click="logout"
                                    class="bg-gray-200 hover:bg-red-500 hover:text-white text-gray-700 font-semibold py-3 px-6 rounded-xl shadow-md transition-colors duration-200 flex items-center"
                                >
                                    <svg
                                        class="w-5 h-5 mr-2"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                        ></path>
                                    </svg>
                                    Déconnexion
                                </button>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center text-gray-600 py-10">
                        <p>Aucun utilisateur trouvé.</p>
                    </div>
                </div>

                <!-- Formulaire d'édition -->
                <div v-else :key="'profile-form'">
                    <ProfileForm
                        :utilisateur="user"
                        :isEdit="true"
                        @updated="handleProfileUpdate"
                    />
                </div>
            </transition>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { useRouter } from "vue-router";
import { onMounted, ref } from "vue";
import ProfileForm from "@/components/ProfileForm.vue";

export default {
    components: { ProfileForm },
    setup() {
        const router = useRouter();
        const user = ref(null);
        const loading = ref(true);
        const error = ref(null);
        const editing = ref(false);
        const debugMode = ref(false);

        // API
        const api = axios.create({
            baseURL: "http://localhost:8000/api",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
            },
        });

        // Fonction pour récupérer les initiales
        const getInitials = (user) => {
            if (!user || !user.nom) return "";
            const parts = user.nom.split(" ");
            return parts
                .map((part) => part[0])
                .join("")
                .toUpperCase();
        };

        const fetchUser = async () => {
            loading.value = true;
            error.value = null;

            // Tentative de récupération depuis localStorage pour affichage immédiat
            const userFromStorage = localStorage.getItem("user");
            if (userFromStorage) {
                try {
                    user.value = JSON.parse(userFromStorage);
                    editing.value = false;
                } catch (parseErr) {
                    console.error(
                        "Erreur lors du parsing localStorage:",
                        parseErr
                    );
                }
            }

            // Récupération des données du serveur
            const token = localStorage.getItem("token");
            if (!token) {
                router.push("/login");
                loading.value = false;
                return;
            }

            api.defaults.headers.common["Authorization"] = `Bearer ${token}`;

            try {
                const response = await api.get("/user");
                user.value = response.data;
                localStorage.setItem("user", JSON.stringify(response.data));
            } catch (e) {
                if (e.response && e.response.status === 401) {
                    logout();
                } else {
                    error.value = "Impossible de charger le profil.";
                    console.error("Erreur de chargement du profil:", e);
                }
            } finally {
                loading.value = false;
            }
        };

        const handleProfileUpdate = (updatedUser) => {
            user.value = updatedUser;
            editing.value = false;
            localStorage.setItem("user", JSON.stringify(updatedUser));
        };

        const editProfile = () => {
            editing.value = true;
        };

        const logout = () => {
            localStorage.removeItem("token");
            localStorage.removeItem("user");
            delete api.defaults.headers.common["Authorization"];
            router.push("/login");
        };

        onMounted(() => {
            fetchUser();
        });

        return {
            user,
            loading,
            error,
            editing,
            debugMode,
            getInitials,
            fetchUser,
            handleProfileUpdate,
            editProfile,
            logout,
        };
    },
};
</script>

<style scoped>
/* Animations pour les transitions */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
