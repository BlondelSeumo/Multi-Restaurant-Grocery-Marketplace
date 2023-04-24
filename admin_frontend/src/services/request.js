import axios from 'axios';
import { notification } from 'antd';
import { api_url } from '../configs/app-global';
import { store } from '../redux/store';
import { clearUser } from '../redux/slices/auth';
import i18n from '../configs/i18next';
import { toast } from 'react-toastify';

const service = axios.create({
  baseURL: api_url,
  timeout: 16000,
});

// Config
const TOKEN_PAYLOAD_KEY = 'authorization';
const AUTH_TOKEN = 'token';
const AUTH_TOKEN_TYPE = 'Bearer';

// API Request interceptor
service.interceptors.request.use(
  (config) => {
    const access_token = localStorage.getItem(AUTH_TOKEN);

    if (access_token) {
      config.headers[TOKEN_PAYLOAD_KEY] = AUTH_TOKEN_TYPE + ' ' + access_token;
    }
    if (config.method === 'get') {
      config.params = { lang: i18n.language, ...config.params };
    }

    return config;
  },
  (error) => {
    // Do something with request error here
    notification.error({
      message: 'Error',
    });
    Promise.reject(error);
  }
);

// API respone interceptor
service.interceptors.response.use(
  (response) => {
    return response.data;
  },
  (error) => {
    let notificationParam = {
      message: i18n.t(error.response.data.statusCode),
    };

    // Remove token and redirect
    if (error.response.status === 403 || error.response.status === 401) {
      localStorage.removeItem(AUTH_TOKEN);
      store.dispatch(clearUser());
    }

    if (error.response?.status === 508) {
      notificationParam.message = 'Loop Detected';
    }

    if (error.response?.status === 500) {
      notificationParam.message = 'Internal Server Error';
    }

    if (error.response?.status === 404) {
      notificationParam.message = 'Page not found';
    }

    if (error.response?.status === 400) {
      notificationParam.message = 'Bad Request';
      // error.response?.data?.message
    }

    toast.error(notificationParam.message, {
      toastId: error.response?.status,
    });
    return Promise.reject(error);
  }
);

export default service;
