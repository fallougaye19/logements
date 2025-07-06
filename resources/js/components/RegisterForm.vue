<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
        <form @submit.prevent="submit" class="bg-white shadow-lg p-8 rounded-xl w-full max-w-sm space-y-6">
            <h1 class="text-2xl font-bold text-center text-gray-800">Créer un compte</h1>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                <input v-model="form.nom" type="text" class="input" placeholder="Nom complet" required />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input v-model="form.email" type="email" class="input" placeholder="email@example.com" required />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                <input v-model="form.telephone" type="text" class="input" placeholder="77 000 00 00" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CNI</label>
                <input v-model="form.cni" type="text" class="input" placeholder="000000000000" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                <input v-model="form.password" type="password" class="input" placeholder="••••••••" required />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rôle</label>
                <select v-model="form.role" class="input" required>
                    <option disabled value="">-- Sélectionner --</option>
                    <option value="proprietaire">Propriétaire</option>
                    <option value="locataire">Locataire</option>
                </select>
            </div>

            <button class="btn" type="submit">S'inscrire</button>

            <p v-if="error" class="text-sm text-red-500 text-center">{{ error }}</p>

            <p class="text-sm text-center text-gray-600">
                Déjà inscrit ?
                <router-link to="/login" class="text-blue-600 hover:underline font-medium">Se connecter</router-link>
            </p>
        </form>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const form = ref({
    nom: '',
    email: '',
    telephone: '',
    cni: '',
    password: '',
    role: 'locataire' // Valeur par défaut
})
const error = ref('')

const submit = async () => {
    error.value = ''
    try {
        const { data } = await axios.post('/api/auth/register', form.value)
        localStorage.setItem('token', data.token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${data.token}`

        // Rediriger selon le rôle
        if (data.user.role === 'locataire') {
            router.push('/dashboardLoc')
        } else {
            router.push('/dashboard')
        }
    } catch (e) {
        error.value = 'Erreur lors de l"inscription'
    }
}
</script>

<style scoped>
.input {
    @apply w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500;
}

.btn {
    @apply w-full bg-blue-600 text-white py-2 rounded-md font-semibold hover:bg-blue-700 transition;
}
</style>
