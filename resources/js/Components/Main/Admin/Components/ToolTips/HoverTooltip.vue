<script setup>
import {computed} from "vue";

const props = defineProps({
    content: {
        type: String,
        required: true,
        default: null
    },
    orientation: {
        type: String,
        default: 'bottom'
    }
});


const orientationClass = computed(() => {
    return {
        'top': 'data-[tooltip]:after:bottom-[calc(100%+4px)] data-[tooltip]:before:[clip-path:polygon(100%_0,0_0,50%_100%)] data-[tooltip]:after:scale-50 hover:data-[tooltip]:after:scale-100',
        'bottom': 'data-[tooltip]:after:bottom-[calc(-100%+2px)] data-[tooltip]:before:bottom-[calc(-100%+28px)] data-[tooltip]:before:[clip-path:polygon(0%_100%,100%_100%,50%_0%)]',
    }[props.orientation];
});
</script>

<template>
    <!--
        Inspirado por Eduardo José
        https://uiverse.io/eduardojsc18/serious-seahorse-3
    -->
    <div
        :class="[orientationClass]"
        class="font-medium text-neutral-600 relative z-[10]
        data-[tooltip]:after:content-[attr(data-tooltip)] data-[tooltip]:after:invisible data-[tooltip]:after:origin-bottom data-[tooltip]:after:opacity-0 hover:data-[tooltip]:after:visible hover:data-[tooltip]:after:opacity-100  data-[tooltip]:after:transition-all data-[tooltip]:after:absolute data-[tooltip]:after:bg-black data-[tooltip]:after:left-1/2 data-[tooltip]:after:-translate-x-1/2 data-[tooltip]:after:-z-[1] data-[tooltip]:after:px-1.5 data-[tooltip]:after:py-1 data-[tooltip]:after:min-h-fit data-[tooltip]:after:min-w-fit data-[tooltip]:after:rounded-md data-[tooltip]:after:drop-shadow data-[tooltip]:before:drop-shadow data-[tooltip]:after:text-center data-[tooltip]:after:text-white data-[tooltip]:after:whitespace-nowrap data-[tooltip]:after:text-[12px] data-[tooltip]:before:invisible data-[tooltip]:before:opacity-0 hover:data-[tooltip]:before:visible hover:data-[tooltip]:before:opacity-100 data-[tooltip]:before:transition-all data-[tooltip]:before:bg-black data-[tooltip]:before:absolute data-[tooltip]:before:bottom-full
        data-[tooltip]:before:left-1/2 data-[tooltip]:before:-translate-x-1/2 data-[tooltip]:before:z-0 data-[tooltip]:before:w-3 data-[tooltip]:before:h-[4px]"
        :data-tooltip="content">
        <slot/>
    </div>
</template>