<script setup>
import VDevider from "@/Shared/VDevider.vue";
import VInputReadonlyWithLabel from "../../../Shared/Form/VInputReadonlyWithLabel.vue";
import VShowBoatman from "./VShowBoatman.vue";
import VShowBoatmanAssistance from "./VShowBoatmanAssistance.vue";

const props = defineProps({
    boats: Array,
});
const labelWidth = 4;
</script>

<template>
    <div class="d-flex justify-content-between">
        <h5 class="d-flex align-items-center fw-bold mb-0">Boat Detailsss</h5>
    </div>
    <VDevider />

    <div class="mt-4">
        <template v-for="boat in boats">
            <div class="row">
                <div class="col-lg-8 mb-3">
                    <VInputReadonlyWithLabel
                        elId="capacity"
                        label="Capacity"
                        type="text"
                        :isPlainText="false"
                        :value="boat.capacity"
                        :widthLabel="labelWidth"
                        :widthInput="4"
                    />
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 mb-3">
                    <VInputReadonlyWithLabel
                        elId="license_expiry_date"
                        label="License Expiry Date"
                        type="text"
                        :isPlainText="false"
                        :value="boat.license_expiry_date"
                        :widthLabel="labelWidth"
                        :widthInput="4"
                    />
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 mb-3">
                    <VInputReadonlyWithLabel
                        elId="license"
                        label="Boat License"
                        type="text"
                        :isPlainText="false"
                        :value="boat.license"
                        :widthLabel="labelWidth"
                        :widthInput="12 - labelWidth"
                    />
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 mb-3">
                    <div class="row">
                        <div class="offset-sm-4 offset-0">
                            <a
                                v-for="fileUrl in boat.license_file_existing"
                                class="btn btn-success mx-1"
                                target="_blank"
                                :href="fileUrl"
                            >
                                <span class="material-icons">visibility</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <VDevider class="mb-3" />
        </template>
    </div>

    <div class="my-4">
        <div v-for="boat in boats">
            <template
                v-for="(boatman, index) in boat.boatman"
                :key="'boatman-' + index"
            >
                <VShowBoatman :value="boatman" :index="index" />
                <VDevider class="mb-3" />
            </template>
        </div>
    </div>

    <div class="my-4">
        <div v-for="boat in boats">
            <template
                v-for="(boatman, index) in boat.asst"
                :key="'boatman-' + index"
            >
                <VShowBoatmanAssistance :value="boatman" :index="index" />
                <VDevider class="mb-3" />
            </template>
        </div>
    </div>
</template>
