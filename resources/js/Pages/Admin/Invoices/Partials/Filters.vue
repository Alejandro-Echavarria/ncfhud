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
        type: Array,
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

const form = useForm({
    client: props.filters.client,
    month: currentMonth,
    year: currentYear,
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

const debounceData = () => debounce(getData, 600);

onMounted(() => {
    debounceData.value = debounce(getData, 600);
});

watch(form, () => {
    debounceData.value();
});

const getData = () => {
    router.get(route(props.url), pickBy({'client': form.client, 'month': form.month, 'year': form.year}), {
        preserveScroll: true,
        preserveState: true,
        replace: true
    });
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
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
        <div class="sm:col-span-2">
            <InputLabel for="client" value="Cliente" />
            <VueSelect id="client"  label="business_name" v-model="form.client" :options="clients" :reduce="options => options.id" />
        </div>

        <div>
            <InputLabel for="month" value="Mes" />
            <VueSelect id="month" label="name" v-model="form.month" :options="monthOptions" :reduce="options => options.id" />
        </div>

        <div>
            <InputLabel for="year" value="Año" />
            <VueSelect id="year" label="name" v-model="form.year" :options="yearOptions" :reduce="options => options.id" />
        </div>
    </div>
</template>