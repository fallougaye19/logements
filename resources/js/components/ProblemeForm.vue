<template>
    <div class="p-8 bg-gray-100 min-h-screen flex justify-center items-start">
        <form
            @submit.prevent="submit"
            class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-xl space-y-6"
        >
            <h2 class="text-2xl font-bold text-blue-800 text-center">
                {{ editing ? "Modifier le problème" : "Nouveau problème" }}
            </h2>

            <!-- Contrat -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Contrat</label
                >
                <select v-model="form.contrat_id" class="input" required>
                    <option disabled value="">-- Sélectionner --</option>
                    <option
                        v-for="contrat in contrats"
                        :key="contrat.id"
                        :value="contrat.id"
                    >
                        Contrat #{{ contrat.id }} -
                        {{ formatePrice(contrat.chambre.prix) }}
                    </option>
                </select>
            </div>

            <!-- Type -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Type</label
                >
                <select v-model="form.type" class="input" required>
                    <option disabled value="">-- Sélectionner --</option>
                    <option value="entretien">Entretien</option>
                    <option value="plomberie">Plomberie</option>
                    <option value="electricite">Électricité</option>
                    <option value="autre">Autre</option>
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
                    placeholder="Ex : Problème de fuite d'eau dans la salle de bain"
                ></textarea>
            </div>

            <!-- Signalé par -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Signalé par</label
                >
                <select v-model="form.signale_par" class="input" required>
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

            <!-- Responsable -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Responsable</label
                >
                <select v-model="form.responsable" class="input">
                    <option disabled value="">-- Sélectionner --</option>
                    <option value="proprietaire">Propriétaire</option>
                    <option value="locataire">Locataire</option>
                </select>
            </div>

            <!-- Résolu -->
            <div>
                <label class="inline-flex items-center space-x-2 mt-2">
                    <input type="checkbox" v-model="form.resolu" />
                    <span>Résolu</span>
                </label>
            </div>

            <!-- Bouton -->
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 transition text-white font-semibold py-2 rounded-lg shadow"
            >
                {{
                    editing
                        ? "Enregistrer les modifications"
                        : "Ajouter le problème"
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
    probleme: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(["created", "updated"]);

// État du formulaire
const initialForm = {
    contrat_id: "",
    signale_par: "",
    description: "",
    type: "",
    responsable: "",
    resolu: false,
};

const form = ref({ ...initialForm });

if (props.probleme) {
    form.value = { ...props.probleme };
}

const editing = ref(!!props.probleme);
const message = ref("");
const success = ref(false);
const contrats = ref([]);
const locataires = ref([]);

// Chargement des contrats
const fetchContrats = async () => {
    try {
        const res = await axios.get("/api/contrats");
        contrats.value = res.data;
    } catch (err) {
        console.error("Erreur lors du chargement des contrats", err);
    }
};

// Chargement des utilisateurs
const fetchLocataires = async () => {
    try {
        const res = await axios.get("/api/utilisateurs");
        locataires.value = res.data;
    } catch (err) {
        console.error("Erreur lors du chargement des locataires", err);
    }
};

fetchContrats();
fetchLocataires();

// Soumettre le formulaire
const submit = async () => {
    try {
        let response;
        if (editing.value) {
            response = await axios.put(
                `/api/problemes/${props.probleme.id}`,
                form.value
            );
            emit("updated", response.data);
        } else {
            response = await axios.post("/api/problemes", form.value);
            emit("created", response.data);
        }

        message.value = editing.value
            ? "Problème mis à jour."
            : "Problème ajouté.";
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
