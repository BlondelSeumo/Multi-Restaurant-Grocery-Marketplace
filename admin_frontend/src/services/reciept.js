import request from './request';

const recieptService = {
  getAll: (params) => request.get('dashboard/admin/receipts', { params }),
  getById: (id, params) =>
    request.get(`dashboard/admin/receipts/${id}`, { params }),
  delete: (params) =>
    request.delete('dashboard/admin/receipts/delete', { params }),
  create: (data) => request.post('dashboard/admin/receipts', data),
  update: (id, data) => request.put(`dashboard/admin/receipts/${id}`, data),
};

export default recieptService;
