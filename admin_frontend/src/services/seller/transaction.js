import request from '../request';

const transactionService = {
  getAll: (params) =>
    request.get('dashboard/seller/transactions/paginate', { params }),
  getById: (id, params) =>
    request.get(`dashboard/seller/transactions/${id}`, { params }),
  create: (id, data) => request.post(`payments/order/${id}/transactions`, data),
  updateStatus: (id, data) =>
    request.post(`payments/order/${id}/transactions`, data),
};

export default transactionService;
