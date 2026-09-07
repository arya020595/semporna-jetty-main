<script setup>
import VDevider from "@/Shared/VDevider.vue";
import { computed, reactive } from "vue";
import VShowFeeDetail from "../../../Shared/Manifest/VShowFeeDetail.vue";

const props = defineProps({
    manifest: Object,
});

const { manifest_fee } = props.manifest;

const form = reactive({
    local_adult: manifest_fee.local_adult,
    local_child: manifest_fee.local_child,

    foreign_adult: manifest_fee.foreign_adult,
    foreign_child: manifest_fee.foreign_child,

    ticket_local_adult: manifest_fee.ticket_local_adult,
    ticket_local_child: manifest_fee.ticket_local_child,

    ticket_foreign_adult: manifest_fee.ticket_foreign_adult,
    ticket_foreign_child: manifest_fee.ticket_foreign_child,

    boat_fee: parseFloat(manifest_fee.boat_fee),

    local_adult_fee: parseFloat(manifest_fee.local_adult_fee),
    local_child_fee: parseFloat(manifest_fee.local_child_fee),

    foreign_adult_fee: parseFloat(manifest_fee.foreign_adult_fee),
    foreign_child_fee: parseFloat(manifest_fee.foreign_child_fee),
});

const subTotal = computed(() => {
    const totalLocal =
        form.local_adult * form.local_adult_fee +
        form.local_child * form.local_child_fee;

    const totalForeign =
        form.foreign_adult * form.foreign_adult_fee +
        form.foreign_child * form.foreign_child_fee;

    const price = totalLocal + totalForeign + form.boat_fee;

    return "RM " + parseFloat(price).toFixed(2);
});

const formatBoatFee = computed(() => {
    return "RM " + parseFloat(form.boat_fee).toFixed(2);
});
</script>

<template>
    <h6 class="bg-primary text-white px-4 py-3 mb-4">Overall Fee Details</h6>

    <div class="d-flex justify-content-between">
        <h5 class="d-flex align-items-center mb-2">Local Passengers</h5>
    </div>
    <VDevider class="mb-3" />
    <VShowFeeDetail
        :adult="form.local_adult"
        :child="form.local_child"
        :ticket_adult="form.ticket_local_adult"
        :ticket_child="form.ticket_local_child"
        :adult_fee="form.local_adult_fee"
        :child_fee="form.local_child_fee"
    />

    <div class="d-flex justify-content-between mt-4">
        <h5 class="d-flex align-items-center mb-2">Foreign Passengers</h5>
    </div>
    <VDevider class="mb-3" />
    <VShowFeeDetail
        :adult="form.foreign_adult"
        :child="form.foreign_child"
        :ticket_adult="form.ticket_foreign_adult"
        :ticket_child="form.ticket_foreign_child"
        :adult_fee="form.foreign_adult_fee"
        :child_fee="form.foreign_child_fee"
    />

    <div class="d-flex justify-content-between mt-4">
        <h5 class="d-flex align-items-center mb-2">Boat Fee</h5>
    </div>
    <VDevider class="mb-3" />
    <div class="row">
        <div class="col-6 mb-3 mt-3">
            <div class="row align-items-sm-center">
                <label
                    :class="'col-sm-4 label-size text-sm-end fw-bold mb-sm-0 mb-2'"
                >
                    Boat Fee
                </label>
                <div :class="'col-sm-8 custom-position-relative'">
                    <input
                        type="text"
                        class="form-control bg-primary text-white"
                        :value="formatBoatFee"
                        disabled
                    />
                </div>
            </div>
        </div>
    </div>
    <VDevider class="mb-3" />
    <div class="row">
        <div class="col-12 text-end">
            <div class="form-control-lg bg-primary text-white d-inline">
                Subtotal : <strong>RM {{ manifest_fee.total }}</strong>
            </div>
        </div>
    </div>
</template>
