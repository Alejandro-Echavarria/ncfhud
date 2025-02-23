<script setup>
import { ref } from 'vue';
import Pagination from '@/Components/Main/Admin/Components/Paginations/Pagination.vue';
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Icon from "@/Components/Main/Admin/Components/Icons/Icon.vue";
import HideBackground from "@/Components/Main/Admin/Components/Backgrounds/HideBackground.vue";
import SaveAlert from "@/Helpers/Alerts/SaveAlert.js";
import HoverCSSTooltip from "@/Components/Main/Admin/Components/ToolTips/HoverCSSTooltip.vue";

const props = defineProps({
    pagination: {
        type: Object,
    },
    copyTable: {
        type: Boolean,
        default: false
    },
    showAllButton: {
        type: Boolean,
        default: false
    },
    tailwindCopy: {
        type: Boolean,
        default: false
    },
    actions: {
        type: Boolean,
        default: true
    }
});

// Ref para la tabla
const tableRef = ref(null);
const showAll = ref(true);

const copyTableData = () => {
    // Selecciona la tabla usando la referencia
    const table = tableRef.value;

    if (!table) {
        console.error("La referencia a la tabla es nula.");
        return;
    }

    // Obtén todas las filas de la tabla
    const rows = table.querySelectorAll('tr');

    // Extrae los datos de cada celda y formato en una cadena
    const textToCopy = Array.from(rows)
        .map(row => {
            // Obtén las celdas de la fila
            const cells = Array.from(row.querySelectorAll('td'));
            // Verifica que la fila tenga celdas y no sea una fila vacía
            if (cells.length > 0) {
                return cells.map(cell => cell.textContent.trim()).join('\t');
            }
            return ''; // Filas vacías no se incluyen en el texto final
        })
        .filter(row => row.trim() !== '') // Elimina filas vacías
        .join('\n'); // Usa saltos de línea entre filas

    // Usa un enfoque alternativo para copiar al portapapeles
    const textarea = document.createElement('textarea');
    textarea.value = textToCopy;
    textarea.style.position = 'absolute';
    textarea.style.left = '-9999px'; // Mueve el textarea fuera de la vista
    document.body.appendChild(textarea);
    textarea.select();

    try {
        document.execCommand('copy'); // Copia el texto al portapapeles
        ok('Datos copiados');
        console.log("Datos copiados al portapapeles con éxito.");
    } catch (error) {
        ok('Error al copiar los datos', 'error');
        console.error("No se pudo copiar los datos: ", error);
    } finally {
        document.body.removeChild(textarea); // Elimina el textarea temporal
    }
};


const ok = (msj, type, timer, toast, title) => {
    SaveAlert(msj, type, timer, toast, title);
};
</script>

<template>
    <div :class="showAll && showAllButton ? 'h-72' : 'h-full'"
         class="relative bg-white dark:bg-gray-800 border rounded-xl overflow-hidden">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
            <div class="w-full md:w-1/2">
                <slot name="search"/>
            </div>

            <div
                class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end space-x-7 flex-shrink-0">
                <slot name="createButton"/>
            </div>

            <div v-if="showAllButton || copyTable" class="flex gap-3">
                <div v-if="showAllButton">
                    <HoverCSSTooltip content="Mostrar todas las filas">
                        <SecondaryButton @click="() => showAll = !showAll">
                            <Icon icon="Arrow"
                                  :class="[!showAll ?
                              '-rotate-0 transition duration-150 ease-linear':
                              '-rotate-90 transition duration-150 ease-linear'
                          ]"/>
                        </SecondaryButton>
                    </HoverCSSTooltip>
                </div>

                <div v-if="copyTable">
                    <HoverCSSTooltip content="Copiar tabla">
                        <SecondaryButton @click="copyTableData">
                            <Icon icon="Copy"/>
                        </SecondaryButton>
                    </HoverCSSTooltip>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table ref="tableRef" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs bg-gray-50 dark:bg-gray-700 text-gray-700 uppercase dark:text-gray-400">
                <tr>
                    <slot name="thead"/>
                    <th v-if="actions" scope="col" class="px-4 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
                </thead>
                <tbody :class="tailwindCopy && 'select-all'">
                <slot name="tbody"/>
                </tbody>
            </table>
        </div>

        <HideBackground v-if="showAll && showAllButton"/>

        <template v-if="pagination && pagination.links">
            <Pagination :pagination="pagination"/>
        </template>
    </div>
</template>
