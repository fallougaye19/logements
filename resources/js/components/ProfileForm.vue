<template>
    <div class="p-8 bg-gray-50 rounded-2xl shadow-xl border border-gray-100">
        <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">
            {{ isEdit ? 'Modifier le profil' : 'Ajouter un utilisateur' }}
        </h2>
        <form @submit.prevent="submit" class="space-y-6">
            <!-- Champ Nom -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nom</label>
                <input
                    v-model="form.nom"
                    type="text"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors"
                />
            </div>
            <!-- Champ Email -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input
                    v-model="form.email"
                    type="email"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors"
                />
            </div>
            <!-- Champ Téléphone -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Téléphone</label>
                <input
                    v-model="form.telephone"
                    type="tel"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors"
                />
            </div>
            <!-- Champ CNI -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">CNI</label>
                <input
                    v-model="form.cni"
                    type="text"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors"
                />
            </div>
            <!-- Champ Mot de passe -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Mot de passe</label>
                <input
                    v-model="form.password"
                    type="password"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="••••••••"
                />
            </div>

            <!-- Bouton de soumission -->
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg transition-all duration-200 transform hover:scale-105"
            >
                {{ isEdit ? 'Enregistrer les modifications' : 'Ajouter utilisateur' }}
            </button>

            <!-- Messages de retour -->
            <p v-if="message" :class="{ 'text-green-600': success, 'text-red-600': !success }" class="text-center font-medium mt-4">
                {{ message }}
            </p>
        </form>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    utilisateur: {
        type: Object,
        default: () => ({}),
    },
    isEdit: {
        type: Boolean,
        default: false,
    },
});

const emits = defineEmits(['updated', 'created']);

const form = ref({
    nom: '',
    email: '',
    telephone: '',
    cni: '',
    password: '',
});

const message = ref('');
const success = ref(false);

const api = axios.create({
    baseURL: 'http://localhost:8000/api',
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
    },
});

watch(() => props.utilisateur, (newUser) => {
    if (props.isEdit && newUser) {
        form.value = {
            nom: newUser.nom || '',
            email: newUser.email || '',
            telephone: newUser.telephone || '',
            cni: newUser.cni || '',
            password: '',
        };
    }
}, { immediate: true });

// Fonction de validation d'email
const validateEmail = (email) => {
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
};

const submit = async () => {
    message.value = '';
    success.value = false;

    // Basic validation
    if (!form.value.nom.trim()) {
        message.value = "Le nom est requis.";
        return;
    }
    if (!form.value.email.trim() || !validateEmail(form.value.email)) {
        message.value = "Un email valide est requis.";
        return;
    }

    try {
        let response;
        if (props.isEdit && props.utilisateur?.id) {
            response = await api.put(`/utilisateurs/${props.utilisateur.id}`, form.value);
            emits("updated", response.data);
        } else {
            response = await api.post("/utilisateurs", form.value);
            emits("created", response.data);
        }

        message.value = props.isEdit ? "Profil mis à jour." : "Utilisateur ajouté.";
        success.value = true;
    } catch (e) {
        if (e.response && e.response.data && e.response.data.message) {
            message.value = `Erreur: ${e.response.data.message}`;
        } else {
            message.value = "Erreur lors de l'enregistrement.";
        }
        success.value = false;
        console.error(e);
    }
};
</script>
