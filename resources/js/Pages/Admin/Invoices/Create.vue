<script setup>
import { ref, watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import MainLayout from "@/Components/Main/Admin/Layout/MainLayout.vue";
import MainTitle from "@/Components/Main/Admin/Components/Titles/MainTitle.vue";
import Filters from "@/Pages/Admin/Invoices/Partials/Filters.vue";

defineOptions({
    layout: MainLayout
});

const props = defineProps({
    clients: {
        type: Object,
        required: true
    },
    // clientFilter: String,
    // monthFilter: String,
    // yearFilter: String
});

const form = useForm({
    client: '',
    month: '',
    year: '',
});
</script>

<template>
    <Head title="Crear facturas"/>

    <MainTitle>Crear facturas</MainTitle>

    <div class="space-y-6">
        <Filters :clients="clients" :filters="{ client: clientFilter, month: monthFilter, year: yearFilter }"
                 :url="url" />

        <div>
            <label for="file" class="sr-only">Choose file</label>
            <input type="file" name="file" id="file" class="block w-full border border-gray-200 shadow-sm rounded-lg text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 file:bg-gray-50 file:border-0 file:me-4 file:py-3 file:px-4 dark:file:bg-neutral-700 dark:file:text-neutral-400">
        </div>
    </div>
</template>
