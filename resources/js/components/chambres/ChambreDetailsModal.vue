<template>
    <div
        v-if="show"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
    >
        <div
            class="relative bg-white rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden"
        >
            <!-- En-tête -->
            <div class="flex justify-between items-center p-5 border-b">
                <h3 class="text-xl font-bold text-gray-900">
                    Détails de la chambre
                </h3>
                <button
                    @click="$emit('close')"
                    class="text-gray-400 hover:text-gray-600"
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

            <!-- Contenu -->
            <div class="overflow-y-auto max-h-[80vh] p-5">
                <!-- Galerie d'images -->
                <div class="mb-6">
                    <div
                        class="relative h-80 bg-gray-100 rounded-lg overflow-hidden"
                    >
                        <img
                            :src="currentImage"
                            :alt="chambre.titre"
                            class="w-full h-full object-cover"
                        />
                        <div
                            class="absolute bottom-4 left-4 bg-blue-600 text-white px-3 py-1 rounded-full font-semibold"
                        >
                            {{ formatPrix(chambre.prix) }}/mois
                        </div>
                        <div
                            class="absolute top-4 right-4 bg-green-500 text-white px-2 py-1 text-xs rounded-full"
                        >
                            Disponible
                        </div>
                    </div>

                    <div
                        v-if="chambre.medias && chambre.medias.length > 1"
                        class="grid grid-cols-4 gap-2 mt-3"
                    >
                        <div
                            v-for="(media, index) in chambre.medias"
                            :key="index"
                            @click="
                                currentImage =
                                    media.url || asset('storage/' + media.path)
                            "
                            :class="{
                                'ring-2 ring-blue-500':
                                    currentImage ===
                                    (media.url ||
                                        asset('storage/' + media.path)),
                            }"
                            class="cursor-pointer h-20 rounded-md overflow-hidden"
                        >
                            <img
                                :src="
                                    media.url || asset('storage/' + media.path)
                                "
                                :alt="`Image ${index + 1}`"
                                class="w-full h-full object-cover"
                            />
                        </div>
                    </div>
                </div>

                <!-- Informations principales -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
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
                                "Adresse non disponible"
                            }}
                        </div>

                        <p
                            v-if="chambre.description"
                            class="text-gray-700 mb-6"
                        >
                            {{ chambre.description }}
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-5">
                        <h3 class="font-bold text-lg text-gray-900 mb-4">
                            Caractéristiques
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex items-center">
                                <svg
                                    class="w-6 h-6 text-blue-600 mr-2"
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
                                        {{ chambre.taille || "N/A" }} m²
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <svg
                                    class="w-6 h-6 text-blue-600 mr-2"
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
                                    <p class="text-sm text-gray-600">Prix</p>
                                    <p class="font-semibold">
                                        {{ formatPrix(chambre.prix) }}/mois
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <svg
                                    class="w-6 h-6 text-blue-600 mr-2"
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
                                        {{ chambre.type || "N/A" }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <svg
                                    class="w-6 h-6 text-blue-600 mr-2"
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

                            <div class="flex items-center">
                                <svg
                                    class="w-6 h-6 text-blue-600 mr-2"
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
                </div>

                <!-- Maison associée -->
                <div class="mb-8">
                    <h3 class="font-bold text-lg text-gray-900 mb-4">Maison</h3>
                    <div class="bg-gray-50 rounded-xl p-5">
                        <h4 class="font-semibold text-gray-800">
                            {{
                                chambre.maison?.adresse ||
                                "Adresse non disponible"
                            }}
                        </h4>
                        <p
                            v-if="chambre.maison?.description"
                            class="text-gray-600 mt-2"
                        >
                            {{ chambre.maison.description }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between p-5 border-t">
                <button
                    @click="$emit('close')"
                    class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
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
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ChambreDetailsModal",
    props: {
        show: Boolean,
        chambre: Object,
        userRole: String,
    },
    emits: ["close", "reserve"],
    data() {
        return {
            currentImage: "",
        };
    },
    watch: {
        chambre: {
            immediate: true,
            handler(newChambre) {
                if (
                    newChambre &&
                    newChambre.medias &&
                    newChambre.medias.length > 0
                ) {
                    this.currentImage =
                        newChambre.medias[0].url ||
                        this.asset("storage/" + newChambre.medias[0].path);
                }
            },
        },
    },
    methods: {
        formatPrix(prix) {
            return new Intl.NumberFormat("fr-FR", {
                style: "currency",
                currency: "EUR",
                maximumFractionDigits: 0,
            }).format(prix);
        },

        asset(path) {
            return `${window.location.origin}/${path}`;
        },
    },
};
</script>
