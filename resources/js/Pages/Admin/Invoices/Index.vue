<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Components/Main/Admin/Layout/MainLayout.vue";
import MainTitle from '@/Components/Main/Admin/Components/Titles/MainTitle.vue';
import MainTable from '@/Components/Main/Admin/Components/Tables/MainTable.vue';
import SaveClient from "@/Pages/Admin/Clients/Partials/SaveClient.vue";
import Search from '@/Components/Main/Admin/Components/Searchs/Search.vue';

defineOptions({
    layout: MainLayout
});

const props = defineProps({
    invoices: {
        type: Object,
        required: true,
    },
    filter: String,
    page: String
});

const thead = ['rnc', 'nombre', 'actividad comercial', 'email', 'creado', 'actualizado'];
const url = 'admin.clients.index';
const callOpenModal = ref(null);

const openModal = (op, id, rnc, business_name, commercial_activity, email) => {
    callOpenModal.value.openModal(op, id, rnc, business_name, commercial_activity, email);
};
</script>

<template>
    <Head title="Facturas"/>

    <MainTitle>Facturas</MainTitle>
</template>