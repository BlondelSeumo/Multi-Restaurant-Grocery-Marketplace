import { toast } from 'react-toastify';

export default function ApiInterceptor(axiosObject) {
  axiosObject.interceptors.request.use(
    (options) => {
      const token = localStorage.getItem('token');
      options.headers['Authorization'] = 'Bearer ' + token;
      return options;
    },
    (err) => {
      return Promise.reject(err);
    }
  );
  axiosObject.interceptors.response.use(
    (response) => {
      return response;
    },
    (err) => {
      if (!err?.response) {
        toast.warn('No Server Response');
      } else if (err.response?.status === 400) {
        toast.warn('ERROR BAD REQUEST');
      } else if (err.response?.status === 413) {
        toast.warn('Content Too Large');
      } else if (err.response?.status === 500) {
        toast.warn('Missing ID or Undefined');
      } else if (err.response?.status === 401) {
        toast.warn('Unauthorized');
        localStorage.clear();
        document.location.reload(true);
      } else if (err.response?.status === 404) {
        toast.warn('Page Not Found');
      } else if (err.response?.status === 402) {
        toast.warn('you are registered');
      } else if (err.response?.status === 403) {
        toast.warn('does not data');
        // localStorage.clear()
        // document.location.reload(true)
      } else {
        toast.warn('login Failed');
      }
      return err;
    }
  );

  return axiosObject;
}
