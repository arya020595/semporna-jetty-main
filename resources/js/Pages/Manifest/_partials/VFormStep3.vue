<script setup>
import intus from "intus";
import { isRequired } from "intus/rules";

import VDevider from "@/Shared/VDevider.vue";
import { useForm } from "@inertiajs/vue3";
import VButtonSubmit from "@/Shared/Buttons/VButtonSubmit";
import VSelectWithBlockLabel from "../../../Shared/Form/Survey/VSelectWithBlockLabel.vue";
import VSelectMultipleWithBlockLabel from "../../../Shared/Form/Survey/VSelectMultipleWithBlockLabel.vue";
import VFormDestinationActivity from "../../../Shared/Manifest/VFormDestinationActivity.vue";
import { watch } from "vue";

const props = defineProps({
    additional: Object,
});

const { arrDestination, arrDeparture, arrActivity } = props.additional;

const form = useForm({
    departure_id: props.additional.manifest?.departure_id ?? "",
    destination: props.additional.manifest?.destination ?? [],
    destination_activity: props.additional.manifest?.destination_activity ?? [],
});

const emits = defineEmits(["onNext", "onPrev"]);

const handleClickPrev = () => {
    emits("onPrev");
};

const handleClickNext = () => {
    const validation = intus.validate(form.data(), {
        departure_id: [isRequired()],
        destination: [isRequired()],
    });

    form.clearErrors();
    if (validation.passes()) {
        emits("onNext", form.data());
    } else {
        form.setError(validation.errors());
    }
};

watch(
    () => form.destination,
    (newValue) => {
        for (let value of newValue) {
            const isExists = form.destination_activity?.find((item) => {
                return item.destination_id == value;
            });

            if (isExists) {
                continue;
            }

            form.destination_activity.push({
                destination_id: value,
                activity: [],
            });
        }

        const formData = form.data();

        form.destination_activity = formData.destination_activity.filter(
            (item) => {
                return formData.destination.includes(item.destination_id);
            }
        );
    }
);

//
</script>

<template>
    <div class="d-flex justify-content-between">
        <h5 class="d-flex align-items-center mb-0">Destination</h5>
    </div>
    <VDevider class="mb-4" />

    <div class="row justify-content-center">
        <div class="col-md-4 mb-3">
            <VSelectWithBlockLabel
                elId="departure_id"
                label="Departure Jetty"
                :options="
                    arrDeparture?.map((item) => ({
                        id: item.id,
                        description: item.title,
                    })) ?? []
                "
                v-model:value="form.departure_id"
                :error="form.errors.departure_id"
            />
        </div>
        <div
            class="col-md-1 d-md-flex justify-content-center align-items-center d-none"
        >
            <img
                src="/assets/images/arrow-destination.png"
                alt="destination-arrow"
                style="width: 100%"
            />
        </div>
        <div class="col-md-4 mb-3">
            <VSelectMultipleWithBlockLabel
                elId="departure_id"
                label="To"
                :options="
                    arrDestination?.map((item) => ({
                        id: item.id,
                        description: item.title,
                    })) ?? []
                "
                v-model:value="form.destination"
                :error="form.errors.destination"
            />
        </div>
    </div>

    <!-- <div class="d-flex justify-content-between mt-5">
        <h5 class="d-flex align-items-center mb-0">Destination Activity</h5>
    </div>
    <VDevider class="mb-4" />
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <VFormDestinationActivity
                v-for="(destActivity, index) in form.destination_activity"
                :key="'destination_activity_' + index"
                :destination="
                    arrDestination.find(
                        (item) => item.id == destActivity.destination_id
                    )
                "
                v-model:value="form.destination_activity[index]"
                :arrActivity="arrActivity"
            />
        </div>
    </div> -->
    <VDevider class="my-3" />

    <div class="d-flex justify-content-between">
        <VButtonSubmit
            type="button"
            :isProcessing="form.processing"
            @onCLickSubmit="handleClickPrev"
            attrClass="px-4"
        >
            Go Back
        </VButtonSubmit>

        <VButtonSubmit
            type="button"
            :isProcessing="form.processing"
            @onCLickSubmit="handleClickNext"
            attrClass="px-4"
        >
            Next
        </VButtonSubmit>
    </div>
</template>
