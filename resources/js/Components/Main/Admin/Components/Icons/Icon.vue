<template>
  <component :is="iconComponent" />
</template>

<script setup>
import { defineAsyncComponent, computed } from 'vue';

const props = defineProps({
    icon: {
        type: String,
        required: true,
    }
});

const iconComponent = computed(() => {
    try {
        return defineAsyncComponent(() => import(/* @vite-ignore */ `../Icons/${props.icon}Icon.vue`));
    } catch (error) {
        console.log(error);
    }
});
</script>