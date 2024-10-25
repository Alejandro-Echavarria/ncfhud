<script setup>
import {defineAsyncComponent, computed} from 'vue';

const props = defineProps({
    icon: {
        type: String,
        required: true,
    }
});

// Función para cargar dinámicamente el componente basado en el nombre
const loadIconComponent = (iconName) => {
    return defineAsyncComponent(async () => {
        try {
            return await import(`../Icons/${iconName}Icon.vue`);
        } catch (e) {
            console.error(`Error loading component: ${iconName}Icon.vue`);
        }
    });
};

const iconComponent = computed(() => {
    return loadIconComponent(props.icon);
});
</script>

<template>
    <component :is="iconComponent"/>
</template>
