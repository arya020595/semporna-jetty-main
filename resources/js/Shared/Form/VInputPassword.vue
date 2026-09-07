<script setup>
import { ref } from "vue";

const props = defineProps({
    elId: {
        Type: String,
        default: "",
    },
    error: String,
    value: String,
    placeholder: String,
    autocomplete: {
        Type: String,
        default: "",
    },
});

const showPassword = ref(false);

const tooglePassword = function () {
    showPassword.value = !showPassword.value;
};

defineEmits(["update:value"]);
</script>

<template>
    <div class="form-group d-flex align-items-center">
        <input
            :id="elId"
            :type="showPassword ? 'text' : 'password'"
            class="form-control"
            @input="$emit('update:value', $event.target.value)"
            :class="{ 'is-invalid': error }"
            :value="value"
            :placeholder="placeholder"
            :autocomplete="autocomplete"
        />
        <i
            class="fas fa-eye toogle-password"
            :class="{
                'fa-eye': showPassword,
                'fa-eye-slash': !showPassword,
            }"
            @click="tooglePassword"
        ></i>
    </div>
</template>
<style>
.toogle-password {
    margin-left: -30px;
    cursor: pointer;
    color: #929aac;
}
</style>
