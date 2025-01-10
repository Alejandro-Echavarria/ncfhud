<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from "@/Components/Main/Admin/Layout/MainLayout.vue";
import MainTitle from '@/Components/Main/Admin/Components/Titles/MainTitle.vue';
import MainTable from '@/Components/Main/Admin/Components/Tables/MainTable.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Filters from "@/Pages/Admin/Invoices/Partials/Filters.vue";
import { usePermission } from "@/Composables/permissions.js";
import H2Title from "@/Components/Main/Admin/Components/Titles/H2Title.vue";

defineOptions({
    layout: MainLayout
});

const props = defineProps({
    invoices: {
        type: Object,
        required: true,
    },
    clients: {
        type: Object,
        required: true,
    },
    clientFilter: String,
    monthFilter: String,
    yearFilter: String,
    page: String
});

const thead = ['ncf', 'Fecha comprobante', 'monto', 'itbis'];
const url = 'admin.invoices.index';
const callOpenModal = ref(null);

const { hasPermission } = usePermission();
const canCreateInvoice = hasPermission("admin.invoices.create_607");

const LinkComponent = canCreateInvoice ? Link : null;
const CreateInvoiceComponent = canCreateInvoice ? PrimaryButton : null;

const openModal = (op, id, rnc, business_name, commercial_activity, email) => {
    callOpenModal.value.openModal(op, id, rnc, business_name, commercial_activity, email);
};
</script>

<template>
    <Head title="Facturas"/>

    <MainTitle>Facturas</MainTitle>

    <div class="space-y-6">
        <Filters :clients="clients" :filters="{ client: clientFilter, month: monthFilter, year: yearFilter }"
                 :url="url"/>

        <H2Title>
            607
        </H2Title>

        <MainTable :pagination="invoices">
            <template #createButton v-if="canCreateInvoice">
                <component :is="LinkComponent"
                           :href="route(
                               'admin.invoices.create',
                               { client: clientFilter, month: monthFilter, year: yearFilter }
                           )">
                    <component :is="CreateInvoiceComponent">Crear</component>
                </component>
            </template>

            <template #thead>
                <th v-for="(th, key) in thead" scope="col" class="px-4 py-3" :key="key + 'th'">
                    {{ th }}
                </th>
            </template>

            <template #tbody>
                <tr v-for="tb in invoices.data"
                    class="dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition ease-linear duration-300"
                    :key="tb.id + 'tb'">
                    <td class="px-4 py-3">{{ tb.ncf }}</td>
                    <td class="px-4 py-3">{{ tb.proof_date }}</td>
                    <td class="px-4 py-3">{{ tb.amount }}</td>
                    <td class="px-4 py-3">{{ tb.itbis }}</td>
                </tr>
            </template>
        </MainTable>
    </div>
</template>
