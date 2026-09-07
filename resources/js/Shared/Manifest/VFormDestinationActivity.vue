<script setup>
import debounce from "lodash/debounce";
import { useForm } from "@inertiajs/vue3";
import VSelectMultipleWithBlockLabel from "../Form/Survey/VSelectMultipleWithBlockLabel.vue";
import { watch } from "vue";
import VSelectMultipleWithLabel from "../Form/VSelectMultipleWithLabel.vue";

const props = defineProps({
    destination: Object,
    value: Object,
    index: Number,
    arrActivity: Array,
});

const form = useForm(
    props.value ?? {
        destination_id: props.destination.id,
        activity: [],
    }
);

const emits = defineEmits(["update:value"]);

watch(
    () => form.activity,
    () => {
        const formdata = form.data();
        emits("update:value", {
            destination_id: formdata.destination_id,
            activity: [...formdata.activity],
        });
    }
);
</script>

<template>
    <div class="row mb-3">
        <div class="col-lg-12">
            <VSelectMultipleWithLabel
                :elId="'destination_activity' + index"
                :label="destination?.title"
                :options="
                    arrActivity?.map((item) => ({
                        id: item.id,
                        description: item.title,
                    })) ?? []
                "
                v-model:value="form.activity"
                :error="form.errors.activity"
            />
        </div>
    </div>
</template>
