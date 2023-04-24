import request from '../request';

const paymentService = {
  getAll: (params) => request.get('dashboard/seller/shop-payments', { params }),
  allPayment: (params) =>
    request.get('dashboard/seller/shop-payments/shop-non-exist', { params }),
  getById: (id, params) =>
    request.get(`dashboard/seller/shop-payments/${id}`, { params }),
  create: (data) => request.post('dashboard/seller/shop-payments', data, {}),
  update: (id, data) =>
    request.put(`dashboard/seller/shop-payments/${id}`, data, {}),
  delete: (params) =>
    request.delete(`dashboard/seller/shop-payments/delete`, { params }),
};

export default paymentService;
