<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { usePermission } from "@/Composables/permissions";
import MainLayout from "@/Components/Main/Admin/Layout/MainLayout.vue";
import MainTitle from '@/Components/Main/Admin/Components/Titles/MainTitle.vue';
import Search from '@/Components/Main/Admin/Components/Searchs/Search.vue';
import MainTable from '@/Components/Main/Admin/Components/Tables/MainTable.vue';
import SaveClient from "@/Pages/Admin/Clients/Partials/SaveClient.vue";
import EditButton from "@/Components/Main/Admin/Components/Buttons/Tables/EditButton.vue";
import DeleteClient from "@/Pages/Admin/Clients/Partials/DeleteClient.vue";

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

const { hasPermission } = usePermission();
const canCreate = hasPermission("admin.clients.create");
const canEdit = hasPermission("admin.clients.edit");
const canDelete = hasPermission("admin.clients.destroy");

const CreateComponent = canCreate || canEdit ? SaveClient : null;
const EditComponent = canEdit ? EditButton : null;
const DeleteComponent = canDelete ? DeleteClient : null;

const openModal = (op, data) => {
    callOpenModal.value.openModal(op, data);
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
            <component :is="CreateComponent" ref="callOpenModal" :filter="filter" :page="page"/>
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
                    <div class="flex items-center justify-end">
                        <component :is="EditComponent" @click="openModal(2, tb)"
                                   :key="tb.id + 'editBtn'"/>

                        <component :is="DeleteComponent" :id="tb.id" :filter="filter" :page="page"
                                   :key="tb.id + 'deleteBtn'"/>
                    </div>
                </td>
            </tr>
        </template>
    </MainTable>
</template>
