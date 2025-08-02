<template>
    <div
        v-if="show"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
    >
        <div
            class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white"
        >
            <!-- En-tête -->
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">
                    {{
                        isEditing
                            ? "Modifier la maison"
                            : "Ajouter une nouvelle maison"
                    }}
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

            <!-- Formulaire -->
            <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Adresse -->
                <div>
                    <label
                        for="adresse"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Adresse *
                    </label>
                    <input
                        id="adresse"
                        v-model.trim="form.adresse"
                        type="text"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="{ 'border-red-500': errors.adresse }"
                        placeholder="Entrez l'adresse complète"
                    />
                    <p v-if="errors.adresse" class="mt-1 text-sm text-red-600">
                        {{ errors.adresse[0] }}
                    </p>
                </div>

                <!-- Description -->
                <div>
                    <label
                        for="description"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Description
                    </label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="{ 'border-red-500': errors.description }"
                        placeholder="Description de la maison (optionnel)"
                    ></textarea>
                    <p
                        v-if="errors.description"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ errors.description[0] }}
                    </p>
                </div>

                <!-- Nombre de chambres -->
                <div>
                    <label
                        for="nombre_chambres"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Nombre de chambres
                    </label>
                    <input
                        id="nombre_chambres"
                        v-model="form.nombre_chambres"
                        type="number"
                        min="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="{ 'border-red-500': errors.nombre_chambres }"
                        placeholder="Ex: 3"
                    />
                    <p
                        v-if="errors.nombre_chambres"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ errors.nombre_chambres[0] }}
                    </p>
                </div>

                <!-- Coordonnées géographiques -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label
                            for="latitude"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Latitude
                        </label>
                        <input
                            id="latitude"
                            v-model="form.latitude"
                            type="number"
                            step="any"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :class="{ 'border-red-500': errors.latitude }"
                            placeholder="Ex: 14.6928"
                        />
                        <p
                            v-if="errors.latitude"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ errors.latitude[0] }}
                        </p>
                    </div>
                    <div>
                        <label
                            for="longitude"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Longitude
                        </label>
                        <input
                            id="longitude"
                            v-model="form.longitude"
                            type="number"
                            step="any"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :class="{ 'border-red-500': errors.longitude }"
                            placeholder="Ex: -17.4467"
                        />
                        <p
                            v-if="errors.longitude"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ errors.longitude[0] }}
                        </p>
                    </div>
                </div>

                <!-- Image -->
                <div>
                    <label
                        for="image"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Image de la maison
                    </label>

                    <!-- Zone de glisser-déposer -->
                    <div
                        @drop="handleDrop"
                        @dragover.prevent
                        @dragenter.prevent
                        class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition-colors"
                        :class="{ 'border-blue-500 bg-blue-50': isDragging }"
                    >
                        <input
                            ref="fileInput"
                            type="file"
                            accept="image/*"
                            @change="handleFileSelect"
                            class="hidden"
                        />

                        <div v-if="!form.image && !imagePreview">
                            <svg
                                class="mx-auto h-12 w-12 text-gray-400"
                                stroke="currentColor"
                                fill="none"
                                viewBox="0 0 48 48"
                            >
                                <path
                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">
                                <button
                                    type="button"
                                    @click="$refs.fileInput.click()"
                                    class="font-medium text-blue-600 hover:text-blue-500"
                                >
                                    Télécharger une image
                                </button>
                                ou glisser-déposer
                            </p>
                            <p class="text-xs text-gray-500">
                                PNG, JPG, GIF jusqu'à 2MB
                            </p>
                        </div>

                        <!-- Prévisualisation de l'image -->
                        <div v-else class="relative">
                            <img
                                :src="imagePreview || form.image_url"
                                alt="Prévisualisation"
                                class="mx-auto max-h-32 rounded-lg"
                            />
                            <button
                                type="button"
                                @click="removeImage"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none"
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
                                        d="M6 18L18 6M6 6l12 12"
                                    ></path>
                                </svg>
                            </button>
                            <p class="mt-2 text-sm text-gray-600">
                                <button
                                    type="button"
                                    @click="$refs.fileInput.click()"
                                    class="font-medium text-blue-600 hover:text-blue-500"
                                >
                                    Changer l'image
                                </button>
                            </p>
                        </div>
                    </div>
                    <p v-if="errors.image" class="mt-1 text-sm text-red-600">
                        {{ errors.image[0] }}
                    </p>
                </div>

                <!-- Statut actif -->
                <div class="flex items-center">
                    <input
                        id="active"
                        v-model="form.active"
                        type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    />
                    <label
                        for="active"
                        class="ml-2 block text-sm text-gray-900"
                    >
                        Maison active
                    </label>
                </div>

                <!-- Boutons d'action -->
                <div class="flex justify-end space-x-3 pt-6 border-t">
                    <button
                        type="button"
                        @click="$emit('close')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                    >
                        Annuler
                    </button>
                    <button
                        type="submit"
                        :disabled="loading"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="loading" class="flex items-center">
                            <svg
                                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                            Sauvegarde...
                        </span>
                        <span v-else>
                            {{ isEditing ? "Mettre à jour" : "Créer" }}
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { ref, reactive, computed, watch } from "vue";

