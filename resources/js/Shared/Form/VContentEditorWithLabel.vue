<script setup>
import { ref, watch } from "vue";
import VContentEditor from "@/Shared/Form/VContentEditor.vue";

const props = defineProps({
    elId: {
        Type: String,
        default: "",
    },
    label: String,
    value: String,
    type: {
        type: String,
        default: "text",
    },
    error: String,
});

const editorData = ref(props.value);

const emits = defineEmits(["update:value"]);

watch(editorData, (newValue) => {
    emits("update:value", newValue);
});
</script>

<template>
    <div class="row">
        <label :for="elId" class="col-12 label-size fw-bold mb-2">
            {{ label }}
        </label>
        <div class="col-12">
            <VContentEditor
                elId="objectives_content_editor"
                v-model:value="editorData"
            />
        </div>
    </div>
    <div v-if="error" class="row">
        <div class="col-sm-9 offset-sm-3 text-danger font-error">
            {{ error }}
        </div>
    </div>
</template>
