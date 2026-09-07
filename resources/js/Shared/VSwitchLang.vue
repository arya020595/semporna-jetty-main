<script setup>
import { useLangStore } from "@/Store/language";
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

const langStore = useLangStore();
langStore.getLang();

const lang = ref(langStore.lang);

const listLang = [
    {
        id: "en",
        description: "English",
    },
    {
        id: "ms",
        description: "Bahasa Malaysia",
    },
    {
        id: "zh",
        description: "中文",
    },
    {
        id: "kr",
        description: "한국어",
    },
    {
        id: "jp",
        description: "日本語",
    },
];

const changeLang = (e) => {
    lang.value = e.target.value;
    langStore.setLang(lang.value);
    location.reload();
};
</script>
<template>
    <select name="lang" id="lang" class="" v-model="lang" @change="changeLang">
        <option v-for="lang in listLang" :key="lang.id" :value="lang.id">
            {{ lang.description }}
        </option>
    </select>
</template>
<style scoped>
#lang {
    border: 1px solid #ccc;
    padding: 5px 8px;
}
</style>
