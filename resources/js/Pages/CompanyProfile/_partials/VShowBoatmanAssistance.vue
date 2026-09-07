<script setup>
import VInputWithLabel from "@/Shared/Form/VInputWithLabel.vue";
import { useForm } from "@inertiajs/vue3";
import VButton from "../../../Shared/Buttons/VButton.vue";
import { watch } from "vue";

import debounce from "lodash/debounce";
import VInputReadonlyWithLabel from "../../../Shared/Form/VInputReadonlyWithLabel.vue";

const props = defineProps({
    value: Object,
    index: Number,
});

const labelWidth = 4;
</script>

<template>
    <div class="row">
        <div class="col-lg-8 mb-3">
            <VInputReadonlyWithLabel
                elId="name"
                label="Assistant Boatman Name"
                type="text"
                :isPlainText="false"
                :value="value.name"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
            />
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 mb-3">
            <VInputReadonlyWithLabel
                elId="ic_no"
                label="Assistant Boatman IC No."
                type="text"
                :isPlainText="false"
                :value="value.ic_no"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
            />
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 mb-3">
            <VInputReadonlyWithLabel
                elId="mate_card"
                label="Assistant Boatman Mate No."
                type="text"
                :isPlainText="false"
                :value="value.mate_card"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
            />
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 mb-3">
            <VInputReadonlyWithLabel
                elId="seaman_card_no"
                label="Assistant Seaman Card No."
                type="text"
                :isPlainText="false"
                :value="value.seaman_card_no"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
            />
        </div>
    </div>
</template>
