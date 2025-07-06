<template>
    <form @submit.prevent="submit" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Nom</label>
            <input v-model="form.nom" type="text" required class="input" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input v-model="form.email" type="email" required class="input" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Téléphone</label>
            <input v-model="form.telephone" type="tel" class="input" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">CNI</label>
            <input v-model="form.cni" type="text" class="input" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Mot de passe</label>
            <input v-model="form.password" type="password" class="input" placeholder="••••••••" />
        </div>

        <button type="submit" class="btn w-full mt-4">
            {{ isEdit ? 'Enregistrer les modifications' : 'Ajouter utilisateur' }}
        </button>

        <p v-if="message" :class="{ 'text-green-600': success, 'text-red-600': !success }" class="text-center text-sm mt-2">
            {{ message }}
        </p>
    </form>
</template>

<script setup>
import { ref, defineProps, defineEmits } from "vue";
import axios from "axios";

const props = defineProps({
    utilisateur: {
        type: Object,
        default: null
    },
    isEdit: {
        type: Boolean,
        default: false
    }
});

const emits = defineEmits(["created", "updated"]);

const form = ref({
    nom: "",
    email: "",
    telephone: "",
    cni: "",
    role: "locataire",
    password: ""
});

if (props.utilisateur) {
    form.value = {
        ...props.utilisateur,
        password: "" // ne pas préremplir le mot de passe
    };
}

const message = ref("");
const success = ref(false);

const submit = async () => {
    try {
        let response;

        if (props.isEdit && props.utilisateur?.id) {
            response = await axios.put(`/api/utilisateurs/${props.utilisateur.id}`, form.value);
            emits("updated", response.data);
        } else {
            response = await axios.post("/api/utilisateurs", form.value);
            emits("created", response.data);
        }

        message.value = props.isEdit ? "Profil mis à jour" : "Utilisateur ajouté";
        success.value = true;

        setTimeout(() => {
            message.value = "";
        }, 3000);

        if (props.isEdit) {
            // Met à jour les données dans localStorage si c’est le profil courant
            localStorage.setItem("user", JSON.stringify(response.data));
        }

    } catch (e) {
        message.value = "Erreur lors de l'enregistrement";
        success.value = false;
        console.error(e);
    }
};
</script>
