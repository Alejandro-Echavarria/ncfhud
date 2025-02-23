<script setup>
import { computed, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['close']);

watch(() => props.show, () => {
    if (props.show) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = null;
    }
});

const close = () => {
    if (props.closeable) {
        emit('close');
    }
};

const closeOnEscape = (e) => {
    if (e.key === 'Escape' && props.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    document.body.style.overflow = null;
});

const maxWidthClass = computed(() => {
    return {
        'sm': 'sm:max-w-sm',
        'md': 'sm:max-w-md',
        'lg': 'sm:max-w-lg',
        'xl': 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
        '3xl': 'sm:max-w-3xl',
        '4xl': 'sm:max-w-4xl',
        '5xl': 'sm:max-w-5xl',
        '6xl': 'sm:max-w-6xl',
        'full': 'sm:max-w-full',
    }[props.maxWidth];
});
</script>

<template>
    <teleport to="body">
        <transition leave-active-class="duration-100">
            <div id="modal" v-show="show" :class="maxWidthClass !== 'sm:max-w-full' && 'sm:px-4'"
                 class="fixed inset-0 overflow-hidden flex items-end sm:items-center justify-center z-50">
                <transition enter-active-class="ease-out duration-200" enter-from-class="opacity-0"
                            enter-to-class="opacity-100" leave-active-class="ease-in duration-100"
                            leave-from-class="opacity-100" leave-to-class="opacity-0">
                    <div v-show="show" class="absolute inset-0 bg-gray-900/50 backdrop-blur backdrop-filter opacity-100"
                         @click="close">
                    </div>
                </transition>

                <transition enter-active-class="ease-out duration-200"
                            enter-from-class="opacity-0 translate-y-64 sm:translate-y-0 sm:scale-95"
                            enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                            leave-active-class="ease-in duration-200"
                            leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                            leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    <div v-show="show" class="bg-white overflow-y-auto shadow-xl transform transition-all w-full"
                         :class="[maxWidthClass, maxWidthClass !== 'sm:max-w-full' && 'rounded-t-xl sm:rounded-xl']">
                        <slot v-if="show"/>
                    </div>
                </transition>
            </div>
        </transition>
    </teleport>
</template>
