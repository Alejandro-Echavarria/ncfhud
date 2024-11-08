<script setup>
import {watch, ref} from "vue";
import {Head, useForm} from "@inertiajs/vue3";
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
    year: props.yearFilter,
    file: null
});

const thead = ['rnc', 'ncf', 'Fecha', 'Monto', 'itbis'];
const thead_differences = ['rnc', 'ncf', 'Detalle'];
const url = 'admin.invoicescompare.index';

const excel = ref([]);
const notInExcel = ref([]);
const notInDatabase = ref([]);
const differences = ref([]);
const comparing = ref(false);

watch(
    () => [props.clientFilter, props.monthFilter, props.yearFilter],
    ([newClient, newMonth, newYear]) => {
        form.client = newClient;
        form.month = newMonth;
        form.year = newYear;
    }
);

const compare = async () => {
    comparing.value = true;
    const formData = new FormData();

    // Agregar los campos de datos al FormData
    formData.append('search', props.filter);
    formData.append('page', props.page);

    // Agregar los datos del formulario al FormData
    for (const [key, value] of Object.entries(form.data())) {
        formData.append(key, value);
    }

    // Suponiendo que tienes un campo de archivo en tu formulario
    // Asegúrate de que el archivo esté incluido en los datos del formulario
    if (form.file) {
        formData.append('file', form.file);
    }

    try {
        const response = await axios.post(route('api.v1.invoicescompare.compare'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        excel.value = response.data.data;
        notInExcel.value = response.data.data.not_in_excel;
        notInDatabase.value = response.data.data.not_in_database;
        differences.value = response.data.data.differences;
        // ok('Client created');
    } catch (error) {
        console.log("error");
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
                <template v-for="error in form.errors">
                    <InputError :message="error" class="mt-2"/>
                </template>
            </div>
        </div>

        <div>
            <H2Title>
                NCF no declarados en el reporte de terceros DGII
            </H2Title>

            <MainTable :actions="false" :copy-table="true" :show-all="true">
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

            <MainTable :actions="false" :copy-table="true">
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

            <MainTable :actions="false" :copy-table="true">
                <template #thead>
                    <th v-for="(th, key) in thead_differences" scope="col" class="px-4 py-3" :key="key + 'th'">
                        {{ th }}
                    </th>
                </template>

                <template #tbody>
                    <tr v-for="tb in differences"
                        class="dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition ease-linear duration-300"
                        :key="tb.id + 'tb'">
                        <td class="px-4 py-3">{{ tb.excel_row.rnc }}</td>
                        <td class="px-4 py-3">{{ tb.excel_row.ncf }}</td>
                        <td class="px-4 py-3 flex-col border border-gray-300 p-2 rounded-lg" v-for="(difference, key) in tb.differences">
                            <div class=" font-bold flex justify-center uppercase text-gray-700">{{ key }}</div>
                            <div class="flex gap-3">
                                <td>
                                    <span class="font-semibold text-gray-700">606:</span>
                                    {{ difference.excel }}
                                </td>

                                <td>
                                    <span class="font-semibold text-gray-700">607:</span>
                                    {{ difference.database }}
                                </td>
                            </div>


                        </td>
<!--                        <td class="px-4 py-3">-->
<!--                            <div class="flex gap-6">-->
<!--                                <div v-for="(difference, key) in tb.differences"-->
<!--                                     class="flex-col border border-gray-300 p-2 rounded-lg">-->
<!--                                    <div class="font-bold flex justify-center uppercase text-gray-700">{{ key }}</div>-->
<!--                                    <div>-->
<!--                                        <span class="font-semibold text-gray-700">606:</span>-->
<!--                                        {{ difference.excel }}-->
<!--                                    </div>-->

<!--                                    <div>-->
<!--                                        <span class="font-semibold text-gray-700">607:</span>-->
<!--                                        {{ difference.database }}-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </td>-->
                    </tr>
                </template>
            </MainTable>
        </div>

        <div>
            <div>
                <H2Title>
                    Facturas
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
