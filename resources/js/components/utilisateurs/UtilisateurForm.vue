<template>
    <div class="p-8 bg-gray-100 min-h-screen flex justify-center items-start">
        <form
            @submit.prevent="submit"
            class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-xl space-y-6"
        >
            <h2 class="text-2xl font-bold text-blue-800 text-center">
                Nouvel utilisateur
            </h2>

            <!-- Nom utilisateur -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Nom d'utilisateur</label
                >
                <input
                    v-model="form.nom"
                    type="text"
                    class="input"
                    placeholder="Ex : Fallou Gaye"
                    required
                />
            </div>

            <!-- Email -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Email</label
                >
                <input
                    v-model="form.email"
                    type="email"
                    class="input"
                    placeholder="Ex : fallou@example.com"
                />
            </div>

            <!-- Téléphone -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Téléphone</label
                >
                <input
                    v-model="form.telephone"
                    type="text"
                    class="input"
                    placeholder="Ex : 77 123 45 67"
                />
            </div>

            <!-- CNI -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Numéro CNI</label
                >
                <input
                    v-model="form.cni"
                    type="text"
                    class="input"
                    placeholder="Ex : 123456789"
                />
            </div>

            <!-- Mot de passe -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Mot de passe</label
                >
                <input
                    v-model="form.password"
                    type="password"
                    class="input"
                    placeholder="••••••••"
                    required
                />
            </div>

            <!-- Bouton -->
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 transition text-white font-semibold py-2 rounded-lg shadow"
            >
                Ajouter l'utilisateur
            </button>

            <!-- Message -->
            <p
                v-if="message"
                class="text-sm text-center"
                :class="{
                    'text-green-600': success,
                    'text-red-600': !success,
                }"
            >
                {{ message }}
            </p>
        </form>
    </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
const emit = defineEmits(["created"]);

const form = ref({
    nom: "",
    email: "",
    telephone: "",
    cni: "",
    role: "locataire",
    password: "",
});

const message = ref("");
const success = ref(false);

const submit = async () => {
    try {
        await axios.post("/api/utilisateurs", form.value);
        message.value = "Utilisateur ajouté avec succès";
        success.value = true;

        form.value = {
            nom: "",
            email: "",
            telephone: "",
            cni: "",
            role: "locataire",
            password: "",
        };

        emit("created");
    } catch (e) {
        message.value = "Erreur lors de l'ajout";
        success.value = false;
    }
};
</script>

<style scoped>
.input {
    @apply w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500;
}
</style>
