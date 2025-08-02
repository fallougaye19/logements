<template>
    <div class="p-8 bg-gray-100 min-h-screen flex justify-center items-start">
        <form
            @submit.prevent="submit"
            class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-xl space-y-6"
        >
            <h2 class="text-2xl font-bold text-blue-800 text-center">
                {{ editing ? "Modifier le contrat" : "Nouveau contrat" }}
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

            <!-- Dates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1"
                        >Date de début</label
                    >
                    <input
                        v-model="form.date_debut"
                        type="date"
                        class="input"
                        required
                    />
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1"
                        >Date de fin</label
                    >
                    <input
                        v-model="form.date_fin"
                        type="date"
                        class="input"
                        required
                    />
                </div>
            </div>

            <!-- Montant de caution -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Montant de caution (FCFA)</label
                >
                <input
                    v-model.number="form.montant_caution"
                    type="number"
                    class="input"
                    placeholder="Ex : 150000"
                />
            </div>

            <!-- Mode de paiement -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Mode de paiement</label
                >
                <select v-model="form.mode_paiement" class="input">
                    <option disabled value="">-- Sélectionner --</option>
                    <option value="espece">Espèce</option>
                    <option value="virement">Virement bancaire</option>
                    <option value="mobile_money">Mobile Money</option>
                </select>
            </div>

            <!-- Périodicité -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Périodicité</label
                >
                <select v-model="form.periodicite" class="input">
                    <option disabled value="">-- Sélectionner --</option>
                    <option value="mensuel">Mensuel</option>
                    <option value="trimestriel">Trimestriel</option>
                    <option value="semestriel">Semestriel</option>
                    <option value="annuel">Annuel</option>
                </select>
            </div>

            <!-- Nombre de mois de caution -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Nombre de mois de caution</label
                >
                <input
                    v-model.number="form.mois_caution"
                    type="number"
                    class="input"
                    placeholder="Ex : 2"
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
                    placeholder="Ex : Paiement anticipé, sans animal domestique..."
                ></textarea>
            </div>

            <!-- Bouton -->
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 transition text-white font-semibold py-2 rounded-lg shadow"
            >
                {{
                    editing
                        ? "Enregistrer les modifications"
                        : "Ajouter le contrat"
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
    contrat: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(["created", "updated"]);

// État du formulaire
const initialForm = {
    locataire_id: "",
    chambre_id: "",
    date_debut: "",
    date_fin: "",
    montant_caution: "",
    mois_caution: "",
    description: "",
    mode_paiement: "",
    periodicite: "",
};

const form = ref({ ...initialForm });

if (props.contrat) {
    form.value = { ...props.contrat };
}

const editing = ref(!!props.contrat);
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

// Soumettre le formulaire
const message = ref("");
const success = ref(false);

const submit = async () => {
    try {
        let response;
        if (editing.value) {
            response = await axios.put(
                `/api/contrats/${props.contrat.id}`,
                form.value
            );
            emit("updated", response.data);
        } else {
            response = await axios.post("/api/contrats", form.value);
            emit("created", response.data);
        }

        message.value = editing.value
            ? "Contrat mis à jour."
            : "Contrat ajouté.";
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
