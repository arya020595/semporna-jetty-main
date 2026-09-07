<script setup>
import { useForm } from "@inertiajs/vue3";
import VButtonIconDelete from "../../../Shared/Buttons/VButtonIconDelete.vue";
import { watch, onMounted } from "vue";

import debounce from "lodash/debounce";
import { formatAlphaNumericOnly } from "../../../Helpers/number";

const props = defineProps({
    title: String,
    field: String,
    type: String,
    value: Object,
    isShowDelete: Boolean,
    index: Number,
});

const form = useForm({
    id: props.value?.id,
    name: props.value?.name,
    ic_no: props.value?.ic_no,
});

const emits = defineEmits(["update:value", "onDelete"]);

onMounted(() => {
    emits("update:value", form.data());
});

watch(
    () => props.value,
    (newValue) => {
        form.id = newValue?.id;
        form.name = newValue?.name;
        form.ic_no = newValue?.ic_no;
    },
);

watch(form, (newValue) => {
    update(newValue.data());
});

watch(
    () => form.ic_no,
    async (newValue) => {
        form.ic_no = await formatAlphaNumericOnly(newValue);
    },
);

const update = debounce((data) => {
    emits("update:value", data);
}, 100);
</script>

<template>
    <tr>
        <td class="text-nowrap" v-if="isShowDelete">
            <VButtonIconDelete
                classStyle="text-danger mt-1"
                @onClick="emits('onDelete')"
            />
        </td>
        <td v-else></td>

        <td class="text-center align-middle" style="width: 50px">
            {{ index + 1 }}
        </td>
        <td>
            <input
                :id="type + '_name_' + index"
                type="text"
                class="form-control"
                v-model="form.name"
                :class="{ 'is-invalid': form.errors.name }"
            />
            <div class="invalid-feedback" v-if="form.errors.name">
                {{ form.errors.name }}
            </div>
        </td>
        <td>
            <input
                :id="type + '_ic_no_' + index"
                type="text"
                class="form-control"
                v-model="form.ic_no"
                :class="{ 'is-invalid': form.errors.ic_no }"
            />
            <div class="invalid-feedback" v-if="form.errors.ic_no">
                {{ form.errors.ic_no }}
            </div>
        </td>
    </tr>
</template>
