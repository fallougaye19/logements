<template>
    <div class="p-8 bg-gray-100 min-h-screen flex justify-center items-start">
        <form
            @submit.prevent="submit"
            class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-xl space-y-6"
        >
            <h2 class="text-2xl font-bold text-blue-800 text-center">
                {{ editing ? "Modifier le paiement" : "Nouveau paiement" }}
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
                        {{ formatePrice(contrat.montant_caution) }}
                    </option>
                </select>
            </div>

            <!-- Montant -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Montant</label
                >
                <input
                    v-model.number="form.montant"
                    type="number"
                    step="any"
                    class="input"
                    placeholder="Ex : 75000"
                    required
                />
            </div>

            <!-- Statut – Visibilité conditionnelle -->
            <div v-if="isProprietaire || isEditingPaye">
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Statut</label
                >
                <select v-model="form.statut" class="input" required>
                    <option disabled value="">-- Sélectionner --</option>
                    <option value="payé">Payé</option>
                    <option value="en attente">En attente</option>
                    <option value="retard">Retard</option>
                </select>
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1"
                        >Date d'échéance</label
                    >
                    <input
                        v-model="form.date_echeance"
                        type="date"
                        class="input"
                        required
                    />
                </div>

                <!-- Date de paiement – visible seulement si statut = 'payé' -->
                <div v-if="form.statut === 'payé' || isProprietaire">
                    <label class="text-sm font-medium text-gray-700 block mb-1"
                        >Date de paiement</label
                    >
                    <input
                        v-model="form.date_paiement"
                        type="date"
                        class="input"
                    />
                </div>
            </div>

            <!-- Mode de paiement -->
            <div v-if="isProprietaire || isEditing">
                <label class="text-sm font-medium text-gray-700 block mb-1"
                    >Mode de paiement</label
                >
                <input
                    v-model="form.mode_paiement"
                    type="text"
                    list="modes-paiement"
                    class="input"
                />
                <datalist id="modes-paiement">
                    <option value="Virement bancaire"></option>
                    <option value="Mobile Money"></option>
                    <option value="Espèces"></option>
                    <option value="Chèque"></option>
                    <option value="Autre"></option>
                </datalist>
            </div>

            <!-- Bouton -->
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 transition text-white font-semibold py-2 rounded-lg shadow"
            >
                {{
                    editing
                        ? "Enregistrer les modifications"
                        : "Ajouter le paiement"
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
import { ref, defineProps, defineEmits, computed, onMounted } from "vue";
import axios from "axios";

const props = defineProps({
    paiement: {
        type: Object,
        default: null,
    },
});

const isEditing = computed(() => props.paiement !== null);

const emit = defineEmits(["created", "updated"]);

// État utilisateur connecté
const user = JSON.parse(localStorage.getItem("user"));
const isProprietaire = computed(() => user?.role === "proprietaire");

// Formulaire
const initialForm = {
    contrat_id: "",
    montant: "",
    statut: "en attente",
    date_echeance: "",
    date_paiement: "",
    mode_paiement: "",
};

const form = ref({ ...initialForm });
const editing = ref(!!props.paiement);
const contrats = ref([]);

// Chargement des contrats
const fetchContrats = async () => {
    try {
        const res = await axios.get("/api/contrats");
        contrats.value = res.data;
    } catch (err) {
        console.error("Erreur lors du chargement des contrats", err);
    }
};

onMounted(fetchContrats);

// Si paiement existant, charger ses données
if (props.paiement) {
    form.value = { ...props.paiement };
}

// Vérifie si le statut est "payé"
const isEditingPaye = computed(() => form.value.statut === "payé");


// Format monnaie
const formatePrice = (price) => {
    return new Intl.NumberFormat("fr-FR", {
        style: "currency",
        currency: "XOF",
    }).format(price);
};

// Soumission du formulaire
const submit = async () => {
    try {
        let response;
        if (editing.value) {
            response = await axios.put(
                `/api/paiements/${props.paiement.id}`,
                form.value
            );
            emit("updated", response.data);
        } else {
            response = await axios.post("/api/paiements", form.value);
            emit("created", response.data);
        }

        message.value = editing.value
            ? "Paiement mis à jour."
            : "Paiement ajouté.";
        success.value = true;
    } catch (e) {
        message.value = "Erreur lors de l'enregistrement.";
        success.value = false;
        console.error(e);
    }
};

const message = ref("");
const success = ref(false);
</script>

<style scoped>
.input {
    @apply w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm;
}
.btn {
    @apply bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition-colors;
}
</style>
