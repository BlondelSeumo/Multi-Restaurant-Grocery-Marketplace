import i18n from "i18next";
import { initReactI18next } from "react-i18next";
import Backend from "i18next-http-backend";
import translationEN from "./locales/en/translation.json";
import { DEFAULT_LANGUAGE } from "constants/config";

const resources = {
  en: {
    translation: translationEN,
  },
};

i18n
  .use(initReactI18next)
  .use(Backend)
  .init({
    resources,
    fallbackLng: DEFAULT_LANGUAGE,
    // lng: getCookieFromBrowser("NEXT_LOCALE") || DEFAULT_LANGUAGE,
    supportedLngs: [
      "en",
      "es",
      "el",
      "fr",
      "de",
      "ru",
      "sa",
      "tr",
      "az",
      "af",
      "ar",
      "be",
      "bg",
      "cs",
      "fa",
      "hi",
      "id",
      "ja",
      "kz",
      "ko",
      "mg",
      "tg",
      "tk",
      "zh",
    ],
    ns: ["translation", "errors"],
    defaultNS: "translation",
  });

export default i18n;
