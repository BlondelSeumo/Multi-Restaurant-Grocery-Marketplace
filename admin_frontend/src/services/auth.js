import request from './request';

const authService = {
  login: (data) => request.post('auth/login', data),
};

export default authService;
