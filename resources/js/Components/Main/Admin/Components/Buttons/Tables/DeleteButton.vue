<script setup>
import Icon from "@/Components/Main/Admin/Components/Icons/Icon.vue";
</script>

<template>
    <td>
        <button class="mr-2">
            <Icon class="text-indigo-500"
                  icon="Delete"/>
            <slot/>
        </button>
    </td>
</template>