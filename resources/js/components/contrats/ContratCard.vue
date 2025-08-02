<template>
    <div class="border-l-4 border-gray-600 rounded-lg p-4 mb-4 bg-white">
        <h3 class="font-bold">Contrat - {{ chambre.titre }}</h3>
        <p class="text-sm text-gray-600 mb-2">
            <strong>Locataire :</strong> {{ locataire.nom }}
        </p>
        <p class="text-sm text-gray-600 mb-2">
            <strong>Début :</strong> {{ formatDate(contrat.date_debut) }}
        </p>
        <p class="text-sm text-gray-600 mb-2">
            <strong>Caution :</strong>
            {{ formatPrix(contrat.montant_caution) }} ({{
                contrat.mois_caution
            }}
            mois)
        </p>
        <p class="text-sm text-gray-700">
            <strong>Périodicité :</strong> {{ contrat.periodicite }}
        </p>
        <p
            :class="
                contrat.statut === 'actif' ? 'text-green-600' : 'text-red-600'
            "
        >
            <strong>Statut :</strong> {{ contrat.statut }}
        </p>
        <button
            @click="$emit('view', contrat.id)"
            class="mt-2 text-blue-600 text-sm hover:underline"
        >
            Voir le contrat
        </button>

    </div>
</template>

<script>
export default {
    props: ["contrat"],
    computed: {
        locataire() {
            return this.contrat.locataire || {};
        },
        chambre() {
            return this.contrat.chambre || {};
        },
    },
    methods: {
        formatPrix(prix) {
            return new Intl.NumberFormat("fr-FR", {
                style: "currency",
                currency: "XOF",
            }).format(prix || 0);
        },
        formatDate(date) {
            if (!date) return "N/A";
            return new Date(date).toLocaleDateString("fr-FR");
        },
    },
};
</script>
