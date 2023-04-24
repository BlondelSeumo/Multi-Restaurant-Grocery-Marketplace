import request from './request';

const smsService = {
  getAll: (params) => request.get('dashboard/admin/sms-gateways', { params }),
  getById: (id, params) =>
    request.get(`dashboard/admin/sms-gateways/${id}`, { params }),
  update: (id, data) => request.put(`dashboard/admin/sms-gateways/${id}`, data),
  create: (data) => request.post(`dashboard/admin/sms-gateways`, data),
  setActive: (id, data) =>
    request.post(`dashboard/admin/sms-gateways/${id}/active/status`, data),
};

export default smsService;
