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
    clients: {
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
    <Head title="Clientes"/>

    <MainTitle>Clientes</MainTitle>

    <MainTable :pagination="clients">
        <template #search>
            <Search :filter="filter" :url="url"/>
        </template>

        <template #createButton>
            <SaveClient ref="callOpenModal" :filter="filter" :page="page"/>
        </template>

        <template #thead>
            <th v-for="(th, key) in thead" scope="col" class="px-4 py-3" :key="key + 'th'">
                {{ th }}
            </th>
        </template>

        <template #tbody>
            <tr v-for="tb in clients.data"
                class="dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition ease-linear duration-300"
                :key="tb.id + 'tb'">
                <td class="px-4 py-3">{{ tb.rnc }}</td>
                <td class="px-4 py-3">{{ tb.business_name }}</td>
                <td class="px-4 py-3">{{ tb.commercial_activity }}</td>
                <td class="px-4 py-3">{{ tb.email }}</td>
                <td class="px-4 py-3">{{ tb.created_at }}</td>
                <td class="px-4 py-3">{{ tb.updated_at }}</td>
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
</template>
