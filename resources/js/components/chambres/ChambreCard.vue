<template>
    <div
        class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 border border-gray-100"
    >
        <!-- Image -->
        <div class="h-56 bg-gray-100 relative">
            <img
                v-if="chambre.image_url"
                :src="chambre.image_url"
                :alt="chambre.titre"
                class="w-full h-full object-cover"
            />
            <div
                v-else
                class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-500"
            >
                <svg
                    class="w-12 h-12"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                    ></path>
                </svg>
            </div>
            <div
                class="absolute top-2 right-2 px-2 py-1 text-xs font-bold rounded-full"
                :class="
                    chambre.disponible
                        ? 'bg-green-100 text-green-800'
                        : 'bg-red-100 text-red-800'
                "
            >
                {{ chambre.disponible ? "Disponible" : "Loué" }}
            </div>
            <div
                class="absolute bottom-3 left-3 px-3 py-1 bg-blue-600 text-white rounded-full text-sm font-semibold"
            >
                {{ formatPrix(chambre.prix) }}/mois
            </div>
        </div>

        <!-- Contenu -->
        <div class="p-5">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-bold text-lg text-gray-900 mb-1">
                        {{ chambre.titre }}
                    </h3>
                    <div class="flex items-center text-gray-600 text-sm mb-3">
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
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                            ></path>
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                            ></path>
                        </svg>
                        <span>{{
                            chambre.maison?.adresse || "Adresse non disponible"
                        }}</span>
                    </div>
                </div>
                <div
                    v-if="chambre.taille"
                    class="bg-blue-50 px-2 py-1 rounded-lg text-blue-800 text-sm font-medium"
                >
                    {{ chambre.taille }} m²
                </div>
            </div>

            <p
                v-if="chambre.description"
                class="text-gray-600 text-sm mb-4 line-clamp-2"
            >
                {{ chambre.description }}
            </p>

            <!-- Caractéristiques -->
            <div class="grid grid-cols-2 gap-2 mb-4">
                <div class="flex items-center text-sm text-gray-700">
                    <svg
                        class="w-4 h-4 mr-2 text-green-600"
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
                    {{ chambre.type || "Type non spécifié" }}
                </div>
                <div class="flex items-center text-sm text-gray-700">
                    <svg
                        class="w-4 h-4 mr-2 text-green-600"
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
                    {{ chambre.meublee ? "Meublée" : "Non meublée" }}
                </div>
                <div class="flex items-center text-sm text-gray-700">
                    <svg
                        class="w-4 h-4 mr-2 text-green-600"
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
                    {{
                        chambre.salle_de_bain
                            ? "Salle de bain privée"
                            : "SDB partagée"
                    }}
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between mt-4">
                <button
                    @click="$emit('view-details', chambre)"
                    class="text-blue-600 hover:text-blue-800 font-medium flex items-center"
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
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        ></path>
                    </svg>
                    Détails
                </button>
                <button
                    v-if="userRole === 'locataire' && chambre.disponible"
                    @click="$emit('reserve', chambre)"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
                >
                    Réserver
                </button>
                <span
                    v-else-if="userRole === 'locataire' && chambre.reservation"
                    class="text-yellow-600 text-sm"
                >
                    En attente...
                </span>

                <!-- Boutons propriétaire -->
                <div v-if="userRole === 'proprietaire'" class="flex gap-2">
                    <button
                        @click="$emit('edit', chambre)"
                        class="text-green-600 hover:text-green-800 font-medium"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                            />
                        </svg>
                    </button>
                    <button
                        @click="$emit('toggle-disponibilite', chambre)"
                        class="bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-2 py-2 rounded text-sm"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"
                            />
                        </svg>
                    </button>
                    <button
                        @click="$emit('delete', chambre)"
                        class="text-red-600 hover:text-red-800 font-medium"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                            />
                        </svg>
                    </button>
                </div>

                <!-- Boutons Accepter/Refuser pour le propriétaire -->
                <div
                    v-if="
                        userRole === 'proprietaire' &&
                        chambre.reservation?.statut === 'en_attente'
                    "
                    class="mt-4 space-x-2"
                >
                    <button
                        @click="accepterReservation(chambre)"
                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm"
                    >
                        Accepter
                    </button>
                    <button
                        @click="refuserReservation(chambre)"
                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm"
                    >
                        Refuser
                    </button>
                </div>

                <!-- Message si réservée -->
                <div
                    v-else-if="
                        userRole === 'proprietaire' &&
                        chambre.reservation?.statut === 'acceptee'
                    "
                    class="mt-4 text-green-600"
                >
                    Réservée par {{ chambre.reservation.locataire?.name }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ChambreCard",
    props: {
        chambre: {
            type: Object,
            required: true,
        },
        userRole: {
            type: String,
            required: true,
        },
    },
    emits: [
        "view-details",
        "edit",
        "toggle-disponibilite",
        "delete",
        "reserve",
    ],
    methods: {
        formatPrix(prix) {
            return new Intl.NumberFormat("fr-FR", {
                style: "currency",
                currency: "EUR",
                maximumFractionDigits: 0,
            }).format(prix);
        },
    },
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
