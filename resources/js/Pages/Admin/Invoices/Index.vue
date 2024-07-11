<script setup>
import { ref, watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import MainLayout from "@/Components/Main/Admin/Layout/MainLayout.vue";
import MainTitle from '@/Components/Main/Admin/Components/Titles/MainTitle.vue';
import MainTable from '@/Components/Main/Admin/Components/Tables/MainTable.vue';
import SaveClient from "@/Pages/Admin/Clients/Partials/SaveClient.vue";
import Search from '@/Components/Main/Admin/Components/Searchs/Search.vue';
import VueSelect from "@/Components/Main/Admin/Components/Selects/VueSelect.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Filters from "@/Pages/Admin/Invoices/Partials/Filters.vue";

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
    clientFilter: Number,
    monthFilter: String,
    yearFilter: String,
    page: String
});

const thead = ['ncf', 'Fecha comprobante', 'monto', 'itbis'];
const url = 'admin.invoices.index';
const callOpenModal = ref(null);

const openModal = (op, id, rnc, business_name, commercial_activity, email) => {
    callOpenModal.value.openModal(op, id, rnc, business_name, commercial_activity, email);
};
</script>

<template>
    <Head title="Facturas" />

    <MainTitle>Facturas</MainTitle>

    <div class="space-y-6">
        <Filters :clients="clients" :filters="{client: clientFilter, month: monthFilter, year: yearFilter}" :url="url"/>

        <MainTable :pagination="invoices">
            <!--        <template #search>-->
            <!--            <Search :filter="filter" :url="url"/>-->
            <!--        </template>-->

            <!--        <template #createButton>-->
            <!--            <SaveClient ref="callOpenModal" :filter="filter" :page="page"/>-->
            <!--        </template>-->

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
                    <td class="px-4 py-3 flex items-center justify-end">
                        <!--                    <TableButton>-->
                        <!--                        <font-awesome-icon @click="openModal(2, tb.slug, tb.name)" class="w-4 h-4 text-indigo-500"-->
                        <!--                            :icon="['far', 'pen-to-square']" />-->
                        <!--                    </TableButton>-->
                        <!--                    <DeleteCategory :id="tb.slug" :filter="filter" :page="page" :key="tb.id + 'deleteBtn'" />-->
                    </td>
                </tr>
            </template>
        </MainTable>
    </div>
</template>