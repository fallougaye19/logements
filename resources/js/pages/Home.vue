<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 flex flex-col items-center">
        <!-- Header centré -->
        <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm shadow-sm w-full">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-center">
                <div class="flex justify-between items-center h-16 w-full">
                    <!-- Logo -->
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                            RésidencePlus
                        </span>
                    </div>

                    <!-- Navigation centrée -->
                    <nav class="hidden md:flex space-x-8 mx-auto">
                        <a href="#hero" class="text-gray-700 hover:text-blue-600 transition-colors">Accueil</a>
                        <a href="#proprietes" class="text-gray-700 hover:text-blue-600 transition-colors">Propriétés</a>
                        <a href="#services" class="text-gray-700 hover:text-blue-600 transition-colors">Services</a>
                    </nav>

                    <!-- Auth Buttons -->
                    <div class="flex items-center space-x-4">
                        <button @click="goToLogin" class="px-4 py-2 text-gray-700 hover:text-blue-600 transition-colors">
                            Connexion
                        </button>
                        <button @click="goToRegister" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5">
                            Inscription
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section centrée -->
        <section id="hero" class="relative py-20 overflow-hidden min-h-[80vh] w-full flex items-center justify-center">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/80 via-purple-900/70 to-blue-800/80"></div>

            <div class="relative w-full max-w-7xl px-4 sm:px-6 lg:px-8 flex justify-center">
                <div class="text-center w-full">
                    <h1 class="text-4xl md:text-6xl font-bold text-white mb-6 drop-shadow-lg">
                        Gérez vos <span class="bg-gradient-to-r from-yellow-400 to-orange-400 bg-clip-text text-transparent">locations immobilières</span><br>avec facilité
                    </h1>
                    <p class="text-xl text-blue-100 mb-8 max-w-3xl mx-auto drop-shadow-md">
                        RésidencePlus est la solution tout-en-un pour la gestion de vos appartements et chambres en location. Simplifiez la gestion des locataires, des paiements et de la maintenance.
                    </p>
                    <div class="flex justify-center gap-4">
                        <button @click="goToRegister" class="px-8 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-105 font-semibold">
                            Commencer maintenant
                        </button>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white animate-bounce">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
            </div>
        </section>

        <!-- Section Propriétés centrée -->
        <section id="proprietes" class="py-20 bg-white w-full flex justify-center">
            <div class="max-w-7xl w-full px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Nos Propriétés</h2>
                    <p class="text-xl text-gray-600">Chambres disponibles à la location</p>
                </div>

                <div v-if="loading" class="flex justify-center py-10">
                    <div class="animate-spin h-10 w-10 border-4 border-blue-500 border-t-transparent rounded-full"></div>
                </div>

                <div v-else-if="error" class="text-center text-red-600">
                    {{ error }}
                </div>

                <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 justify-items-center">
                    <div v-for="chambre in chambres" :key="chambre.id" class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 w-full max-w-sm">
                        <div class="relative">
                            <img :src="chambre.image_url || 'https://via.placeholder.com/400x300'" :alt="chambre.titre" class="w-full h-48 object-cover"/>
                            <div class="absolute top-4 right-4">
                                <span :class="{'bg-green-500': chambre.disponible, 'bg-red-500': !chambre.disponible}" class="px-3 py-1 text-white text-sm rounded-full">
                                    {{ chambre.disponible ? "Disponible" : "Occupée" }}
                                </span>
                            </div>
                        </div>

                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-800 text-center">{{ chambre.titre }}</h3>
                            <p class="text-gray-600 mt-1 text-center">{{ chambre.maison?.adresse }}</p>
                            <p class="text-sm text-gray-500 mt-2 text-center">{{ chambre.description }}</p>

                            <div class="flex items-center justify-between mt-6">
                                <span class="text-2xl font-bold text-blue-600">
                                    {{ new Intl.NumberFormat("fr-FR", { style: "currency", currency: "XOF" }).format(chambre.prix) }}
                                    <span class="text-sm text-gray-500">/mois</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section centrée -->
        <section id="services" class="py-20 bg-gradient-to-r from-blue-600 to-purple-600 w-full flex justify-center">
            <div class="max-w-7xl w-full px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Notre impact</h2>
                    <p class="text-xl text-blue-100 max-w-3xl mx-auto">Des chiffres qui parlent d'eux-mêmes</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 justify-items-center">
                    <div class="text-center w-full max-w-xs">
                        <div class="text-4xl font-bold text-white mb-2">98%</div>
                        <div class="text-blue-100">Paiements à temps</div>
                    </div>
                    <div class="text-center w-full max-w-xs">
                        <div class="text-4xl font-bold text-white mb-2">24/7</div>
                        <div class="text-blue-100">Support disponible</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer centré -->
        <footer id="contact" class="bg-gray-900 text-white py-16 w-full flex justify-center">
            <div class="max-w-7xl w-full px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-4 gap-8 justify-items-center">
                    <div class="col-span-2 max-w-xl">
                        <div class="flex items-center space-x-2 mb-4 justify-center md:justify-start">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                </svg>
                            </div>
                            <span class="text-xl font-bold">RésidencePlus</span>
                        </div>
                        <p class="text-gray-400 mb-6 text-center md:text-left">
                            Votre solution complète pour la gestion de locations immobilières. Simplifiez la gestion de vos appartements et chambres en location.
                        </p>
                        <div class="flex space-x-4 justify-center md:justify-start">
                            <a href="#" class="text-gray-400 hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="max-w-xs">
                        <h3 class="text-lg font-semibold mb-4 text-center md:text-left">Services</h3>
                        <ul class="space-y-2 text-gray-400 text-center md:text-left">
                            <li><a href="#" class="hover:text-white transition-colors">Gestion locative</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Suivi des paiements</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Maintenance</a></li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                    <p>&copy; 2025 RésidencePlus. Tous droits réservés.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "Home",
    data() {
        return {
            chambres: [],
            loading: true,
            error: null,
        };
    },
    async mounted() {
        await this.loadChambres();
    },
    methods: {
        async loadChambres() {
            try {
                const token = localStorage.getItem("token");
                const response = await axios.get("/api/chambres/disponibles", {
                    headers: token ? { Authorization: `Bearer ${token}` } : {},
                });

                if (response.data.success) {
                    this.chambres = response.data.data.slice(0, 6);
                } else {
                    this.error = response.data.message || "Erreur lors du chargement.";
                }
            } catch (err) {
                this.error = "Impossible de charger les chambres.";
                console.error("Erreur API:", err);
            } finally {
                this.loading = false;
            }
        },
        goToLogin() {
            this.$router.push("/login");
        },
        goToRegister() {
            this.$router.push("/register");
        }
    },
};
</script>

<style>
/* Styles pour améliorer le centrage sur mobile */
@media (max-width: 768px) {
    header .flex {
        flex-direction: column;
        gap: 1rem;
        padding: 1rem 0;
    }
    
    .auth-buttons {
        width: 100%;
        justify-content: center;
    }
    
    .flex.justify-between {
        flex-direction: column;
        align-items: center;
    }
    
    .flex.items-center.space-x-4 {
        margin-top: 1rem;
    }
}
</style>