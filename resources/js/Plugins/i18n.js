// filepath: /Users/dioratar/Documents/_project/omni/repo/datablu/tourism-survey/resources/js/i18n.js
import { createI18n } from 'vue-i18n';

// Import your language files
import en from '@/locales/en';
import ms from '@/locales/ms';
import zh from '@/locales/zh';
import kr from '@/locales/kr';
import jp from '@/locales/jp';

const messages = {
  en,
  ms,
  zh,
  kr,
  jp
};

const i18n = createI18n({
  locale: 'en', // set locale
  fallbackLocale: 'en', // set fallback locale
  messages, // set locale messages
});

export default i18n;
