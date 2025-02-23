<script setup>
import { router } from '@inertiajs/vue3';
import { onBeforeMount, watch } from 'vue';

const props = defineProps({
    pagination: {
        type: Object,
        required: true
    }
});

onBeforeMount(() => {
    if (props.pagination.last_page < props.pagination.current_page) {
        const url = props.pagination.last_page_url;

        router.visit(url);
    }
});

watch(() => props.pagination?.data?.length, (newLength, oldLength) => {
    // Verificar si la longitud es cero y realizar la redirección
    if (newLength === 0) {
        const prevPageUrl = props.pagination?.prev_page_url?.split("?page=")[1];

        if (prevPageUrl) {
            router.visit(router.page.url, {
                preserveScroll: false,
                preserveState: true,
                data: {
                    page: prevPageUrl
                }
            });
        }
    }
});

const changePage = (url) => {
    const page = url.split("?page=")[1];
    router.visit(router.page.url, {
        preserveScroll: false,
        preserveState: true,
        data: {
            page: page
        }
    });
};
</script>

<template>
    <div
        class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-3 md:space-y-0 p-4 border-t border-[#000000]/[0.16]">
        <div class="w-full sm:hidden">
            <div class="flex justify-between mb-4">
                <span v-if="pagination.prev_page_url === null"
                      class="relative inline-flex items-center py-2 text-sm font-semibold text-gray-600 focus:z-20 cursor-not-allowed">
                    Anterior
                </span>

                <a v-else @click="changePage(pagination.prev_page_url)" :key="'mobile-link'"
                   :class="pagination.prev_page_url == null
                    ? 'relative inline-flex items-center py-2 text-sm font-semibold text-gray-600 focus:z-20 cursor-not-allowed'
                    : 'relative inline-flex items-center py-2 text-sm font-semibold text-gray-900 focus:z-20 cursor-pointer'">
                    Anterior
                </a>

                <span v-if="pagination.next_page_url === null"
                      class="relative inline-flex items-center py-2 text-sm font-semibold text-gray-600 focus:z-20 cursor-not-allowed">
                    Siguiente
                </span>

                <a v-else @click="changePage(pagination.next_page_url)" :key="'mobile-link'"
                   :class="pagination.next_page_url == null
                    ? 'relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-600 focus:z-20 cursor-not-allowed'
                    : 'relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 focus:z-20 cursor-pointer'">
                    Siguiente
                </a>
            </div>
            <div class="flex justify-center">
                <p class="text-sm text-gray-700">
                    Mostrando
                    <span class="font-small">{{ pagination.from }}</span>
                    al
                    <span class="font-small">{{ pagination.to }}</span>
                    de
                    <span class="font-small">{{ pagination.total }}</span>
                    resultados
                </p>
            </div>
        </div>

        <div class="hidden sm:block w-full">
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-center mb-6">
                <div v-if="pagination.links.length > 3">
                    <nav class="isolate inline-flex -space-x-px rounded-md" aria-label="Pagination">
                        <template v-for="(link, index) in pagination.links">
                            <div v-if="link.url == null" :key="'botons-' + index"
                                 class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-600 focus:z-20 cursor-not-allowed select-none"
                                 v-html="link.label">
                            </div>

                            <a v-else :key="'links-' + index" @click="changePage(link.url)"
                               class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 focus:z-20 cursor-pointer select-none"
                               :class="link.active
                                    ? 'z-10 border-b-2 border-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 focus:outline-none focus:border-indigo-500 transition duration-150 ease-in-out'
                                    : ''" v-html="link.label">
                            </a>
                        </template>
                    </nav>
                </div>
            </div>

            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-center">
                <div>
                    <p class="text-sm text-gray-700">
                        Monstrando
                        <span class="font-medium">{{ pagination.from }}</span>
                        al
                        <span class="font-medium">{{ pagination.to }}</span>
                        de
                        <span class="font-medium">{{ pagination.total }}</span>
                        resultados
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>