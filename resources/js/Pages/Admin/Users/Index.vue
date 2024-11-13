<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from "@/Components/Main/Admin/Layout/MainLayout.vue";
import MainTitle from '@/Components/Main/Admin/Components/Titles/MainTitle.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Filters from "@/Pages/Admin/Invoices/Partials/Filters.vue";
import MainTable from "@/Components/Main/Admin/Components/Tables/MainTable.vue";

defineOptions({
    layout: MainLayout
});

const props = defineProps({
    users: {
        type: Object,
        required: true,
    }
});

const thead = ['nombre', 'email', 'fecha de creación', 'fecha de actualización'];
const url = 'admin.users.index';
</script>

<template>
    <Head title="Usuarios"/>

    <MainTitle>Usuarios</MainTitle>

    <div class="space-y-6">
        <!--        <Filters :clients="clients" :filters="{ client: clientFilter, month: monthFilter, year: yearFilter }"-->
        <!--                 :url="url"/>-->

        <MainTable :pagination="invoices">
            <template #createButton>
                <Link
                    :href="route('admin.users.create')">
                    <PrimaryButton>Crear</PrimaryButton>
                </Link>
            </template>

            <template #thead>
                <th v-for="(th, key) in thead" scope="col" class="px-4 py-3" :key="key + 'th'">
                    {{ th }}
                </th>
            </template>

            <template #tbody>
                <tr v-for="tb in users.data"
                    class="dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition ease-linear duration-300"
                    :key="tb.id + 'tb'">
                    <td class="px-4 py-3">{{ tb.name }}</td>
                    <td class="px-4 py-3">{{ tb.email }}</td>
                    <td class="px-4 py-3">{{ tb.created_at }}</td>
                    <td class="px-4 py-3">{{ tb.updated_at }}</td>
                </tr>
            </template>
        </MainTable>
    </div>
</template>

<style scoped>

</style>