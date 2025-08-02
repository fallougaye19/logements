<template>
    <div class="p-8 bg-gray-100 min-h-fit flex justify-center items-start">
        <form
            @submit.prevent="submit"
            class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-xl space-y-6"
        >
            <h2 class="text-2xl font-bold text-blue-800 text-center">
                {{ editing ? "Modifier le paiement" : "Nouveau paiement" }}
            </h2>

            <!-- Contrat -->
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Contrat</label>
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
                <label class="text-sm font-medium text-gray-700 block mb-1">Montant</label>
                <input
                    v-model.number="form.montant"
                    type="number"
                    step="any"
                    class="input"
                    placeholder="Ex : 75000"
                    required
                />
            </div>

            <!-- Statut -->
            <div v-if="isProprietaire || isEditingPaye">
                <label class="text-sm font-medium text-gray-700 block mb-1">Statut</label>
                <select v-model="form.statut" class="input" required>
                    <option disabled value="">-- Sélectionner --</option>
                    <option value="payé">Payé</option>
                    <option value="impayé">En attente</option>
                    <option value="en_retard">En retard</option>
                </select>
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1">Date d'échéance</label>
                    <input v-model="form.date_echeance" type="date" class="input" required />
                </div>

                <div v-if="form.statut === 'payé' || isProprietaire">
                    <label class="text-sm font-medium text-gray-700 block mb-1">Date de paiement</label>
                    <input v-model="form.date_paiement" type="date" class="input" />
                </div>
            </div>

            <!-- Mode de paiement -->
            <div v-if="isProprietaire || isEditing">
                <label class="text-sm font-medium text-gray-700 block mb-1">Mode de paiement</label>
                <input
                    v-model="form.mode_paiement"
                    type="text"
                    list="modes-paiement"
                    class="input"
                />
                <datalist id="modes-paiement">
                    <option value="Virement bancaire" />
                    <option value="Mobile Money" />
                    <option value="Espèces" />
                    <option value="Chèque" />
                    <option value="Autre" />
                </datalist>
            </div>

            <!-- Bouton -->
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 transition text-white font-semibold py-2 rounded-lg shadow"
            >
                {{ editing ? "Enregistrer les modifications" : "Ajouter le paiement" }}
            </button>

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

// Props
const props = defineProps({
    paiement: { type: Object, default: null },
    contratId: { type: [Number, String], default: null }, // Accepter Number ou String
});

const emit = defineEmits(["created", "updated"]);

const isEditing = computed(() => props.paiement !== null);
const editing = ref(!!props.paiement);
const user = JSON.parse(localStorage.getItem("user"));
const isProprietaire = computed(() => user?.role === "proprietaire");

const initialForm = {
    contrat_id: "",
    montant: "",
    statut: "en attente",
    date_echeance: "",
    date_paiement: "",
    mode_paiement: "",
};

const form = ref({ ...initialForm });
const contrats = ref([]);
const message = ref("");
const success = ref(false);

const isEditingPaye = computed(() => form.value.statut === "payé");

const fetchContrats = async () => {
    try {
        const token = localStorage.getItem("token");
        const res = await axios.get("/api/contrats", {
            headers: { Authorization: `Bearer ${token}` }
        });
        contrats.value = res.data.success ? res.data.data : res.data;
    } catch (err) {
        console.error("Erreur lors du chargement des contrats", err);
    }
};

onMounted(async () => {
    await fetchContrats();

    if (props.paiement) {
        form.value = { ...props.paiement };
    }

    // Correction ici : bien récupérer l'ID du contrat
    if (!props.paiement && props.contratId) {
        form.value.contrat_id = Number(props.contratId); // S'assurer que c'est un nombre
        console.log("Contrat ID récupéré:", props.contratId);
    }
});

const formatePrice = (price) => {
    return new Intl.NumberFormat("fr-FR", {
        style: "currency",
        currency: "XOF",
    }).format(price);
};

const submit = async () => {
    console.log("Form submitted:", JSON.stringify(form.value, null, 2));

    try {
        const token = localStorage.getItem("token");
        let response;

        if (editing.value) {
            response = await axios.put(
                `/api/paiements/${props.paiement.id}`,
                form.value,
                { headers: { Authorization: `Bearer ${token}` } }
            );
            emit("updated", response.data);
        } else {
            response = await axios.post("/api/paiements", form.value, {
                headers: { Authorization: `Bearer ${token}` }
            });
            emit("created", response.data);
        }

        message.value = editing.value
            ? "Paiement mis à jour."
            : "Paiement ajouté.";
        success.value = true;

        // Réinitialiser le formulaire après création
        if (!editing.value) {
            form.value = { ...initialForm };
            if (props.contratId) {
                form.value.contrat_id = Number(props.contratId);
            }
        }
    } catch (e) {
        console.error("Erreur lors de l'enregistrement du paiement", e);
        message.value = "Erreur lors de l'enregistrement.";
        success.value = false;
    }
};
</script>

<style scoped>
.input {
    @apply w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm;
}
</style>
