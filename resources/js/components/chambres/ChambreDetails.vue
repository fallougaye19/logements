<template>
    <div
        v-if="show && chambre"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
    >
        <div
            class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white"
        >
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">
                    Détails de la chambre
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

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Images -->
                <div class="space-y-4">
                    <div class="relative">
                        <img
                            :src="mainImage"
                            :alt="chambre.titre"
                            class="w-full h-64 object-cover rounded-lg shadow-md"
                        />
                        <div
                            class="absolute bottom-4 left-4 bg-blue-600 text-white px-3 py-1 rounded-full font-semibold"
                        >
                            {{ formatPrix(chambre.prix) }}
                        </div>
                        <div class="absolute top-4 right-4">
                            <span
                                :class="
                                    chambre.disponible
                                        ? 'bg-green-500'
                                        : 'bg-red-500'
                                "
                                class="px-2 py-1 text-xs text-white rounded-full"
                            >
                                {{
                                    chambre.disponible
                                        ? "Disponible"
                                        : "Non disponible"
                                }}
                            </span>
                        </div>
                    </div>

                    <div
                        v-if="chambre.medias && chambre.medias.length > 1"
                        class="grid grid-cols-3 gap-2"
                    >
                        <img
                            v-for="(image, index) in chambre.medias"
                            :key="index"
                            :src="image.url || asset('storage/' + image.path)"
                            :alt="`Image ${index + 1}`"
                            class="w-full h-20 object-cover rounded cursor-pointer hover:opacity-80 transition-opacity"
                            @click="
                                mainImage =
                                    image.url || asset('storage/' + image.path)
                            "
                        />
                    </div>
                </div>

                <!-- Informations -->
                <div class="space-y-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">
                            {{ chambre.titre }}
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
                            {{
                                chambre.maison?.adresse ||
                                "Adresse non spécifiée"
                            }}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
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
                                    <p class="text-sm text-gray-600">Taille</p>
                                    <p class="font-semibold">
                                        {{ chambre.taille }} m²
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
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
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    ></path>
                                </svg>
                                <div>
                                    <p class="text-sm text-gray-600">
                                        Prix mensuel
                                    </p>
                                    <p class="font-semibold">
                                        {{ formatPrix(chambre.prix) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="chambre.type"
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
                                        {{ chambre.type }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
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
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                    ></path>
                                </svg>
                                <div>
                                    <p class="text-sm text-gray-600">Meublée</p>
                                    <p class="font-semibold">
                                        {{ chambre.meublee ? "Oui" : "Non" }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
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
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    ></path>
                                </svg>
                                <div>
                                    <p class="text-sm text-gray-600">
                                        Salle de bain
                                    </p>
                                    <p class="font-semibold">
                                        {{
                                            chambre.salle_de_bain
                                                ? "Privée"
                                                : "Partagée"
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="chambre.description">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">
                            Description
                        </h4>
                        <p class="text-gray-700 leading-relaxed">
                            {{ chambre.description }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-4">
                <button
                    @click="$emit('close')"
                    class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                >
                    Fermer
                </button>
                <button
                    v-if="userRole === 'locataire' && chambre.disponible"
                    @click="$emit('reserve', chambre)"
                    class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium"
                >
                    Réserver cette chambre
                </button>
                <button
                    v-if="userRole === 'locataire' && chambre.disponible"
                    @click="showRendezVousForm = true"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium"
                >
                    Demander un rendez-vous
                </button>
            </div>
        </div>
        <RendezVousForm
            v-if="showRendezVousForm"
            :chambre-id="chambre.id"
            @close="showRendezVousForm = false"
        />
    </div>
</template>

<script>
import RendezVousForm from "@/components/rendezvous/RendezVousForm.vue";
export default {
    name: "ChambreDetails",
    props: {
        show: Boolean,
        chambre: Object,
        userRole: String,
    },
    emits: ["close", "reserve"],
    data() {
        return {
            mainImage: "",
            showRendezVousForm: false,
        };
    },

    components: {
        RendezVousForm,
    },

    watch: {
        chambre: {
            handler(newChambre) {
                if (newChambre) {
                    this.setMainImage();
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

        setMainImage() {
            if (this.chambre.medias && this.chambre.medias.length > 0) {
                this.mainImage =
                    this.chambre.medias[0].url ||
                    this.asset("storage/" + this.chambre.medias[0].path);
            } else {
                this.mainImage = "/placeholder-room.jpg";
            }
        },

        asset(path) {
            return `${window.location.origin}/${path}`;
        },
    },
};
</script>
