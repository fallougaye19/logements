<template>
    <div class="p-4">
        <!-- Titre -->
        <div class="flex items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Mon Profil</h2>
        </div>

        <!-- Loader -->
        <div v-if="loading" class="text-center py-8">
            <div
                class="animate-spin rounded-full h-10 w-10 border-4 border-blue-500 border-t-transparent mx-auto"
            ></div>
            <p class="mt-2 text-gray-600">Chargement...</p>
        </div>

        <!-- Profil utilisateur -->
        <div
            v-if="user && !loading"
            class="bg-white rounded-lg shadow-md p-6 max-w-xl mx-auto"
        >
            <div class="flex justify-center mb-6">
                <div
                    class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 text-2xl"
                >
                    {{ user.nom.charAt(0).toUpperCase() }}
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500"
                        >Nom</label
                    >
                    <p class="text-lg">{{ user.nom }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500"
                        >Email</label
                    >
                    <p class="text-lg">{{ user.email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500"
                        >Téléphone</label
                    >
                    <p class="text-lg">
                        {{ user.telephone || "Non renseigné" }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500"
                        >CNI</label
                    >
                    <p class="text-lg">{{ user.cni || "Non renseigné" }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500"
                        >Rôle</label
                    >
                    <p class="capitalize text-lg">{{ user.role }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500"
                        >Inscrit depuis</label
                    >
                    <p class="text-lg">{{ formatDate(user.created_at) }}</p>
                </div>
            </div>

            <!-- Boutons -->
            <div class="mt-6 flex justify-end space-x-4">
                <button
                    @click="editProfile"
                    class="btn-icon text-yellow-600 flex items-center"
                >
                    <svg
                        class="w-4 h-4 mr-1"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-3-6h.01M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1H7a1 1 0 00-1 1v3M4 7h16"
                        />
                    </svg>
                    Modifier mon profil
                </button>
                <button
                    @click="logout"
                    class="btn-icon text-red-600 flex items-center"
                >
                    <svg
                        class="w-4 h-4 mr-1"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4V7m0 0l3 3m-3-3l-3 3"
                        />
                    </svg>
                    Se déconnecter
                </button>
            </div>
        </div>

        <!-- Modal d'édition -->
        <div
            v-if="editing"
            class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
        >
            <div
                class="bg-white rounded-lg shadow-xl w-full max-w-xl max-h-[90vh] overflow-y-auto"
            >
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">
                            Modifier mon profil
                        </h3>
                        <button
                            @click="editing = false"
                            class="text-gray-500 hover:text-gray-700"
                        >
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                    <ProfileForm
                        :utilisateur="user"
                        :is-edit="true"
                        @updated="fetchUser"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import ProfileForm from "@/components/ProfileForm.vue";
//import router from "@/router";

// États
const user = ref(null);
const loading = ref(true);
const editing = ref(false);

// Charger l'utilisateur connecté
const fetchUser = async () => {
    try {
        const res = await axios.get("/api/user");
        user.value = res.data;
    } catch (e) {
        alert("Erreur lors du chargement du profil");
        console.error(e);
    } finally {
        loading.value = false;
    }
};

// Méthodes utilitaires
const editProfile = () => {
    editing.value = true;
};

const logout = () => {
    localStorage.removeItem("token");
    delete axios.defaults.headers.common["Authorization"];
    router.push("/login");
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("fr-FR");
};

onMounted(fetchUser);
</script>

<style scoped>
.btn {
    @apply bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors duration-200;
}
.btn-icon {
    @apply p-2 rounded hover:bg-gray-100 transition-colors duration-200;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
