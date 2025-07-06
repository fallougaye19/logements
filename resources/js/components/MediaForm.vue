<template>
    <div class="p-8 bg-gray-100 min-h-screen flex justify-center items-start">
        <form
            @submit.prevent="submit"
            class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-xl space-y-6"
        >
            <h2 class="text-2xl font-bold text-blue-800 text-center">
                {{ editing ? "Modifier le média" : "Nouveau média" }}
            </h2>

            <!-- URL -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >URL</label
                >
                <input
                    v-model="form.url"
                    type="text"
                    class="input"
                    placeholder="Ex : https://exemple.com/image.jpg "
                    required
                />
            </div>

            <!-- Type -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Type</label
                >
                <select v-model="form.type" class="input" required>
                    <option disabled value="">-- Sélectionner --</option>
                    <option value="image">Image</option>
                    <option value="video">Video</option>
                    <option value="document">Document</option>
                </select>
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
                    placeholder="Ex : Vue panoramique de la chambre..."
                ></textarea>
            </div>

            <!-- Chambre -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Chambre</label
                >
                <select v-model="form.chambre_id" class="input" required>
                    <option disabled value="">-- Sélectionner --</option>
                    <option
                        v-for="chambre in chambres"
                        :key="chambre.id"
                        :value="chambre.id"
                    >
                        {{ chambre.titre }}
                    </option>
                </select>
            </div>

            <!-- Bouton -->
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 transition text-white font-semibold py-2 rounded-lg shadow"
            >
                {{
                    editing
                        ? "Enregistrer les modifications"
                        : "Ajouter le média"
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
    media: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(["created", "updated"]);

// État du formulaire
const form = ref({
    url: "",
    type: "",
    description: "",
    chambre_id: "",
});

const chambres = ref([]);

// Chargement des chambres
const fetchChambres = async () => {
    try {
        const res = await axios.get("/api/chambres");
        chambres.value = res.data;
    } catch (err) {
        console.error("Erreur lors du chargement des chambres", err);
    }
};

fetchChambres();

// Si on est en mode édition
if (props.media) {
    form.value = { ...props.media };
}

const editing = ref(!!props.media);
const message = ref("");
const success = ref(false);

const submit = async () => {
    try {
        let response;
        if (editing.value) {
            response = await axios.put(
                `/api/medias/${props.media.id}`,
                form.value
            );
            emit("updated", response.data);
        } else {
            response = await axios.post("/api/medias", form.value);
            emit("created", response.data);
        }

        message.value = editing.value ? "Média mis à jour." : "Média ajouté.";
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
