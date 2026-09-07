<script setup>
const props = defineProps({
    label: String,
    value: String,
});
</script>

<template>
    <div class="row">
        <label class="col-12 label-size fw-bold mb-2">
            {{ label }}
        </label>
        <div class="col-12">
            <div
                v-html="value"
                class="border border-secondary content-editor-show p-2"
            ></div>
        </div>
    </div>
</template>

<style scoped>
.content-editor-show {
    max-height: 150px;
    overflow-y: auto;
}
</style>
