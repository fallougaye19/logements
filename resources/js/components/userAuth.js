import { computed } from "vue";

export default function useAuth() {
    const user = JSON.parse(localStorage.getItem("user"));

    const isProprietaire = computed(() => user && user.role === "proprietaire");
    const isLocataire = computed(() => user && user.role === "locataire");

    return {
        user,
        isProprietaire,
        isLocataire
    };
}
