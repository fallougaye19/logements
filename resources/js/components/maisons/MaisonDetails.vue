<template>
    <div
        v-if="show && maison"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
    >
        <div
            class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white"
        >
            <!-- En-tête -->
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">
                    Détails de la maison
                </h3>
                <button
                    @click="$emit('close')"
                    class="text-gray-400 hover:text-gray-600 focus:outline-none"
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
                        ></path>
                    </svg>
                </button>
            </div>

            <!-- Contenu principal -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Images -->
                <div class="space-y-4">
                    <div class="relative">
                        <img
                            :src="
                                maison.image_url ||
                                '/placeholder-house.jpg'
                            "
                            alt="Image principale"
                            :alt="maison.titre"
                            class="w-full h-64 object-cover rounded-lg shadow-md"
                        />
                        <div
                            v-if="maison.prix"
                            class="absolute top-4 right-4 bg-blue-600 text-white px-3 py-1 rounded-full font-semibold"
                        >
                            {{ formatPrix(maison.prix) }}
                        </div>
                    </div>

                    <!-- Galerie d'images secondaires -->
                    <div
                        v-if="maison.images && maison.images.length > 0"
                        class="grid grid-cols-3 gap-2"
                    >
                        <img
                            v-for="(image, index) in maison.images.slice(0, 3)"
                            :key="index"
                            :src="image.url || '/storage/${image.path}'"
                            :alt="`Image ${index + 1}`"
                            class="w-full h-20 object-cover rounded cursor-pointer hover:opacity-80 transition-opacity"
                            @click=" mainImage =
                                    image.url || '/storage/${image.path}'"
                        />
                    </div>
                </div>

                <!-- Informations -->
                <div class="space-y-6">
                    <!-- Titre et localisation -->
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">
                            {{ maison.titre }}
                        </h2>
                        <div class="flex items-center text-gray-600 mb-4">
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
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                ></path>
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                ></path>
                            </svg>
                            {{ maison.adresse || "Adresse non spécifiée" }}
                        </div>
                    </div>

                    <!-- Caractéristiques principales -->
                    <div class="grid grid-cols-2 gap-4">
                        <div
                            v-if="maison.chambres"
                            class="bg-gray-50 p-4 rounded-lg"
                        >
                            <div class="flex items-center">
                                <svg
                                    class="w-6 h-6 text-gray-600 mr-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"
                                    ></path>
                                </svg>
                                <div>
                                    <p class="text-sm text-gray-600">
                                        Chambres
                                    </p>
                                    <p class="font-semibold">
                                        {{ maison.chambres }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="maison.salles_bain"
                            class="bg-gray-50 p-4 rounded-lg"
                        >
                            <div class="flex items-center">
                                <svg
                                    class="w-6 h-6 text-gray-600 mr-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"
                                    ></path>
                                </svg>
                                <div>
                                    <p class="text-sm text-gray-600">
                                        Salles de bain
                                    </p>
                                    <p class="font-semibold">
                                        {{ maison.salles_bain }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="maison.surface"
                            class="bg-gray-50 p-4 rounded-lg"
                        >
                            <div class="flex items-center">
                                <svg
                                    class="w-6 h-6 text-gray-600 mr-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"
                                    ></path>
                                </svg>
                                <div>
                                    <p class="text-sm text-gray-600">Surface</p>
                                    <p class="font-semibold">
                                        {{ maison.surface }} m²
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="maison.type"
                            class="bg-gray-50 p-4 rounded-lg"
                        >
                            <div class="flex items-center">
                                <svg
                                    class="w-6 h-6 text-gray-600 mr-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                    ></path>
                                </svg>
                                <div>
                                    <p class="text-sm text-gray-600">Type</p>
                                    <p class="font-semibold">
                                        {{ maison.type }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div v-if="maison.description">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">
                            Description
                        </h4>
                        <p class="text-gray-700 leading-relaxed">
                            {{ maison.description }}
                        </p>
                    </div>

                    <!-- Équipements -->
                    <div
                        v-if="
                            maison.equipements && maison.equipements.length > 0
                        "
                    >
                        <h4 class="text-lg font-semibold text-gray-900 mb-3">
                            Équipements
                        </h4>
                        <div class="grid grid-cols-2 gap-2">
                            <div
                                v-for="equipement in maison.equipements"
                                :key="equipement"
                                class="flex items-center text-gray-700"
                            >
                                <svg
                                    class="w-4 h-4 text-green-500 mr-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7"
                                    ></path>
                                </svg>
                                {{ equipement }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations supplémentaires -->
            <div class="mt-8 border-t pt-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Informations énergétiques -->
                    <div v-if="maison.dpe" class="text-center">
                        <h5 class="font-semibold text-gray-900 mb-2">DPE</h5>
                        <div
                            class="inline-block px-4 py-2 rounded-full text-white font-bold"
                            :class="getDpeColor(maison.dpe)"
                        >
                            {{ maison.dpe }}
                        </div>
                    </div>

                    <!-- Date de construction -->
                    <div v-if="maison.annee_construction" class="text-center">
                        <h5 class="font-semibold text-gray-900 mb-2">
                            Année de construction
                        </h5>
                        <p class="text-gray-700">
                            {{ maison.annee_construction }}
                        </p>
                    </div>

                    <!-- Agent/Contact -->
                    <div v-if="maison.agent" class="text-center">
                        <h5 class="font-semibold text-gray-900 mb-2">
                            Contact
                        </h5>
                        <p class="text-gray-700">{{ maison.agent.nom }}</p>
                        <p class="text-sm text-gray-600">
                            {{ maison.agent.telephone }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex justify-end space-x-4">
                <button
                    @click="$emit('close')"
                    class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                >
                    Fermer
                </button>
                <button
                    v-if="!hideFavoriteButton"
                    @click="toggleFavorite"
                    class="px-6 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                    :class="
                        isFavorite
                            ? 'bg-red-600 hover:bg-red-700 text-white'
                            : 'bg-blue-600 hover:bg-blue-700 text-white'
                    "
                >
                    {{
                        isFavorite
                            ? "Retirer des favoris"
                            : "Ajouter aux favoris"
                    }}
                </button>
                <button
                    v-if="!hideContactButton && userRole !== 'proprietaire'"
                    @click="$emit('contact', maison)"
                    class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors"
                >
                    Contacter
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "MaisonDetailsModal",
    props: {
        show: {
            type: Boolean,
            default: false,
        },
        maison: {
            type: Object,
            default: null,
        },
        userRole: {
            type: String,
            default: "",
        },
        hideFavoriteButton: {
            type: Boolean,
            default: false,
        },
        hideContactButton: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            mainImage: "",
        };
    },
    watch: {
        maison: {
            handler(newMaison) {
                if (newMaison) {
                    this.checkIfFavorite();
                }
            },
            immediate: true,
        },
    },
    methods: {
        formatPrix(prix) {
            return new Intl.NumberFormat("fr-FR", {
                style: "currency",
                currency: "EUR",
            }).format(prix);
        },

        getDpeColor(dpe) {
            const colors = {
                A: "bg-green-500",
                B: "bg-green-400",
                C: "bg-yellow-500",
                D: "bg-yellow-600",
                E: "bg-orange-500",
                F: "bg-red-500",
                G: "bg-red-600",
            };
            return colors[dpe] || "bg-gray-500";
        },

        toggleFavorite() {
            this.isFavorite = !this.isFavorite;
            this.$emit("toggle-favorite", {
                maison: this.maison,
                isFavorite: this.isFavorite,
            });
        },

        checkIfFavorite() {
            // Logique pour vérifier si la maison est déjà en favori
            // À adapter selon votre système de gestion des favoris
            const favoris = JSON.parse(localStorage.getItem("favoris") || "[]");
            this.isFavorite = favoris.some((fav) => fav.id === this.maison?.id);
        },

        ouvrirGalerie(index) {
            this.$emit("open-gallery", {
                images: this.maison.images,
                startIndex: index,
            });
        },
    },
    emits: ["close", "toggle-favorite", "contact", "open-gallery"],
};
</script>

<style scoped>
/* Animations et transitions personnalisées */
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

/* Scrollbar personnalisée */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
