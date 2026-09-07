<script setup>
defineProps({
    type: {
        type: String,
        default: "submit",
    },
    isProcessing: {
        type: Boolean,
        default: false,
    },
    attrClass: {
        type: String,
        default: "",
    },
    btnColor: {
        type: String,
        default: "btn-primary",
    },
});

const emits = defineEmits(["onCLickSubmit"]);

const onClick = () => {
    emits("onCLickSubmit", true);
};
</script>

<template>
    <button
        :type="type"
        :disabled="isProcessing"
        :class="'btn rounded-06 ' + btnColor + ' ' + attrClass"
        @click="onClick"
    >
        <slot />
        <div v-if="isProcessing" class="spinner-border spinner-border-sm ms-2">
            <span class="visually-hidden">Loading...</span>
        </div>
    </button>
</template>
