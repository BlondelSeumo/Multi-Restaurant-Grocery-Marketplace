import { toast } from 'react-toastify';

export default function AuthInterceptor(axiosObject) {
  axiosObject.interceptors.request.use((options) => {
    return options;
  });
  axiosObject.interceptors.response.use(
    (response) => {
      return response;
    },
    (err) => {
      if (!err?.response) {
        toast.warn('No Server Response');
      } else if (err.response?.status === 400) {
        toast.warn('Missing Email or Password');
      } else if (err.response?.status === 413) {
        toast.warn('Content Too Large');
      } else if (err.response?.status === 401) {
        toast.warn('Unauthorized');
      } else if (err.response?.status === 404) {
        toast.warn('Page Not Found');
      } else if (err.response?.status === 402) {
        toast.warn('you are registered');
      } else {
        toast.warn('login Failed');
      }
      return err;
    }
  );

  return axiosObject;
}
