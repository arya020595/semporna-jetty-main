import { defineStore } from "pinia";
import { ref } from "vue";

export const useFormDataStore = defineStore("fromdata", () => {
    const formData = ref({});
    getForm();

    function reset() {
        localStorage.setItem("formdata", JSON.stringify({}));
        formData.value = {};
    }

    function getForm() {
        const localData = localStorage.getItem("formdata");
        const data = JSON.parse(localData) ?? {};
        formData.value = data;

        return data;
    }

    function setForm(data) {
        localStorage.setItem("formdata", JSON.stringify(data));
        formData.value = data;
    }

    return { formData, reset, getForm, setForm };
});
