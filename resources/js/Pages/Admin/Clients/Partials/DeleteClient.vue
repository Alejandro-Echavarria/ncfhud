<script setup>
import { useForm } from "@inertiajs/vue3";
import TableButton from "@/Components/Main/Admin/Components/Buttons/TableButton.vue";
import TrashIcon from "@/Components/Main/Admin/Components/Icons/TrashIcon.vue";
import SaveAlert from "@/Helpers/Alerts/SaveAlert.js";
import DeleteAlert from "@/Helpers/Alerts/DeleteAlert.js";

const props = defineProps({
    id: Number,
    filter: String,
    page: String,
});

const form = useForm({
    id: props.id
});

const ok = (msj, type, timer, toast, title) => {
    SaveAlert(msj, type, timer, toast, title);
};

const destroy = (id) => {
    DeleteAlert().then((result) => {
        if (result.isConfirmed) {
            form.transform((data) => ({
                ...data,
                search: props.filter,
                page: props.page
            })).delete(route('admin.clients.destroy', id), {
                preserveScroll: true,
                onSuccess: () => {
                    ok('Cliente eliminado');
                },
                onError: (errors) => {
                    if (errors.delete) {
                        ok(errors.delete, 'error', null, false, 'Error');
                    }
                },
            });
        }
    });
};
</script>

<template>
    <TableButton>
        <TrashIcon @click="destroy(id)" :class="{ 'opacity-25': form.processing }"
                   :disabled="form.processing" class="w-4 h-4 text-red-500"/>
    </TableButton>
</template>