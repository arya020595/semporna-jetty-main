<script setup>
import { Tooltip } from "bootstrap";
import { watch, ref, onMounted } from "vue";

const props = defineProps({
    elId: String,
    value: Object,
    existing: String,
    error: String,
    additionalAttr: Object,
});

const picture = ref(null);
const refTootipEl = ref(null);
let tooltipInstance = null;

const emits = defineEmits(["update:value", "update:existing"]);

watch(picture, (newValue) => {
    emits("update:value", newValue);

    // Update the title attribute
    refTootipEl.value.setAttribute("title", newValue?.name);

    // Manually update tooltip content
    tooltipInstance.setContent({ ".tooltip-inner": newValue?.name });
});

const onDelete = () => {
    emits("update:existing", "");
};

onMounted(() => {
    tooltipInstance = new Tooltip(refTootipEl?.value);
});
</script>

<template>
    <div class="d-flex align-items-center gap-1">
        <label
            :for="elId"
            class="btn btn-primary mb-0 flex-shrink-0"
            style="min-width: 210px"
            v-bind="additionalAttr"
        >
            <span class="material-icons"> cloud_upload </span>
            <span class="d-none ms-2 d-lg-inline"><slot /></span>
        </label>
        <button
            ref="refTootipEl"
            class="btn btn-light flex-shrink-0"
            :class="{ 'd-none': !picture }"
            data-bs-toggle="tooltip"
            :title="'file: ' + picture?.name"
            type="button"
        >
            <span class="material-icons"> attach_file </span>
        </button>
        <a
            class="btn btn-success flex-shrink-0"
            :class="{ 'd-none': !existing }"
            target="_blank"
            :href="existing"
        >
            <span class="material-icons">visibility</span>
        </a>
        <button
            type="button"
            class="btn btn-danger flex-shrink-0"
            :class="{ 'd-none': !existing }"
            @click="onDelete"
        >
            <span class="material-icons"> delete </span>
        </button>
        <input
            type="file"
            :id="elId"
            class="d-none"
            @input="picture = $event.target.files[0]"
        />
    </div>
    <div v-if="error" class="row mt-1">
        <div class="col-12 text-danger font-error">
            {{ error }}
        </div>
    </div>
</template>

<style scoped>
.upload-box {
    height: 200px;
    border: 1px dashed #ccc;
    cursor: pointer;
}

.upload-box .material-icons {
    font-size: 6rem;
}
</style>
