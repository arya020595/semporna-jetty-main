<script setup>
import VDevider from "@/Shared/VDevider.vue";
import VInputWithLabel from "../Form/VInputWithLabel.vue";
import { useForm } from "@inertiajs/vue3";
import VSelectDefaultWithLabel from "../Form/VSelectDefaultWithLabel.vue";
import VButton from "../Buttons/VButton.vue";
import { watch } from "vue";

import debounce from "lodash/debounce";

const props = defineProps({
    index: Number,
    value: Object,
    isShowDelete: Boolean,
    arrNationality: Array,
    arrActivity: Array,
});

const form = useForm({
    id: props.value?.id ?? null,
    name: props.value?.name ?? "",
    ic_no: props.value?.ic_no ?? "",
    nationality_id: props.value?.nationality_id ?? "",
    age: props.value?.age ?? "",
    gender: props.value?.gender ?? "",
    next_of_kin: props.value?.next_of_kin ?? "",
    emergency_contact: props.value?.emergency_contact ?? "",
    activity_id: props.value?.activity_id ?? "",
});

const emits = defineEmits(["update:value", "onDelete"]);

watch(
    () => props.value,
    (newValue) => {
        form.name = props.value?.name ?? "";
        form.ic_no = props.value?.ic_no ?? "";
        form.nationality_id = props.value?.nationality_id ?? "";
        form.age = props.value?.age ?? "";
        form.gender = props.value?.gender ?? "";
        form.next_of_kin = props.value?.next_of_kin ?? "";
        form.emergency_contact = props.value?.emergency_contact ?? "";
        form.activity_id = props.value?.activity_id ?? "";
    }
);

watch(
    form,
    (newValue) => {
        update(form.data());
    },
    { deep: true }
);

const update = debounce((data) => {
    emits("update:value", data);
}, 500);

const ARR_GENDER = [
    {
        id: "M",
        description: "Male",
    },
    {
        id: "F",
        description: "Female",
    },
];
</script>

<template>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <VInputWithLabel
                    :elId="index + '_name'"
                    label="Guest Name"
                    type="text"
                    v-model:value="form.name"
                    :error="form.errors.name"
                />
            </div>
            <div class="mb-3">
                <VInputWithLabel
                    :elId="index + '_ic_no'"
                    label="IC/Passport No."
                    type="text"
                    v-model:value="form.ic_no"
                    :error="form.errors.ic_no"
                />
            </div>
            <div class="mb-3">
                <VSelectDefaultWithLabel
                    :elId="index + '_nationality_id'"
                    label="Nationality"
                    :options="
                        arrNationality.map((item) => ({
                            id: item.id,
                            description: item.title,
                        }))
                    "
                    v-model:value="form.nationality_id"
                    :error="form.errors.nationality_id"
                />
            </div>
            <div class="mb-3">
                <VInputWithLabel
                    :elId="index + '_age'"
                    label="Age"
                    type="number"
                    v-model:value="form.age"
                    :error="form.errors.age"
                />
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <VSelectDefaultWithLabel
                    :elId="index + '_gender'"
                    label="Gender"
                    :options="ARR_GENDER"
                    v-model:value="form.gender"
                    :error="form.errors.gender"
                />
            </div>
            <div class="mb-3">
                <VInputWithLabel
                    :elId="index + '_next_of_kin'"
                    label="Next of Kin"
                    type="text"
                    v-model:value="form.next_of_kin"
                    :error="form.errors.next_of_kin"
                />
            </div>
            <div class="mb-3">
                <VInputWithLabel
                    :elId="index + '_emergency_contact'"
                    label="Emergency Contact"
                    type="text"
                    v-model:value="form.emergency_contact"
                    :error="form.errors.emergency_contact"
                />
            </div>
            <div class="mb-3">
                <VSelectDefaultWithLabel
                    :elId="index + '_activity_id'"
                    label="Activity"
                    :options="
                        arrActivity.map((item) => ({
                            id: item.id,
                            description: item.title,
                        }))
                    "
                    v-model:value="form.activity_id"
                    :error="form.errors.activity_id"
                />
            </div>
        </div>
    </div>
    <div class="row mb-3" v-if="isShowDelete">
        <div class="col-12 text-end">
            <VButton btnStyle="btn-danger" @onClick="emits('onDelete')"
                ><span class="material-icons me-1">cancel</span> Cancel
            </VButton>
        </div>
    </div>
    <VDevider class="my-3" />
</template>
