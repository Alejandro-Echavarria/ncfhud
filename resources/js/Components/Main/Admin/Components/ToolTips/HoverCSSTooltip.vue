<script setup>
const props = defineProps({
    content: {
        type: String,
        required: true,
        default: null
    }
})
</script>

<template>
    <div class="tooltip-wrapper">
        <slot/>
        <span :class="['text-xs', 'tooltip--bottom']" class="tooltip__text">{{ content }}</span>
    </div>
</template>

<style scoped>
.tooltip__text {
    visibility: hidden;
    opacity: 0;
    transition: opacity 0.3s ease;

    color: #ffffff;
    text-align: center;
    padding: 4px 8px;
    border-radius: 6px;
    min-width: 80px;
    max-width: 200px; /* Limitar el ancho máximo */
    background: #1f2937;
    white-space: nowrap;
    word-wrap: break-word;

    position: absolute;
    z-index: 1;
    transform: translateX(-50%); /* Centrado horizontal */
}

.tooltip-wrapper {
    display: inline-block;
    position: relative;
}

.tooltip-wrapper:hover .tooltip__text {
    visibility: visible;
    opacity: 1;
}

/* Posicionamiento del tooltip en la parte inferior */
.tooltip--bottom {
    bottom: -100%; /* Coloca el tooltip por debajo del botón */
    left: 50%; /* Centrado horizontal */
}

.tooltip__text::after {
    content: " ";
    position: absolute;
    border-width: 5px;
    border-style: solid;
}

.tooltip--bottom::after {
    bottom: 100%; /* Posiciona la flecha justo encima del tooltip */
    left: 50%;
    transform: translateX(-50%); /* Centra la flecha */
    border-color: transparent transparent #1f2937 transparent;
}

:slotted(*):focus + .tooltip__text {
    visibility: visible;
    opacity: 1;
}
</style>
