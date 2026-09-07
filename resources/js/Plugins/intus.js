import intus from "intus";
import i18n from "./i18n";
import { useLangStore } from "@/Store/language";
import { createPinia, setActivePinia } from 'pinia';

// Ensure Pinia is active
const pinia = createPinia();
setActivePinia(pinia);

const langStore = useLangStore();
langStore.getLang();

i18n.global.locale = langStore.lang;

intus.messages = {
  "isAccepted": i18n.global.t("plugins.intus.messages.isAccepted"),
  "isAcceptedIf": i18n.global.t("plugins.intus.messages.isAccepted"),
  "isAfter": i18n.global.t("plugins.intus.messages.isAfter"),
  "isAfterOrEqual": i18n.global.t("plugins.intus.messages.isAfterOrEqual"),
  "isArray": i18n.global.t("plugins.intus.messages.isArray"),
  "isBefore": i18n.global.t("plugins.intus.messages.isBefore"),
  "isBeforeOrEqual": i18n.global.t("plugins.intus.messages.isBeforeOrEqual"),
  "isBetween": i18n.global.t("plugins.intus.messages.isBetween"),
  "isBoolean": i18n.global.t("plugins.intus.messages.isBoolean"),
  "isDate": i18n.global.t("plugins.intus.messages.isDate"),
  "isDistinct": i18n.global.t("plugins.intus.messages.isDistinct"),
  "isEmail": i18n.global.t("plugins.intus.messages.isEmail"),
  "isExtension": i18n.global.t("plugins.intus.messages.isExtension"),
  "isGt": i18n.global.t("plugins.intus.messages.isGt"),
  "isGte": i18n.global.t("plugins.intus.messages.isGte"),
  "isImage": i18n.global.t("plugins.intus.messages.isImage"),
  "isIn": i18n.global.t("plugins.intus.messages.isIn"),
  "isInteger": i18n.global.t("plugins.intus.messages.isInteger"),
  "isIp": i18n.global.t("plugins.intus.messages.isIp"),
  "isJSON": i18n.global.t("plugins.intus.messages.isJSON"),
  "isLt": i18n.global.t("plugins.intus.messages.isLt"),
  "isLte": i18n.global.t("plugins.intus.messages.isLte"),
  "isMax": i18n.global.t("plugins.intus.messages.isMax"),
  "isMax.string": i18n.global.t("plugins.intus.messages.isMax"),
  "isMax.array": i18n.global.t("plugins.intus.messages.isMax"),
  "isMax.file": i18n.global.t("plugins.intus.messages.isMax"),
  "isMime": i18n.global.t("plugins.intus.messages.isMime"),
  "isMin": i18n.global.t("plugins.intus.messages.isMin"),
  "isMin.string": i18n.global.t("plugins.intus.messages.isMin"),
  "isMin.array": i18n.global.t("plugins.intus.messages.isMin"),
  "isMin.file": i18n.global.t("plugins.intus.messages.isMin"),
  "isNotIn": i18n.global.t("plugins.intus.messages.isNotIn"),
  "isNotRegex": i18n.global.t("plugins.intus.messages.isNotRegex"),
  "isNumeric": i18n.global.t("plugins.intus.messages.isNumeric"),
  "isRegex": i18n.global.t("plugins.intus.messages.isRegex"),
  "isRequired": i18n.global.t("plugins.intus.messages.isRequired"),
  "isRequiredIf": i18n.global.t("plugins.intus.messages.isRequiredIf"),
  "isRequiredIfAccepted": i18n.global.t("plugins.intus.messages.isRequiredIfAccepted"),
  "isRequiredUnless": i18n.global.t("plugins.intus.messages.isRequiredUnless"),
  "isRequiredWith": i18n.global.t("plugins.intus.messages.isRequiredWith"),
  "isRequiredWithAll": i18n.global.t("plugins.intus.messages.isRequiredWithAll"),
  "isRequiredWithout": i18n.global.t("plugins.intus.messages.isRequiredWithout"),
  "isRequiredWithoutAll": i18n.global.t("plugins.intus.messages.isRequiredWithoutAll"),
  "isSame": i18n.global.t("plugins.intus.messages.isSame"),
  "isUrl": i18n.global.t("plugins.intus.messages.isUrl"),
}

export default intus;
