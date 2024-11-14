<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Components/Main/Admin/Layout/MainLayout.vue";
import MainTitle from '@/Components/Main/Admin/Components/Titles/MainTitle.vue';
import MainTable from "@/Components/Main/Admin/Components/Tables/MainTable.vue";
import Search from "@/Components/Main/Admin/Components/Searchs/Search.vue";
import SaveUser from "@/Pages/Admin/Users/Partials/SaveUser.vue";
import TableButton from "@/Components/Main/Admin/Components/Buttons/TableButton.vue";
import Icon from "@/Components/Main/Admin/Components/Icons/Icon.vue";

defineOptions({
    layout: MainLayout
});

const props = defineProps({
    users: {
        type: Object,
        required: true,
    },
    filter: String,
    page: String
});

const thead = ['nombre', 'email', 'estado', 'fecha de creación', 'fecha de actualización'];
const url = 'admin.users.index';
const callOpenModal = ref(null);

const openModal = (op, data) => {
    callOpenModal.value.openModal(op, data);
};
</script>

<template>
    <Head title="Usuarios"/>

    <MainTitle>Usuarios</MainTitle>

    <MainTable :pagination="users">
        <template #search>
            <Search :filter="filter" :url="url"/>
        </template>

        <template #createButton>
            <SaveUser ref="callOpenModal" :filter="filter" :page="page"/>
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
                <td class="px-4 py-3">{{ tb.is_active }}</td>
                <td class="px-4 py-3">{{ tb.created_at }}</td>
                <td class="px-4 py-3">{{ tb.updated_at }}</td>
                <table-button>
                    <Icon @click="openModal(2, tb)" class="text-indigo-500"
                          icon="Edit"/>
                </table-button>
<!--                <DeleteCategory :id="tb.id" :filter="filter" :page="page" :key="tb.id + 'deleteBtn'"/>-->
            </tr>
        </template>
    </MainTable>
</template>
