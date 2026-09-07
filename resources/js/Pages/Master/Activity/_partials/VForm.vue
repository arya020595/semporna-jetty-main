<script setup>
import VButtonSubmit from "@/Shared/Buttons/VButtonSubmit.vue";
import VInputWithLabel from "@/Shared/Form/VInputWithLabel.vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    value: Object,
    urlSubmit: String,
    isCreate: Boolean,
});

const form = useForm({
    code: props.value?.code,
    title: props.value?.title,
    _method: props.isCreate ? "POST" : "PUT",
});

const submit = () => {
    form.post(props.urlSubmit, {
        preserveScroll: true,
    });
};
</script>

<template>
    <form @submit.prevent="submit">
        <div class="row">
            <div class="col-lg-6 mb-3">
                <VInputWithLabel
                    elId="code"
                    label="Code"
                    type="text"
                    :error="form.errors.code"
                    v-model:value="form.code"
                    :additionalAttr="{ readonly: isCreate }"
                />
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 mb-3">
                <VInputWithLabel
                    elId="title"
                    label="Activity"
                    type="text"
                    :error="form.errors.title"
                    v-model:value="form.title"
                />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-3">
                <div class="text-end">
                    <VButtonSubmit
                        type="submit"
                        :isProcessing="form.processing"
                    >
                        Submit
                    </VButtonSubmit>
                </div>
            </div>
        </div>
    </form>
</template>
