<script setup>
import { ref, watch, onMounted } from "vue";
import { useForm, router } from '@inertiajs/vue3';
import pickBy from 'lodash/pickBy';
import InputLabel from "@/Components/InputLabel.vue";
import VueSelect from "@/Components/Main/Admin/Components/Selects/VueSelect.vue";

const props = defineProps({
    clients: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        required: true
    },
    url: {
        type: String,
        required: true
    },
});

const currentDate = new Date();
const currentMonth = currentDate.getMonth() + 1; // getMonth() returns 0-based index
const currentYear = currentDate.getFullYear();

const params = new URLSearchParams(window.location.search);
const clientParam = params.get('client');
const monthParam = params.get('month');
const yearParam = params.get('year');
const perPageParam = params.get('per_page');

const form = useForm({
    client: clientParam || props.filters.client,
    month: monthParam || currentMonth,
    year: yearParam || currentYear,
    perPage: perPageParam || 10,
});

const monthOptions = ref([
    {
        'id': 1,
        'name': 'Enero'
    },
    {
        'id': 2,
        'name': 'Febrero'
    },
    {
        'id': 3,
        'name': 'Marzo'
    },
    {
        'id': 4,
        'name': 'Abril'
    },
    {
        'id': 5,
        'name': 'Mayo'
    },
    {
        'id': 6,
        'name': 'Junio'
    },
    {
        'id': 7,
        'name': 'Julio'
    },
    {
        'id': 8,
        'name': 'Agosto'
    },
    {
        'id': 9,
        'name': 'Septiembre'
    },
    {
        'id': 10,
        'name': 'Octubre'
    },
    {
        'id': 11,
        'name': 'Noviembre'
    },
    {
        'id': 12,
        'name': 'Diciembre'
    }
]);
const yearOptions = ref(Array.from({ length: 2041 - 2010 }, (_, i) => ({ 'id': `${2010 + i}`, 'name': `${2010 + i}` })));
const perPageOptions = ref([
    {
        'id': 10,
        'name': '10'
    },
    {
        'id': 25,
        'name': '25'
    },
    {
        'id': 50,
        'name': '50'
    },
    {
        'id': 100,
        'name': '100'
    }
]);

const debounceData = () => debounce(getData, 100);

onMounted(() => {
    debounceData.value = debounce(getData, 100);
});

watch(form, () => {
    debounceData.value();
});

const getData = () => {
    if (form.client != null) {
        router.get(route(props.url), pickBy({'client': form.client, 'month': form.month, 'year': form.year, 'per_page': form.perPage}), {
            preserveScroll: true,
            preserveState: true,
            replace: true
        });
    }
};

const debounce = (func, wait) => {
    let timeout;
    return function () {
        const context = this,
            args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(function () {
            timeout = null;
            func.apply(context, args);
        }, wait);
    };
};
</script>

<template>
    <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-12 gap-6">
        <div class="sm:col-span-3 md:col-span-12">
            <InputLabel for="client" value="Cliente" />
            <VueSelect id="client" label="business_name" v-model="form.client" :options="clients" :reduce="options => options.id" />
        </div>

        <div class="sm:col-span-1 md:col-span-4">
            <InputLabel for="month" value="Mes" />
            <VueSelect id="month" label="name" v-model="form.month" :options="monthOptions" :reduce="options => options.id" />
        </div>

        <div class="sm:col-span-1 md:col-span-4">
            <InputLabel for="year" value="Año" />
            <VueSelect id="year" label="name" v-model="form.year" :options="yearOptions" :reduce="options => options.id" />
        </div>

        <div class="sm:col-span-1 md:col-span-4">
            <InputLabel for="perPage" value="Registros por página" />
            <VueSelect id="perPage" label="name" v-model="form.perPage" :options="perPageOptions" :reduce="options => options.id" />
        </div>
    </div>
</template>