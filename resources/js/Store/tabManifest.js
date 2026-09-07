import { defineStore } from "pinia";
import { ref } from "vue";

export const useTabManifestStore = defineStore("manifest_tabs", () => {
    const STORAGE_KEY_TABS = "manifest_tabs";
    const STORAGE_KEY_CURRENT_TAB = "manifest_current_tab";


    const tabs = ref(null);
    const current = ref(null);

    getTabs();
    getCurrent();

    function resetTabs() {
        localStorage.setItem(STORAGE_KEY_TABS, JSON.stringify({}));
        tabs.value = {};
    }

    function resetCurrent() {
        localStorage.setItem(STORAGE_KEY_CURRENT_TAB, JSON.stringify({}));
        current.value = {};
    }


    function resetAll() {
        resetTabs();
        resetCurrent();
    }

    function getTabs() {
        const localData = localStorage.getItem(STORAGE_KEY_TABS);
        const data = JSON.parse(localData) ?? {};
        tabs.value = data;
        return data;
    }

    function getCurrent() {
        const localData = localStorage.getItem(STORAGE_KEY_CURRENT_TAB);
        const data = JSON.parse(localData) ?? {};
        current.value = data;
        return data;
    }
    function setTabs(data) {
        localStorage.setItem(STORAGE_KEY_TABS, JSON.stringify(data));
        tabs.value = data;
    }

    function setCurrent(data) {
        localStorage.setItem(STORAGE_KEY_CURRENT_TAB, JSON.stringify(data));
        current.value = data;
    }
    return {
        tabs,
        current,
        resetAll,
        resetTabs,
        resetCurrent,
        setTabs,
        setCurrent,
    };
});
