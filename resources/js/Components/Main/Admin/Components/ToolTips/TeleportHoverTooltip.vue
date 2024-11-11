<template>
    <div @mouseenter="showTooltip = true" @mouseleave="showTooltip = false" class="relative inline-block">
        <slot/>
        <teleport to="body">
            <div v-if="showTooltip" :style="tooltipStyle" class="tooltip z-50">
                {{ content }}
                <div class="tooltip-arrow"></div>
            </div>
        </teleport>
    </div>
</template>

<script setup>
import {ref, computed, onMounted, onUnmounted} from 'vue';

const props = defineProps({
    content: {
        type: String,
        required: true
    }
});

const showTooltip = ref(false);
const tooltipPosition = ref({top: 0, left: 0});

const updateTooltipPosition = (event) => {
    const {top, left, height} = event.target.getBoundingClientRect();
    tooltipPosition.value = {
        top: top + window.scrollY - height - 10, // Ajuste de posición vertical
        left: left + window.scrollX + event.target.offsetWidth / 2
    };
};

const tooltipStyle = computed(() => ({
    position: 'absolute',
    top: `${tooltipPosition.value.top}px`,
    left: `${tooltipPosition.value.left}px`,
    transform: 'translateX(-50%)'
}));

onMounted(() => {
    window.addEventListener('mousemove', updateTooltipPosition);
});

onUnmounted(() => {
    window.removeEventListener('mousemove', updateTooltipPosition);
});
</script>

<style>
.tooltip {
    background-color: #000;
    color: #fff;
    padding: 0.5rem 1rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    white-space: nowrap;
    pointer-events: none;
}

.tooltip-arrow {
    position: absolute;
    bottom: -5px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 0;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 5px solid #000;
}
</style>
