<script setup>
import { computed, ref } from "vue";
import VFormStep1 from "./VFormStep1.vue";
import VFormStep2 from "./VFormStep2.vue";
import VFormStep3 from "./VFormStep3.vue";
import VFormStep4 from "./VFormStep4.vue";
import { TAB_ENUM } from "./tabs.config";
import VFormStep5 from "./VFormStep5.vue";

const props = defineProps({
    manifest: Object,
    boats: Array,
    company: Object,
    arrInstructor: Array,
    arrDivemaster: Array,
    arrGuide: Array,
    arrDestination: Array,
    arrDeparture: Array,
    arrNationality: Array,
    urlTemplate: String,
    urlTemplateStaff: String,
    arrActivity: Array,
    errors: Object,
    tab: Object,
});

const emits = defineEmits(["onNext", "onPrev"]);
const form = ref(props.manifest);
const tabDirection = ref(1);

const activeComponent = computed({
    get() {
        const additional = {
            manifest: form.value,
            errors: props.errors,
            tabDirection: tabDirection.value,
        };

        switch (props.tab.key) {
            case TAB_ENUM.FORM_1:
                return {
                    component: VFormStep1,
                    additional: {
                        ...additional,
                        boats: props.boats,
                        company: props.company,
                    },
                };
            case TAB_ENUM.FORM_2:
                return {
                    component: VFormStep2,
                    additional: {
                        ...additional,
                        arrInstructor: props.arrInstructor,
                        arrDivemaster: props.arrDivemaster,
                        arrGuide: props.arrGuide,
                        isFreeText: props.manifest.is_rent,
                    },
                };
            case TAB_ENUM.FORM_3:
                return {
                    component: VFormStep3,
                    additional: {
                        ...additional,
                        arrDestination: props.arrDestination,
                        arrDeparture: props.arrDeparture,
                        arrActivity: props.arrActivity,
                    },
                };
            case TAB_ENUM.FORM_4:
                return {
                    component: VFormStep4,
                    additional: {
                        ...additional,
                        arrNationality: props.arrNationality,
                        arrActivity: props.arrActivity,
                        urlTemplate: props.urlTemplate,
                        arrBoat: props.boats,
                        arrDeparture: props.arrDeparture,
                    },
                };
            case TAB_ENUM.FORM_5:
                return {
                    component: VFormStep5,
                    additional: {
                        ...additional,
                        arrNationality: props.arrNationality,
                        arrActivity: props.arrActivity,
                        urlTemplateStaff: props.urlTemplateStaff,
                        arrBoat: props.boats,
                    },
                };
        }
    },
});

const handelOnNext = (data) => {
    for (let props in data) {
        form.value[props] = data[props];
    }
    tabDirection.value = 1;
    emits("onNext", { ...form.value });
};

const handleOnPrev = (data) => {
    for (let props in data) {
        form.value[props] = data[props];
    }
    tabDirection.value = -1;
    emits("onPrev", { ...form.value });
};
</script>

<template>
    <KeepAlive>
        <component
            :is="activeComponent.component"
            :additional="activeComponent.additional"
            @onNext="handelOnNext"
            @onPrev="handleOnPrev"
        />
    </KeepAlive>
</template>
