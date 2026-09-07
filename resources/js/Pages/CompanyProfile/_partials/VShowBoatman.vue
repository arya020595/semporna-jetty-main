<script setup>
import VInputReadonlyWithLabel from "../../../Shared/Form/VInputReadonlyWithLabel.vue";
import VDownloadButton from "../../../Shared/Form/VDownloadButton.vue";

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
                :elId="'name' + index"
                label="Boatman name"
                type="text"
                :value="value.name"
                :isPlainText="false"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
            />
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 mb-3">
            <VInputReadonlyWithLabel
                :elId="'mate_card' + index"
                label="Boatman Mate Card No."
                type="text"
                :value="value.mate_card"
                :isPlainText="false"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
            />
        </div>
        <div class="col-lg-4 mb-3">
            <div class="row">
                <div class="offset-sm-4 offset-0 offset-lg-0">
                    <VDownloadButton :value="value.mate_card_file_existing"
                        >Upload Mate Card</VDownloadButton
                    >
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 mb-3">
            <VInputReadonlyWithLabel
                :elId="'ic_no' + index"
                label="Boatman IC"
                type="text"
                :value="value.ic_no"
                :isPlainText="false"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
            />
        </div>
        <div class="col-lg-4 mb-3">
            <div class="row">
                <div class="offset-sm-4 offset-0 offset-lg-0">
                    <VDownloadButton :value="value.ic_no_file_existing"
                        >Upload Mate Card</VDownloadButton
                    >
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 mb-3">
            <VInputReadonlyWithLabel
                :elId="'seaman_card_no' + index"
                label="Seaman Card No."
                type="text"
                :value="value.seaman_card_no"
                :isPlainText="false"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
            />
        </div>
        <div class="col-lg-4 mb-3">
            <div class="row">
                <div class="offset-sm-4 offset-0 offset-lg-0">
                    <VDownloadButton :value="value.seaman_card_file_existing"
                        >Upload Mate Card</VDownloadButton
                    >
                </div>
            </div>
        </div>
    </div>
</template>
