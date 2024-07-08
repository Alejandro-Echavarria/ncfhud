<script setup>
import MainLayout from "@/Components/Main/Admin/Layout/MainLayout.vue";
import { Head } from '@inertiajs/vue3';
import MainTitle from '@/Components/Main/Admin/Components/Titles/MainTitle.vue';
import MainTable from '@/Components/Main/Admin/Components/Tables/MainTable.vue';

defineOptions({
    layout: MainLayout
});

const props = defineProps({
    clients: {
        type: Object,
        required: true,
    },
});

const thead = ['rnc', 'nombre', 'actividad comercial', 'email', 'creado', 'actualizado'];
</script>

<template>
    <Head title="Clients" />

    <MainTitle>Clients</MainTitle>

    <MainTable :pagination="clients">
        <template #search>
<!--            <Search :filter="filter" :url="url" />-->
        </template>

        <template #createButton>
<!--            <SaveCategory ref="callOpenModal" :filter="filter" :page="page" />-->
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
