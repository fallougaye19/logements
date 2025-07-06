<template>
    <div class="p-8 bg-gray-100 min-h-screen flex justify-center items-start">
        <form
            @submit.prevent="submit"
            class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-xl space-y-6"
        >
            <h2 class="text-2xl font-bold text-blue-800 text-center">
                {{ editing ? "Modifier la maison" : "Nouvelle maison" }}
            </h2>

            <!-- Aperçu de l'image -->
            <div
                v-if="imagePreview"
                class="relative mx-auto w-48 h-48 rounded-lg overflow-hidden bg-gray-200"
            >
                <img
                    :src="imagePreview"
                    alt="Aperçu de l'image"
                    class="w-full h-full object-cover"
                />
            </div>

            <!-- Upload Image -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Image principale</label
                >
                <input
                    @change="onImageChange"
                    type="file"
                    accept="image/*"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                />
            </div>

            <!-- Adresse -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Adresse</label
                >
                <input
                    v-model="form.adresse"
                    type="text"
                    class="input"
                    placeholder="Ex : 123 Rue des Champs"
                    required
                />
            </div>

            <!-- Description -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Description</label
                >
                <textarea
                    v-model="form.description"
                    rows="3"
                    class="input"
                    placeholder="Ex : Maison spacieuse avec jardin..."
                ></textarea>
            </div>

            <!-- Propriétaire -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Propriétaire</label
                >
                <select v-model="form.proprietaire_id" class="input" required>
                    <option disabled value="">-- Sélectionner --</option>
                    <option
                        v-for="user in users"
                        :key="user.id"
                        :value="user.id"
                    >
                        {{ user.nom }} ({{ user.email }})
                    </option>
                </select>
            </div>

            <!-- Coordonnées -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1"
                        >Latitude</label
                    >
                    <input
                        v-model.number="form.latitude"
                        type="number"
                        step="any"
                        class="input"
                        placeholder="Ex : 48.8566"
                    />
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1"
                        >Longitude</label
                    >
                    <input
                        v-model.number="form.longitude"
                        type="number"
                        step="any"
                        class="input"
                        placeholder="Ex : 2.3522"
                    />
                </div>
            </div>

            <!-- Bouton -->
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 transition text-white font-semibold py-2 rounded-lg shadow"
            >
                {{
                    editing
                        ? "Enregistrer les modifications"
                        : "Ajouter la maison"
                }}
            </button>

            <!-- Message -->
            <p
                v-if="message"
                class="text-sm text-center"
                :class="{ 'text-green-600': success, 'text-red-600': !success }"
            >
                {{ message }}
            </p>
        </form>
    </div>
</template>

<script setup>
import { ref, defineProps, defineEmits } from "vue";
import axios from "axios";

const props = defineProps({
    maison: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(["created", "updated"]);

// État du formulaire
const initialForm = {
    adresse: "",
    description: "",
    proprietaire_id: "",
    latitude: "",
    longitude: "",
};

const form = ref({ ...initialForm });
const imagePreview = ref(null);
const editing = ref(!!props.maison);

const message = ref("");
const success = ref(false);

// Si on est en mode édition, on charge les données existantes
if (props.maison) {
    form.value = { ...props.maison };
    if (props.maison.image_principale) {
        imagePreview.value = props.maison.image_principale;
    }
}

// Gérer le changement de fichier
const onImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.value.image_principale = file;

        // Aperçu
        const reader = new FileReader();
        reader.onload = () => {
            imagePreview.value = reader.result;
        };
        reader.readAsDataURL(file);
    }
};

// Soumettre le formulaire
const submit = async () => {
    const formData = new FormData();

    for (const key in form.value) {
        if (form.value[key] instanceof File) {
            formData.append(key, form.value[key]);
        } else {
            formData.append(key, form.value[key] ?? "");
        }
    }

    if (editing.value) {
        formData.append("_method", "PUT");
    }

    try {
        let response;
        if (editing.value) {
            response = await axios.post(
                `/api/maisons/${props.maison.id}`,
                formData,
                {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                }
            );
            emit("updated", response.data);
        } else {
            response = await axios.post("/api/maisons", formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });
            emit("created", response.data);
        }

        message.value = editing.value
            ? "Maison mise à jour."
            : "Maison ajoutée.";
        success.value = true;
    } catch (e) {
        message.value = "Erreur lors de l'enregistrement.";
        success.value = false;
        console.error(e);
    }
};
</script>
<style scoped>
.input {
    @apply w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500;
}
</style>
