import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import Backend from 'i18next-http-backend';
import LanguageDetector from 'i18next-browser-languagedetector';
import translationEN from '../assets/locale/en/translation.json';
import { THEME_CONFIG } from './theme-config';

const resources = {
  en: {
    translation: translationEN,
  },
};

i18n
  .use(initReactI18next)
  .use(Backend)
  .use(LanguageDetector)
  .init({
    resources,
    fallbackLng: THEME_CONFIG.locale,
    lng: localStorage.getItem('i18nextLng') || THEME_CONFIG.locale,
    supportedLngs: [
      'en',
      'es',
      'fr',
      'de',
      'ru',
      'sa',
      'tr',
      'az',
      'aze',
      'af',
      'af',
      'ar',
      'be',
      'bg',
      'cs',
      'fa',
      'hi',
      'id',
      'ja',
      'kz',
      'ko',
      'mg',
      'tg',
      'tk',
      'zh',
    ],
    ns: ['translation', 'errors'],
    defaultNS: 'translation',
  });

export default i18n;
