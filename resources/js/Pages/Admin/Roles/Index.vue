<script setup>
import { ref } from "vue";
import { Head } from "@inertiajs/vue3";
import MainLayout from "@/Components/Main/Admin/Layout/MainLayout.vue";
import MainTitle from "@/Components/Main/Admin/Components/Titles/MainTitle.vue";
import MainTable from "@/Components/Main/Admin/Components/Tables/MainTable.vue";
import Icon from "@/Components/Main/Admin/Components/Icons/Icon.vue";
import TableButton from "@/Components/Main/Admin/Components/Buttons/TableButton.vue";
import Search from "@/Components/Main/Admin/Components/Searchs/Search.vue";
import SaveRole from "@/Pages/Admin/Roles/Partials/SaveRole.vue";
import EditButton from "@/Components/Main/Admin/Components/Buttons/Tables/EditButton.vue";
import DeleteRole from "@/Pages/Admin/Roles/Partials/DeleteRole.vue";
import { usePermission } from "@/Composables/permissions";


defineOptions({
    layout: MainLayout
});

const props = defineProps({
    roles: {
        type: Object,
        required: true,
    },
    permissions: {
        type: Object,
        required: true,
    },
    filter: String,
    page: String
});

const thead = ['nombre'];
const url = 'admin.roles.index';
const callOpenModal = ref(null);

const { hasPermission } = usePermission();
const canCreateRole = hasPermission("admin.roles.create");
const canEditRole = hasPermission("admin.roles.edit");
const canDeleteRole = hasPermission("admin.roles.destroy");

const CreateRoleComponent = canCreateRole ? SaveRole : null;
const EditButtonComponent = canEditRole ? EditButton : null;
const DeleteRoleComponent = canDeleteRole ? DeleteRole : null;

const openModal = (op, data) => {
    callOpenModal.value.openModal(op, data);
};
</script>

<template>
    <Head title="Roles"/>

    <MainTitle>Roles</MainTitle>

    <MainTable :pagination="roles">
        <template #search>
            <Search :filter="filter" :url="url"/>
        </template>

        <template #createButton>
            <component :is="CreateRoleComponent" ref="callOpenModal" :permissions="permissions" :filter="filter"
                       :page="page"/>
        </template>

        <template #thead>
            <th v-for="(th, key) in thead" scope="col" class="px-4 py-3" :key="key + 'th'">
                {{ th }}
            </th>
        </template>

        <template #tbody>
            <tr v-for="tb in roles.data"
                class="dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition ease-linear duration-300"
                :key="tb.id + 'tb'">
                <td class="px-4 py-3">{{ tb.name }}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center justify-end">
                        <component :is="EditButtonComponent" @click="openModal(2, tb)"
                                   :key="tb.id + 'editBtn'"/>

                        <component :is="DeleteRoleComponent" :id="tb.id" :filter="filter" :page="page"
                                   :key="tb.id + 'deleteBtn'"/>
                    </div>
                </td>
            </tr>
        </template>
    </MainTable>
</template>