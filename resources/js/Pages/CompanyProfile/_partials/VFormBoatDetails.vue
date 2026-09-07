<script setup>
import VInputWithLabel from "@/Shared/Form/VInputWithLabel.vue";
import { useForm } from "@inertiajs/vue3";
import VUploadButton from "../../../Shared/Form/VUploadButton.vue";
import VDevider from "@/Shared/VDevider.vue";
import VFormBoatman from "./VFormBoatman.vue";
import VFormBoatmanAssistance from "./VFormBoatmanAssistance.vue";
import { watch } from "vue";
import debounce from "lodash/debounce";
import VButton from "../../../Shared/Buttons/VButton.vue";

const props = defineProps({
    boats: Array,
    value: Object,
    urlSubmit: String,
});

const emits = defineEmits(["update:value", "onAddBoat"]);

const form = useForm({
    id: props.value.id,
    number: props.value.number,
    license: props.value.license,
    license_file_existing: props.value.license_file_existing,
    license_file: null,
    boatman:
        props.value.boatman?.length > 0
            ? props.value.boatman
            : [
                  {
                      boat_id: props.value.id,
                      name: "",
                      ic_no: "",
                      mate_card: "",
                      seaman_card_no: "",

                      ic_no_file: null,
                      mate_card_file: null,
                      seaman_card_no_file: null,

                      ic_no_file_existing: null,
                      mate_card_file_existing: null,
                      seaman_card_file_existing: null,
                  },
              ],
    asst:
        props.value.asst?.length > 0
            ? props.value.asst
            : [
                  {
                      boat_id: props.value.id,
                      name: "",
                      ic_no: "",
                      mate_card: "",
                      seaman_card_no: "",
                  },
              ],
});

const labelWidth = 4;

watch(
    form,
    (newValue) => {
        update(newValue.data());
    },
    { deep: true }
);

watch(
    () => props.value,
    (newValue) => {
        form.id = props.value.id;
        form.number = props.value.number;
        form.license = props.value.license;
        form.license_file = null;
        form.license_file_existing = props.value.license_file_existing;

        form.boatman =
            props.value.boatman?.length > 0
                ? props.value.boatman
                : [
                      {
                          boat_id: props.value.id,
                          name: "",
                          ic_no: "",
                          mate_card: "",
                          seaman_card_no: "",

                          ic_no_file: null,
                          mate_card_file: null,
                          seaman_card_no_file: null,

                          ic_no_file_existing: null,
                          mate_card_file_existing: null,
                          seaman_card_file_existing: null,
                      },
                  ];

        form.asst =
            props.value.asst?.length > 0
                ? props.value.asst
                : [
                      {
                          boat_id: props.value.id,
                          name: "",
                          ic_no: "",
                          mate_card: "",
                          seaman_card_no: "",
                      },
                  ];
    }
);

const resetForm = () => {
    form.id = null;
    form.number = "";
    form.license = "";
    form.license_file = null;
    form.license_file_existing = "";

    form.boatman = [
        {
            boat_id: props.value.id,
            name: "",
            ic_no: "",
            mate_card: "",
            seaman_card_no: "",

            ic_no_file: null,
            mate_card_file: null,
            seaman_card_no_file: null,

            ic_no_file_existing: null,
            mate_card_file_existing: null,
            seaman_card_file_existing: null,
        },
    ];

    form.asst = [
        {
            boat_id: props.value.id,
            name: "",
            ic_no: "",
            mate_card: "",
            seaman_card_no: "",
        },
    ];
};

const update = debounce((data) => {
    emits("update:value", data);
}, 500);

const addBoatman = () => {
    form.boatman.push({
        boat_id: props.value.id,
        name: "",
        ic_no: "",
        mate_card: "",
        seaman_card_no: "",

        ic_no_file: null,
        mate_card_file: null,
        seaman_card_file: null,
    });
};

const deleteBoatman = (index) => {
    const formData = form.data();
    form.boatman = [];

    form.boatman = formData.boatman.filter((item, i) => {
        return i != index;
    });
};

const addAsst = () => {
    form.asst.push({
        boat_id: props.value.id,
        name: "",
        ic_no: "",
        mate_card: "",
    });
};

const deleteAsst = (index) => {
    const formData = form.data();
    form.asst = [];

    form.asst = formData.asst.filter((item, i) => {
        return i != index;
    });
};

const submitAndAddNewBoat = () => {
    form.post(props.urlSubmit, {
        preserveScroll: true,
        onSuccess: () => {
            resetForm();
            emits("onAddBoat");
        },
    });
};
</script>

<template>
    <div class="d-flex justify-content-between">
        <h5 class="d-flex align-items-center mb-0">Boat Details</h5>
        <VButton btnStyle="btn-success" @onClick="submitAndAddNewBoat">
            <span class="material-icons me-1">add_circle_outline</span>
            Add New Boat Details</VButton
        >
    </div>
    <VDevider />

    <div class="row mt-4">
        <div class="col-lg-8 mb-3">
            <VInputWithLabel
                elId="number"
                label="Boat No."
                type="text"
                v-model:value="form.number"
                :error="form.errors.number"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
            />
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 mb-3">
            <VInputWithLabel
                elId="license"
                label="Boat License"
                type="text"
                v-model:value="form.license"
                :error="form.errors.license"
                :widthLabel="labelWidth"
                :widthInput="12 - labelWidth"
            />
        </div>
        <div class="col-lg-4 mb-3">
            <div class="row">
                <div class="offset-sm-4 offset-0 offset-lg-0">
                    <VUploadButton
                        elId="license_file"
                        :existing="value.license_file_existing"
                        v-model:value="form.license_file"
                        >Upload Boat License</VUploadButton
                    >
                </div>
            </div>
        </div>
    </div>

    <VDevider />

    <div class="my-4">
        <VFormBoatman
            v-for="(boatman, index) in form.boatman"
            :key="'boatman-' + index"
            v-model:value="form.boatman[index]"
            :index="index"
            @onDelete="deleteBoatman(index)"
            :isShowDelete="index < form.boatman?.length - 1"
        />
        <div class="row">
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-sm-8 offset-sm-4">
                        <VButton btnStyle="btn-success" @onClick="addBoatman">
                            <span class="material-icons me-1"
                                >add_circle_outline</span
                            >
                            Additional Boatman</VButton
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>

    <VDevider />

    <div class="my-4">
        <VFormBoatmanAssistance
            v-for="(boatman, index) in form.asst"
            :key="'asst-' + index"
            v-model:value="form.asst[index]"
            :index="index"
            @onDelete="deleteAsst(index)"
            :isShowDelete="index < form.asst?.length - 1"
        />

        <div class="row">
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-sm-8 offset-sm-4">
                        <VButton btnStyle="btn-success" @onClick="addAsst">
                            <span class="material-icons me-1"
                                >add_circle_outline</span
                            >
                            Additional Asst. Boatman</VButton
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
