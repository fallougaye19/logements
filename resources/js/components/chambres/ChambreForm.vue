<template>
    <div
        v-if="show"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
    >
        <div
            class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white"
        >
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">
                    {{
                        isEditing
                            ? "Modifier la chambre"
                            : "Ajouter une nouvelle chambre"
                    }}
                </h3>
                <button
                    @click="$emit('close')"
                    class="text-gray-400 hover:text-gray-600 focus:outline-none"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"
                        ></path>
                    </svg>
                </button>
            </div>

            <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Maison -->
                <div>
                    <label
                        for="maison_id"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Maison *
                    </label>
                    <select id="maison_id" v-model="form.maison_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        :class="{ 'border-red-500': errors.maison_id }"
                    >
                        <option value="" disabled>
                            Sélectionnez une maison
                        </option>
                        <option v-for="maison in maisons" :key="maison.id" :value="maison.id"
                        >
                            {{ maison.adresse }}
                        </option>
                    </select>
                    <p
                        v-if="errors.maison_id"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ errors.maison_id[0] }}
                    </p>
                </div>

                <!-- Titre -->
                <div>
                    <label
                        for="titre"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Titre *
                    </label>
                    <input id="titre" v-model.trim="form.titre" type="text" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        :class="{ 'border-red-500': errors.titre }"
                        placeholder="Titre de la chambre"
                    />
                    <p v-if="errors.titre" class="mt-1 text-sm text-red-600">
                        {{ errors.titre[0] }}
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
                    <textarea id="description" v-model="form.description" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        :class="{ 'border-red-500': errors.description }"
                        placeholder="Description de la chambre"
                    ></textarea>
                    <p
                        v-if="errors.description"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ errors.description[0] }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Taille -->
                    <div>
                        <label
                            for="taille"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Taille (m²)
                        </label>
                        <input id="taille" v-model="form.taille" type="number" min="0" step="0.1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="{ 'border-red-500': errors.taille }"
                            placeholder="Ex: 15.5"
                        />
                        <p
                            v-if="errors.taille"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ errors.taille[0] }}
                        </p>
                    </div>

                    <!-- Prix -->
                    <div>
                        <label
                            for="prix"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Prix mensuel (XOF) *
                        </label>
                        <input id="prix" v-model="form.prix" type="number" min="0" step="0.01" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="{ 'border-red-500': errors.prix }"
                            placeholder="Ex: 35000"
                        />
                        <p v-if="errors.prix" class="mt-1 text-sm text-red-600">
                            {{ errors.prix[0] }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Type -->
                    <div>
                        <label
                            for="type"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Type
                        </label>
                        <input id="type" v-model="form.type" type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="{ 'border-red-500': errors.type }"
                            placeholder="Ex: Chambre simple"
                        />
                        <p v-if="errors.type" class="mt-1 text-sm text-red-600">
                            {{ errors.type[0] }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Meublée -->
                    <div class="flex items-center">
                        <input id="meublee" v-model="form.meublee" type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        />
                        <label
                            for="meublee"
                            class="ml-2 block text-sm text-gray-900"
                        >
                            Meublée
                        </label>
                    </div>

                    <!-- Salle de bain -->
                    <div class="flex items-center">
                        <input id="salle_de_bain" v-model="form.salle_de_bain" type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        />
                        <label
                            for="salle_de_bain"
                            class="ml-2 block text-sm text-gray-900"
                        >
                            Salle de bain privée
                        </label>
                    </div>
                </div>

                <!-- Disponibilité -->
                <div class="flex items-center">
                    <input id="disponible" v-model="form.disponible" type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    />
                    <label
                        for="disponible"
                        class="ml-2 block text-sm text-gray-900"
                    >
                        Disponible
                    </label>
                </div>

                <!-- Images -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Images de la chambre
                    </label>

                    <div @drop="handleDrop" @dragover.prevent @dragenter.prevent
                        class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition-colors"
                        :class="{ 'border-blue-500 bg-blue-50': isDragging }"
                    >
                        <input ref="fileInput" type="file" multiple accept="image/*"
                            @change="handleFileSelect"
                            class="hidden"
                        />

                        <div v-if="!uploadedImages.length">
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
                                    Télécharger des images
                                </button>
                                ou glisser-déposer
                            </p>
                            <p class="text-xs text-gray-500">
                                PNG, JPG, GIF jusqu'à 2MB par image
                            </p>
                        </div>

                        <div v-else class="grid grid-cols-3 gap-4">
                            <div
                                v-for="(image, index) in uploadedImages"
                                :key="index"
                                class="relative"
                            >
                                <img
                                    :src="image.preview || image.url"
                                    alt="Prévisualisation"
                                    class="w-full h-32 object-cover rounded-lg"
                                />
                                <button
                                    type="button"
                                    @click="removeImage(index)"
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
                            </div>
                        </div>
                    </div>
                    <p v-if="errors.images" class="mt-1 text-sm text-red-600">
                        {{ errors.images[0] }}
                    </p>
                </div>

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
import { ref, reactive, computed, watch, onMounted } from "vue";

const getCsrfToken = () => {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    if (metaTag) return metaTag.getAttribute("content");

    const cookies = document.cookie.split(";");
    for (let cookie of cookies) {
        const [name, value] = cookie.trim().split("=");
        if (name === "XSRF-TOKEN") return decodeURIComponent(value);
    }

    return null;
};

const chambreService = {
    async createChambre(formData) {
        const csrfToken = getCsrfToken();
        const headers = {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
            "X-Requested-With": "XMLHttpRequest",
        };

        if (csrfToken) headers["X-CSRF-TOKEN"] = csrfToken;

        const response = await fetch("/api/chambres", {
            method: "POST",
            headers: headers,
            body: formData,
        });

        return { data: await response.json() };
    },

    async updateChambre(chambreId, formData) {
        const csrfToken = getCsrfToken();
        const headers = {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
            "X-Requested-With": "XMLHttpRequest",
        };

        if (csrfToken) headers["X-CSRF-TOKEN"] = csrfToken;

        const response = await fetch(`/api/chambres/${chambreId}`, {
            method: "POST",
            headers: headers,
            body: formData,
        });

        return { data: await response.json() };
    },

    async getMaisons() {
        const response = await fetch("/api/maisons/select", {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${localStorage.getItem("token")}`,
            },
        });
        return { data: await response.json() };
    },
};

const useToast = () => {
    const showToast = (message, type = "info") => {
        if (type === "error") alert(`Erreur: ${message}`);
        else if (type === "success") alert(`Succès: ${message}`);
        else alert(message);
    };

    return { showToast };
};

export default {
    name: "ChambreForm",
    props: {
        show: Boolean,
        chambre: Object,
        isEditing: Boolean,
    },
    emits: ["close", "saved"],
    setup(props, { emit }) {
        const { showToast } = useToast();
        const loading = ref(false);
        const errors = ref({});
        const isDragging = ref(false);
        const uploadedImages = ref([]);
        const maisons = ref([]);

        const form = reactive({
            maison_id: "", titre: "", description: "", taille: null, type: "",
            meublee: false, salle_de_bain: false, prix: null, disponible: true, images: [],
        });

        const resetForm = () => {
            Object.assign(form, {
                maison_id: "", titre: "", description: "", taille: null, type: "",
                meublee: false, salle_de_bain: false, prix: null, disponible: true, images: [],
            });
            uploadedImages.value = [];
            errors.value = {};
        };

        watch(
            () => props.chambre,
            (newChambre) => {
                if (newChambre && props.isEditing) {
                    Object.assign(form, {
                        maison_id: newChambre.maison_id,
                        titre: newChambre.titre,
                        description: newChambre.description,
                        taille: newChambre.taille,
                        type: newChambre.type,
                        meublee: newChambre.meublee,
                        salle_de_bain: newChambre.salle_de_bain,
                        prix: newChambre.prix,
                        disponible: newChambre.disponible,
                        images: [],
                    });

                    // Charger les images existantes
                    if (newChambre.medias && newChambre.medias.length) {
                        uploadedImages.value = newChambre.medias.map(
                            (media) => ({id: media.id, url: media.url, path: media.path,})
                        );
                    } else {
                        uploadedImages.value = [];
                    }
                } else {
                    resetForm();
                }
            },
            { immediate: true }
        );

        onMounted(async () => {
            try {
                const token = localStorage.getItem("token");
                const response = await fetch("/api/maisons", {
                    method: "GET",
                    headers: {
                        Authorization: `Bearer ${token}`,
                        Accept: "application/json",
                    },
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        maisons.value = result.data;
                    } else {
                        console.error("Erreur API:", result.message);
                    }
                } else {
                    throw new Error(`Erreur HTTP ${response.status}`);
                }
            } catch (error) {
                console.error(
                    "Erreur lors du chargement des maisons",
                    error.message
                );
            }
        });

        const handleFileSelect = (event) => {
            const files = Array.from(event.target.files);
            if (!files.length) return;

            files.forEach((file) => {
                if (file.size > 2 * 1024 * 1024) {
                    showToast("L'image ne doit pas dépasser 2MB", "error");
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    uploadedImages.value.push({
                        file,
                        preview: e.target.result,
                        isNew: true,
                    });
                };
                reader.readAsDataURL(file);
            });
        };

        const handleDrop = (event) => {
            event.preventDefault();
            isDragging.value = false;
            const files = Array.from(event.dataTransfer.files);

            files.forEach((file) => {
                if (!file.type.startsWith("image/")) {
                    showToast(
                        "Veuillez sélectionner des images valides",
                        "error"
                    );
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    showToast("L'image ne doit pas dépasser 2MB", "error");
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    uploadedImages.value.push({
                        file,
                        preview: e.target.result,
                        isNew: true,
                    });
                };
                reader.readAsDataURL(file);
            });
        };

        const removeImage = (index) => {
            uploadedImages.value.splice(index, 1);
        };

        const submitForm = async () => {
            loading.value = true;
            errors.value = {};

            try {
                const formData = new FormData();
                formData.append("maison_id", form.maison_id);
                formData.append("titre", form.titre);
                formData.append("description", form.description);
                if (form.taille) formData.append("taille", form.taille);
                if (form.type) formData.append("type", form.type);
                formData.append("meublee", form.meublee ? "1" : "0");
                formData.append(
                    "salle_de_bain",
                    form.salle_de_bain ? "1" : "0"
                );
                formData.append("prix", form.prix);
                formData.append("disponible", form.disponible ? "1" : "0");

                // Ajouter les nouvelles images
                uploadedImages.value.forEach((img, index) => {
                    if (img.isNew) {
                        formData.append(`images[${index}]`, img.file);
                    }
                });

                let response;
                if (props.isEditing) {
                    formData.append("_method", "PUT");
                    response = await chambreService.updateChambre(props.chambre.id,formData);
                } else {
                    response = await chambreService.createChambre(formData);
                }
                if (response.data.success) {
                    emit("saved", response.data.data);
                    resetForm();
                    showToast("Chambre sauvegardée avec succès!", "success");
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
                showToast("Erreur serveur: " + error.message, "error");
            } finally {
                loading.value = false;
            }
        };

        return {
            form,maisons,loading,errors,isDragging,uploadedImages,handleFileSelect,handleDrop,
            removeImage,submitForm,resetForm,
        };
    },
};
</script>
