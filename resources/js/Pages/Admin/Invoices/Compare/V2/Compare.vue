<script setup>
import { watch, ref } from "vue";
import { Head, useForm } from "@inertiajs/vue3";
import MainLayout from "@/Components/Main/Admin/Layout/MainLayout.vue";
import MainTitle from "@/Components/Main/Admin/Components/Titles/MainTitle.vue";
import Filters from "@/Pages/Admin/Invoices/Partials/Filters.vue";
import MainTable from "@/Components/Main/Admin/Components/Tables/MainTable.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import H2Title from "@/Components/Main/Admin/Components/Titles/H2Title.vue";
import InputError from "@/Components/InputError.vue";

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

const thead = ['rnc', 'ncf', 'Fecha', 'Monto', 'itbis'];
const thead_differences = ['rnc', 'ncf', 'Detalle'];
const url = 'admin.v2.invoicescompare.index';

const notInExcel = ref([]);
const notInDatabase = ref([]);
const differences = ref([]);
const comparing = ref(false);
const errorsMessage = ref([]);

watch(
    () => [props.clientFilter, props.monthFilter, props.yearFilter],
    ([newClient, newMonth, newYear]) => {
        form.client = newClient;
        form.month = newMonth;
        form.year = newYear;
    }
);

const compare = async () => {
    errorsMessage.value = [];
    comparing.value = true;
    const formData = new FormData();

    // Agregar los datos del formulario al FormData
    for (const [key, value] of Object.entries(form.data())) {
        formData.append(key, value);
    }

    try {
        const response = await axios.post(route('api.v2.invoicescompare.compare'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        notInExcel.value = response.data.data.notIn606;
        notInDatabase.value = response.data.data.notIn607;
        differences.value = response.data.data.differences;
    } catch (error) {
        const errors = error.response?.data?.errors || {};
        errorsMessage.value = Object.values(errors).flat();
    }

    comparing.value = false;
};
</script>

<template>
    <Head title="Comparar facturas"/>

    <MainTitle>Comparar facturas</MainTitle>

    <div class="space-y-12">
        <div>
            <Filters :clients="clients" :filters="{ client: clientFilter, month: monthFilter, year: yearFilter }"
                     :url="url"/>

            <div class="mt-6">
                <div class="flex w-full space-x-6 mt-2 justify-end">
                    <div>
                        <PrimaryButton @click="compare()" :class="{ 'opacity-25 animate-pulse': comparing }"
                                       :disabled="comparing">
                            <template v-if="comparing">
                                Comparando..
                            </template>

                            <template v-else>
                                Comparar
                            </template>
                        </PrimaryButton>
                    </div>
                </div>
            </div>

            <div>
                <template v-for="error in errorsMessage">
                    <InputError :message="error" class="mt-2"/>
                </template>
            </div>
        </div>

        <div>
            <H2Title>
                NCF no declarados en el reporte de terceros DGII
            </H2Title>

            <MainTable :actions="false" :copy-table="true" :show-all-button="true">
                <template #thead>
                    <th v-for="(th, key) in thead" scope="col" class="px-4 py-3" :key="key + 'th'">
                        {{ th }}
                    </th>
                </template>

                <template #tbody>
                    <tr v-for="tb in notInExcel"
                        class="dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition ease-linear duration-300"
                        :key="tb.id + 'tb'">
                        <td class="px-4 py-3">{{ tb.rnc }}</td>
                        <td class="px-4 py-3">{{ tb.ncf }}</td>
                        <td class="px-4 py-3">{{ tb.proof_date }}</td>
                        <td class="px-4 py-3">{{ tb.amount }}</td>
                        <td class="px-4 py-3">{{ tb.itbis }}</td>
                        <!--                        <td class="px-4 py-3">-->
                        <!--                            <div>-->
                        <!--                                <div>-->
                        <!--                                    <span class="font-semibold text-gray-700">Monto: </span>-->
                        <!--                                    {{ tb.amount }}-->
                        <!--                                </div>-->
                        <!--                            </div>-->
                        <!--                        </td>-->
                    </tr>
                </template>
            </MainTable>
        </div>

        <div>
            <H2Title>
                NCF declarados por terceros que no corresponden al 607 reportado
            </H2Title>

            <MainTable :actions="false" :copy-table="true" :show-all-button="true">
                <template #thead>
                    <th v-for="(th, key) in thead" scope="col" class="px-4 py-3" :key="key + 'th'">
                        {{ th }}
                    </th>
                </template>

                <template #tbody>
                    <tr v-for="tb in notInDatabase"
                        class="dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition ease-linear duration-300"
                        :key="tb.id + 'tb'">
                        <td class="px-4 py-3">{{ tb.rnc }}</td>
                        <td class="px-4 py-3">{{ tb.ncf }}</td>
                        <td class="px-4 py-3">{{ tb.proof_date }}</td>
                        <td class="px-4 py-3">{{ tb.amount }}</td>
                        <td class="px-4 py-3">{{ tb.itbis }}</td>
                    </tr>
                </template>
            </MainTable>
        </div>

        <div>
            <H2Title>
                NCF con diferencias
            </H2Title>

            <MainTable :actions="false" :copy-table="true" :show-all-button="true">
                <template #thead>
                    <th v-for="(th, key) in thead_differences" scope="col"
                        :class="['px-4 py-3', key === 2 && 'bg-white']" :key="key + 'th'">
                        {{ th }}
                    </th>
                </template>

                <template #tbody>
                    <tr v-for="tb in differences"
                        class="hover:bg-gray-50 dark:hover:bg-gray-600 transition ease-linear duration-300"
                        :key="tb.id + 'tb'">
                        <td class="px-4 py-3">{{ tb.invoices607.rnc }}</td>
                        <td class="px-4 py-3">{{ tb.invoices607.ncf }}</td>
                        <td class="px-4 py-3 border border-gray-300 p-2 rounded-lg"
                            v-for="(difference, key) in tb.differences" :key="key">
                            <div class="flex text-xs min-w-[150px] flex-shrink-0">
                                <div class="pr-1 font-bold justify-center uppercase text-gray-700">{{ key }}</div>
                                <div class="flex gap-3">
                                    <div class="whitespace-nowrap">
                                        <span class="font-semibold text-gray-700">| 606:</span>
                                        {{ difference.invoices606 }}
                                    </div>

                                    <div class="whitespace-nowrap">
                                        <span class="font-semibold text-gray-700">&nbsp;607:</span>
                                        {{ difference.invoices607 }}
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </template>
            </MainTable>
        </div>

        <div>
            <div>
                <H2Title>
                    Facturas 607
                </H2Title>

                <MainTable :pagination="invoices" :actions="false">
                    <template #thead>
                        <th v-for="(th, key) in thead" scope="col" class="px-4 py-3" :key="key + 'th'">
                            {{ th }}
                        </th>
                    </template>

                    <template #tbody>
                        <tr v-for="tb in invoices.data"
                            class="dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition ease-linear duration-300"
                            :key="tb.id + 'tb'">
                            <td class="px-4 py-3">{{ tb.rnc }}</td>
                            <td class="px-4 py-3">{{ tb.ncf }}</td>
                            <td class="px-4 py-3">{{ tb.proof_date }}</td>
                            <td class="px-4 py-3">{{ tb.amount }}</td>
                            <td class="px-4 py-3">{{ tb.itbis }}</td>
                        </tr>
                    </template>
                </MainTable>
            </div>
        </div>
    </div>
</template>
