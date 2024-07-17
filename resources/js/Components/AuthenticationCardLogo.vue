<script setup>
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<template>
    <Link :href="'/'">
        <ApplicationLogo :class="'h-14 h-14'" :entity-name="$page.props.entity?.name" :url="$page.props.entity?.url" />
    </Link>
</template>
