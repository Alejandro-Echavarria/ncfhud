<script setup>
import { watch } from "vue";
import { Head, useForm } from "@inertiajs/vue3";
import MainLayout from "@/Components/Main/Admin/Layout/MainLayout.vue";
import MainTitle from "@/Components/Main/Admin/Components/Titles/MainTitle.vue";
import Filters from "@/Pages/Admin/Invoices/Partials/Filters.vue";
import MainTable from "@/Components/Main/Admin/Components/Tables/MainTable.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

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
    year: props.yearFilter,
    file: null
});

const thead = ['ncf', 'Fecha comprobante'];
const url = 'admin.invoicescompare.index';

watch(
    () => [props.clientFilter, props.monthFilter, props.yearFilter],
    ([newClient, newMonth, newYear]) => {
        form.client = newClient;
        form.month = newMonth;
        form.year = newYear;
    }
);

const compare = () => {
    form.transform((data) => ({
        ...data,
        search: props.filter,
        page: props.page,
    })).post(route('admin.invoicescompare.compare'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            // ok('Client created');
        },
        onError: () => {
            console.log("error");
        }
    });
};
</script>

<template>
    <Head title="Comparar facturas"/>

    <MainTitle>Comparar facturas</MainTitle>

    <div class="space-y-6">
        <Filters :clients="clients" :filters="{ client: clientFilter, month: monthFilter, year: yearFilter }"
                 :url="url"/>

        <div>
            <InputLabel for="file" value="Cargar archivo"/>
            <div class="flex w-full space-x-6 mt-2">
                <div class="w-full">
                    <label for="file" class="sr-only">Choose file</label>
                    <input type="file" name="file" id="file" @input="form.file = $event.target.files[0]"
                           class="block w-full border-0 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-700 sm:text-sm sm:leading-6 transition rounded-lg text-sm
                       file:bg-gray-50 overflow-hidden
                       file:border-0 file:ring-1 file:rounded-l-lg file:ring-gray-300 file:focus:ring-inset file:focus:ring-indigo-700 file:focus:ring-2 file:me-4 file:py-2 file:px-4 file:transition">
                </div>

                <div>
                    <PrimaryButton @click="compare()">Comparar</PrimaryButton>
                </div>
            </div>
        </div>

        <div class="grid sm:grid-cols-8">
            <div class="sm:col-span-4">
                <MainTable :pagination="invoices">
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
                        </tr>
                    </template>
                </MainTable>
            </div>
        </div>
    </div>
</template>
