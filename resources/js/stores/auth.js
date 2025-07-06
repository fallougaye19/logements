import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
    state: () => ({ user: null, token: localStorage.getItem('token') || '' }),
    actions: {
        async login(payload) {
            const { data } = await axios.post('/api/auth/login', payload)
            this.token = data.token
            this.user = data.user
            localStorage.setItem('token', data.token)
            axios.defaults.headers.common['Authorization'] = `Bearer ${data.token}`
        },

        async register(payload) {
            try {
                const { data } = await axios.post('/api/auth/register', payload)
                this.token = data.token
                this.user = data.user

                localStorage.setItem('token', data.token)
                axios.defaults.headers.common['Authorization'] = `Bearer ${data.token}`
            } catch (error) {
                throw error.response?.data?.message || 'Erreur lors de l’inscription'
            }
        },
        logout() {
            this.token = ''
            this.user = null
            localStorage.removeItem('token')
            delete axios.defaults.headers.common['Authorization']
        },

        async logout() {
            try {
                await axios.post('/api/auth/logout')
            } catch (e) {
                // ignore erreur API si token expiré
            }

            this.token = ''
            this.user = null
            localStorage.removeItem('token')
            delete axios.defaults.headers.common['Authorization']
        },

        async fetchUser() {
            try {
                const { data } = await axios.get('/api/user')
                this.user = data
            } catch {
                this.logout()
            }
        }
    }
})
