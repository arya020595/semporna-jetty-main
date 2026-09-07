<script setup>
import VDevider from "@/Shared/VDevider.vue";
import VInputWithLabel from "../../../Shared/Form/VInputWithLabel.vue";

const props = defineProps({
    manifest: Object,
});

const form = {
    departure_name: props.manifest?.departure_name ?? "",
    destination: props.manifest?.manifest_destination?.map(
        (item) => item.ref_destination_name
    ),
};

const labelWidth = 4;
</script>

<template>
    <h6 class="bg-primary text-white px-4 py-3 mt-4 mb-3">Destination</h6>

    <div class="row">
        <div class="col-md-6 mb-3">
            <VInputWithLabel
                elId="departure_time"
                label="Departure Jetty"
                type="text"
                :value="form.departure_name"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
                :additionalAttr="{ disabled: true }"
            />
        </div>
        <div class="col-md-6 mb-3 destination-wrapper">
            <img
                src="/assets/images/arrow-destination.png"
                alt="destination-arrow"
                class="destination-arrow ms-md-2 ms-lg-2 ms-0 d-none d-md-block"
            />
            <VInputWithLabel
                elId="departure_time"
                label="To"
                type="text"
                :value="form.destination?.join(', ')"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
                :additionalAttr="{ disabled: true }"
                :additionalLabelAttr="{ class: 'pe-5 text-md-end text-start' }"
            />
        </div>
    </div>
</template>

<style lang="css" scoped>
.destination-wrapper {
    position: relative;
}

.destination-arrow {
    position: absolute;
    width: 40px;
}
</style>