// Fonction pour obtenir le token CSRF
const getCsrfToken = () => {
    // Méthode 1: Depuis une meta tag
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    if (metaTag) {
        return metaTag.getAttribute("content");
    }

    // Méthode 2: Depuis un cookie (si configuré)
    const cookies = document.cookie.split(";");
    for (let cookie of cookies) {
        const [name, value] = cookie.trim().split("=");
        if (name === "XSRF-TOKEN") {
            return decodeURIComponent(value);
        }
    }

    return null;
};

// Service pour les appels API
const maisonService = {
    async createMaison(formData) {
        const csrfToken = getCsrfToken();
        const headers = {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
            "X-Requested-With": "XMLHttpRequest",
        };

        // Ajouter le token CSRF selon la méthode disponible
        if (csrfToken) {
            headers["X-CSRF-TOKEN"] = csrfToken;
        }

        const response = await fetch("/api/maisons", {
            method: "POST",
            headers: headers,
            body: formData,
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return { data: await response.json() };
    },

    async updateMaison(maisonId, formData) {
        const csrfToken = getCsrfToken();
        const headers = {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
            "X-Requested-With": "XMLHttpRequest",
        };

        if (csrfToken) {
            headers["X-CSRF-TOKEN"] = csrfToken;
        }

        const response = await fetch(`/api/maisons/${maisonId}`, {
            method: "POST",
            headers: headers,
            body: formData,
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return { data: await response.json() };
    },
};

// Composable pour les notifications
const useToast = () => {
    const showToast = (message, type = "info") => {
        if (type === "error") {
            console.error(message);
            alert(`Erreur: ${message}`);
        } else if (type === "success") {
            console.log(message);
            alert(`Succès: ${message}`);
        } else {
            console.info(message);
            alert(message);
        }
    };

    return { showToast };
};

export default {
    name: "MaisonForm",
    props: {
        show: {
            type: Boolean,
            default: false,
        },
        maison: {
            type: Object,
            default: null,
        },
        isEditing: {
            type: Boolean,
            default: false,
        },
        userRole: {
            type: String,
            default: "",
        },
    },
    emits: ["close", "saved"],
    setup(props, { emit }) {
        const { showToast } = useToast();

        const loading = ref(false);
        const errors = ref({});
        const isDragging = ref(false);
        const imagePreview = ref(null);
        const selectedFile = ref(null);

        const form = reactive({
            adresse: "",
            description: "",
            nombre_chambres: null,
            latitude: null,
            longitude: null,
            image: null,
            active: true,
        });

        const resetForm = () => {
            Object.assign(form, {
                adresse: "",
                description: "",
                nombre_chambres: null,
                latitude: null,
                longitude: null,
                image: null,
                active: true,
            });
            imagePreview.value = null;
            selectedFile.value = null;
            errors.value = {};
        };

        // Watcher pour initialiser le formulaire
        watch(
            () => props.maison,
            (newMaison) => {
                if (newMaison && props.isEditing) {
                    Object.assign(form, {
                        adresse: newMaison.adresse || "",
                        description: newMaison.description || "",
                        nombre_chambres: newMaison.nombre_chambres || null,
                        latitude: newMaison.latitude || null,
                        longitude: newMaison.longitude || null,
                        image: null,
                        active: newMaison.active ?? true,
                    });
                    imagePreview.value = null;
                    selectedFile.value = null;
                } else {
                    resetForm();
                }
            },
            { immediate: true }
        );

        // Empêcher l'ouverture du formulaire si l'utilisateur n'est pas propriétaire
        watch(
            () => props.show,
            (newShow) => {
                if (newShow && props.userRole !== "proprietaire") {
                    showToast("Accès refusé : vous n'êtes pas propriétaire.", "error");
                    emit("close");
                }
            }
        );

        const handleFileSelect = (event) => {
            const file = event.target.files[0];
            if (file) {
                // Vérification de la taille (2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    showToast("L'image ne doit pas dépasser 2MB", "error");
                    return;
                }

                selectedFile.value = file;
                form.image = file;

                // Créer une prévisualisation
                const reader = new FileReader();
                reader.onload = (e) => {
                    imagePreview.value = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        };

        const handleDrop = (event) => {
            event.preventDefault();
            isDragging.value = false;

            const files = event.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                if (file.type.startsWith("image/")) {
                    // Vérification de la taille
                    if (file.size > 2 * 1024 * 1024) {
                        showToast("L'image ne doit pas dépasser 2MB", "error");
                        return;
                    }

                    selectedFile.value = file;
                    form.image = file;

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        imagePreview.value = e.target.result;
                    };
                    reader.readAsDataURL(file);
                } else {
                    showToast(
                        "Veuillez sélectionner une image valide",
                        "error"
                    );
                }
            }
        };

        const removeImage = () => {
            form.image = null;
            imagePreview.value = null;
            selectedFile.value = null;
        };

        const submitForm = async () => {
            loading.value = true;
            errors.value = {};

            try {
                const formData = new FormData();

                // Ajouter les champs du formulaire
                formData.append("adresse", form.adresse);
                if (form.description)
                    formData.append("description", form.description);
                if (form.nombre_chambres)
                    formData.append("nombre_chambres", form.nombre_chambres);
                if (form.latitude) formData.append("latitude", form.latitude);
                if (form.longitude)
                    formData.append("longitude", form.longitude);
                if (form.image) formData.append("image", form.image);
                formData.append("active", form.active ? "1" : "0");

                let response;
                if (props.isEditing) {
                    // Mise à jour
                    formData.append("_method", "PUT");
                    response = await maisonService.updateMaison(
                        props.maison.id,
                        formData
                    );
                } else {
                    // Création
                    response = await maisonService.createMaison(formData);
                }

                if (response.data.success) {
                    emit("saved", response.data.data);
                    resetForm();
                    showToast("Maison sauvegardée avec succès!", "success");
                } else {
                    if (response.data.errors) {
                        errors.value = response.data.errors;
                    }
                    showToast(
                        response.data.message || "Une erreur est survenue",
                        "error"
                    );
                }
            } catch (error) {
                console.error("Erreur:", error);

                // Gestion spécifique des erreurs 422 (validation)
                if (error.response?.status === 422) {
                    errors.value = error.response.data.errors;
                    showToast("Corrigez les erreurs du formulaire.", "error");
                } else {
                    showToast("Erreur serveur: " + error.message, "error");
                }
            } finally {
                loading.value = false;
            }
        };

        return {
            form,
            loading,
            errors,
            isDragging,
            imagePreview,
            selectedFile,
            handleFileSelect,
            handleDrop,
            removeImage,
            submitForm,
            resetForm,
        };
    },
};
</script>
