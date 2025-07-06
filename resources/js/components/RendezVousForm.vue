<template>
    <div class="p-8 bg-gray-100 min-h-screen flex justify-center items-start">
        <form
            @submit.prevent="submit"
            class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-xl space-y-6"
        >
            <h2 class="text-2xl font-bold text-blue-800 text-center">
                {{
                    editing ? "Modifier le rendez-vous" : "Nouveau rendez-vous"
                }}
            </h2>

            <!-- Locataire -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Locataire</label
                >
                <select v-model="form.locataire_id" class="input" required>
                    <option disabled value="">-- Sélectionner --</option>
                    <option
                        v-for="user in locataires"
                        :key="user.id"
                        :value="user.id"
                    >
                        {{ user.nom }} ({{ user.email }})
                    </option>
                </select>
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
                        {{ chambre.titre }} ({{ formatPrice(chambre.prix) }})
                    </option>
                </select>
            </div>

            <!-- Date et heure -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Date et heure</label
                >
                <input
                    v-model="form.date_heure"
                    type="datetime-local"
                    class="input"
                    required
                />
            </div>

            <!-- Statut -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Statut</label
                >
                <select v-model="form.statut" class="input" required>
                    <option disabled value="">-- Sélectionner --</option>
                    <option value="confirmé">Confirmé</option>
                    <option value="en attente">En attente</option>
                    <option value="annulé">Annulé</option>
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
                        : "Ajouter le rendez-vous"
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
    "rendez-vous": {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(["created", "updated"]);

// État du formulaire
const initialForm = {
    locataire_id: "",
    chambre_id: "",
    date_heure: "",
    statut: "",
};

const form = ref({ ...initialForm });

if (props.rendez_vous) {
    form.value = { ...props.rendez_vous };
}

const editing = ref(!!props.rendez_vous);
const locataires = ref([]);
const chambres = ref([]);

// Chargement des données
const fetchLocataires = async () => {
    try {
        const res = await axios.get("/api/utilisateurs");
        locataires.value = res.data;
    } catch (err) {
        console.error("Erreur lors du chargement des locataires", err);
    }
};

const fetchChambres = async () => {
    try {
        const res = await axios.get("/api/chambres");
        chambres.value = res.data;
    } catch (err) {
        console.error("Erreur lors du chargement des chambres", err);
    }
};

fetchLocataires();
fetchChambres();

const message = ref("");
const success = ref(false);

const submit = async () => {
    try {
        let response;
        if (editing.value) {
            response = await axios.put(
                `/api/rendez-vous/${props.rendez_vous.id}`,
                form.value
            );
            emit("updated", response.data);
        } else {
            response = await axios.post("/api/rendez-vous", form.value);
            emit("created", response.data);
        }

        message.value = editing.value
            ? "Rendez-vous mis à jour."
            : "Rendez-vous ajouté.";
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
