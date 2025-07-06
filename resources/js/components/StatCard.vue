<template>
    <div
        class="bg-white shadow-md rounded-lg p-6 flex items-center justify-between hover:shadow-lg transition-shadow"
    >
        <div>
            <p class="text-sm font-medium text-gray-500">{{ label }}</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ value }}</p>
        </div>
        <div :class="`text-${color}-500`">
            <svg
                class="w-10 h-10"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 19v-6a2 2 0 012-2h2c.59 0 1.155-.25 1.59-.68l3.85 2.25"
                ></path>
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1H7a1 1 0 00-1 1v3M4 7h16"
                />
            </svg>
        </div>
    </div>
</template>

<script setup>
defineProps({
    label: String,
    value: [Number, String],
    color: {
        type: String,
        default: "blue",
        validator: (c) =>
            [
                "blue",
                "green",
                "red",
                "yellow",
                "purple",
                "pink",
                "teal",
                "indigo",
            ].includes(c),
    },
});
</script>
