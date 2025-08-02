<template>
    <div
        class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
    >
        <div
            class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-lg relative"
        >
            <h2 class="text-2xl font-bold mb-4 text-gray-800">
                Demander un rendez-vous
            </h2>

            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2"
                        >Date et Heure</label
                    >
                    <input
                        type="datetime-local"
                        v-model="date_heure"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl"
                    />
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button
                        type="button"
                        @click="$emit('close')"
                        class="px-4 py-2 bg-gray-300 rounded-xl"
                    >
                        Annuler
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-xl"
                    >
                        Envoyer
                    </button>
                </div>

                <p
                    v-if="message"
                    :class="success ? 'text-green-600' : 'text-red-600'"
                    class="mt-4"
                >
                    {{ message }}
                </p>
            </form>

            <button
                @click="$emit('close')"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-800"
            >
                ✕
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";

const props = defineProps({
    chambreId: Number,
});

const emits = defineEmits(["close", "submitted"]);

const date_heure = ref("");
const message = ref("");
const success = ref(false);

const submit = async () => {
    message.value = "";
    success.value = false;

    try {
        await axios.post(
            "http://localhost:8000/api/rendez-vous",
            {
                chambre_id: props.chambreId,
                date_heure: date_heure.value,
                locataire_id: JSON.parse(localStorage.getItem("user"))?.id,
            },
            {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                },
            }
        );
        success.value = true;
        message.value = "Rendez-vous demandé avec succès";
        emits("submitted");
        setTimeout(() => emits("close"), 1000);
    } catch (e) {
        message.value =
            e.response?.data?.message || "Erreur lors de la demande";
    }
};
</script>
