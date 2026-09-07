import { ref } from 'vue'
import { usePage } from "@inertiajs/vue3"
import { defineStore } from 'pinia'
import i18n from '@/Plugins/i18n.js'
import VueCookies from 'vue-cookies'

export const useLangStore = defineStore('language', () => {
  const lang = ref('en')

  const cookiesDomain = usePage().props?.domain;

  function getLang() {
    const dataLang = VueCookies.get('lang')
    i18n.global.locale = dataLang ?? 'en'
    lang.value = dataLang ?? 'en'
  }

  function setLang(dataLang) {
    VueCookies.set('lang', dataLang, { domain: cookiesDomain })
    i18n.global.locale = dataLang
    lang.value = dataLang
  }

  return {
    lang,
    getLang,
    setLang,
  }
})
