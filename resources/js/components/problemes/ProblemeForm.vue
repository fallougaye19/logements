<!-- components/problemes/ProblemeForm.vue -->

<template>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-bold mb-4">Signaler un problème</h3>

        <form @submit.prevent="soumettreProbleme">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1"
                    >Type de problème</label
                >
                <select
                    v-model="form.type"
                    class="w-full border rounded px-3 py-2"
                    required
                >
                    <option value="plomberie">Plomberie</option>
                    <option value="electricite">Électricité</option>
                    <option value="serrurerie">Serrurerie</option>
                    <option value="autre">Autre</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1"
                    >Description</label
                >
                <textarea
                    v-model="form.description"
                    rows="4"
                    class="w-full border rounded px-3 py-2"
                    placeholder="Décrivez le problème..."
                    required
                ></textarea>
            </div>

            <button
                type="submit"
                :disabled="loading"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded disabled:opacity-50"
            >
                {{ loading ? "Envoi..." : "Signaler le problème" }}
            </button>
        </form>
    </div>
</template>

<script>
import axios from "axios";

export default {
    props: {
        contratId: { type: Number, required: true },
    },
    data() {
        return {
            loading: false,
            form: {
                contrat_id: this.contratId,
                description: "",
                type: "autre",
                responsable: null,
            },
        };
    },
    methods: {
        async soumettreProbleme() {
            this.loading = true;
            try {
                const response = await axios.post("/api/problemes", this.form, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem(
                            "token"
                        )}`,
                    },
                });

                this.$emit("probleme-signe", "Problème signalé avec succès.");
                this.$emit("close");
                this.resetForm();
            } catch (error) {
                console.error("Erreur:", error);
                alert("Erreur lors du signalement.");
            } finally {
                this.loading = false;
            }
        },
        resetForm() {
            this.form = {
                contrat_id: this.contratId,
                description: "",
                type: "autre",
                responsable: null,
            };
        },
    },
};
</script>
