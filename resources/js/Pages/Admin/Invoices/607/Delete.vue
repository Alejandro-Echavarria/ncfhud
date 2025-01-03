<script setup>
import { watch, computed } from "vue";
import { Head, useForm } from "@inertiajs/vue3";
import MainLayout from "@/Components/Main/Admin/Layout/MainLayout.vue";
import MainTitle from "@/Components/Main/Admin/Components/Titles/MainTitle.vue";
import Filters from "@/Pages/Admin/Invoices/Partials/Filters.vue";
import MainTable from "@/Components/Main/Admin/Components/Tables/MainTable.vue";
import DangerButton from "@/Components/DangerButton.vue";
import Icon from "@/Components/Main/Admin/Components/Icons/Icon.vue";
import SaveAlert from "@/Helpers/Alerts/SaveAlert.js";
import InputError from "@/Components/InputError.vue";
import DeleteAlert from "@/Helpers/Alerts/DeleteAlert.js";

defineOptions({
    layout: MainLayout
});

const props = defineProps({
    clients: {
        type: Object,
        required: true
    },
    invoices: {
        type: Object,
        default: null
    },
    clientFilter: String,
    monthFilter: String,
    yearFilter: String,
    page: String
});

const form = useForm({
    client: props.clientFilter,
    month: props.monthFilter,
    year: props.yearFilter
});

const thead = ['ncf', 'Fecha comprobante', 'monto', 'itbis'];
const url = 'admin.invoices.delete';

watch(
    () => [props.clientFilter, props.monthFilter, props.yearFilter],
    ([newClient, newMonth, newYear]) => {
        form.client = newClient;
        form.month = newMonth;
        form.year = newYear;
    }
);

const hasInvoices = computed(() => {
    return props.invoices.data?.length > 0;
});

const destroy = () => {
    DeleteAlert().then((result) => {
        if (result.isConfirmed) {
            form.delete(route('admin.invoices.destroy', form.client), {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    ok('Facturas Eliminadas');
                },
                onError: (errors) => {
                    if (errors.data) {
                        ok(errors.data, 'error', null, false, 'Error');
                    }
                }
            });
        }
    });
}

const ok = (msj, type, timer, toast, title) => {
    SaveAlert(msj, type, timer, toast, title);
};
</script>

<template>
    <Head title="Eliminar facturas 607"/>

    <MainTitle>Eliminar facturas 607</MainTitle>

    <div class="space-y-6">
        <Filters :clients="clients"
                 :filters="{ client: clientFilter, month: monthFilter, year: yearFilter }"
                 :url="url"/>

        <div>
            <template v-for="error in form.errors">
                <InputError :message="error" class="mt-2"/>
            </template>
        </div>

        <div>
            <MainTable :pagination="invoices">
                <template #createButton>
                    <DangerButton
                        :disabled="!hasInvoices"
                        class="w-full disabled:opacity-50 disabled:cursor-not-allowed transition ease-linear duration-300"
                        :onclick="destroy"
                    >
                        <Icon icon="Trash" class="mr-1"/>
                        Eliminar facturas
                    </DangerButton>
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
                        <td class="px-4 py-3 flex items-center justify-end">

                        </td>
                    </tr>
                </template>
            </MainTable>
        </div>
    </div>
</template>
