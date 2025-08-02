<template>
    <div class="media-form p-4 border rounded shadow-sm">
        <h3 class="text-lg font-semibold mb-4">
            Ajouter un média (photo ou vidéo)
        </h3>
        <form @submit.prevent="submitForm" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="file" class="block mb-1 font-medium">Fichier</label>
                <input
                    type="file"
                    id="file"
                    @change="handleFileChange"
                    accept="image/*,video/*"
                    required
                />
            </div>
            <div class="mb-4">
                <label for="type" class="block mb-1 font-medium">Type</label>
                <select id="type" v-model="form.type" required>
                    <option value="" disabled>Choisir le type</option>
                    <option value="photo">Photo</option>
                    <option value="video">Vidéo</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="description" class="block mb-1 font-medium"
                    >Description</label
                >
                <textarea
                    id="description"
                    v-model="form.description"
                    rows="3"
                    placeholder="Description (optionnel)"
                ></textarea>
            </div>
            <div class="mb-4">
                <label for="maison_id" class="block mb-1 font-medium"
                    >Maison ID</label
                >
                <input
                    type="number"
                    id="maison_id"
                    v-model.number="form.maison_id"
                    placeholder="ID de la maison (optionnel)"
                />
            </div>
            <div class="mb-4">
                <label for="chambre_id" class="block mb-1 font-medium"
                    >Chambre ID</label
                >
                <input
                    type="number"
                    id="chambre_id"
                    v-model.number="form.chambre_id"
                    placeholder="ID de la chambre (optionnel)"
                />
            </div>
            <button
                type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
            >
                Envoyer
            </button>
        </form>
        <div
            v-if="message"
            class="mt-4 p-2 bg-green-100 text-green-700 rounded"
        >
            {{ message }}
        </div>
        <div v-if="error" class="mt-4 p-2 bg-red-100 text-red-700 rounded">
            {{ error }}
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "MediaForm",
    data() {
        return {
            form: {
                file: null,
                type: "",
                description: "",
                maison_id: null,
                chambre_id: null,
            },
            message: "",
            error: "",
        };
    },
    methods: {
        handleFileChange(event) {
            this.form.file = event.target.files[0];
        },
        async submitForm() {
            this.message = "";
            this.error = "";
            if (!this.form.file) {
                this.error = "Veuillez sélectionner un fichier.";
                return;
            }
            const formData = new FormData();
            formData.append("file", this.form.file);
            formData.append("type", this.form.type);
            formData.append("description", this.form.description);
            if (this.form.maison_id)
                formData.append("maison_id", this.form.maison_id);
            if (this.form.chambre_id)
                formData.append("chambre_id", this.form.chambre_id);

            try {
                const response = await axios.post("/api/medias", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                });
                this.message = "Média ajouté avec succès.";
                this.form.file = null;
                this.form.type = "";
                this.form.description = "";
                this.form.maison_id = null;
                this.form.chambre_id = null;
                this.$refs.fileInput.value = null;
            } catch (error) {
                if (
                    error.response &&
                    error.response.data &&
                    error.response.data.errors
                ) {
                    this.error = Object.values(error.response.data.errors)
                        .flat()
                        .join(" ");
                } else if (
                    error.response &&
                    error.response.data &&
                    error.response.data.message
                ) {
                    this.error = error.response.data.message;
                } else {
                    this.error = "Une erreur est survenue lors de l'upload.";
                }
            }
        },
    },
};
</script>

<style scoped>
.media-form {
    max-width: 400px;
}
</style>
