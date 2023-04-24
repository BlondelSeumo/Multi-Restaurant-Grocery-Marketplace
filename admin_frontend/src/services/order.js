import request from './request';

const orderService = {
  getAll: (params) =>
    request.get('dashboard/admin/orders/paginate', { params }),
  getById: (id, params) =>
    request.get(`dashboard/admin/orders/${id}`, { params }),
  export: () => request.get(`dashboard/admin/order/export`),
  create: (data) => request.post('dashboard/admin/orders', data, {}),
  update: (id, data) => request.put(`dashboard/admin/orders/${id}`, data),
  calculate: (params) =>
    request.get(`dashboard/admin/order/products/calculate${params}`),
  updateStatus: (id, params) =>
    request.post(`dashboard/admin/order/${id}/status`, {}, { params }),
  updateDelivery: (id, params) =>
    request.post(`dashboard/admin/order/${id}/deliveryman`, {}, { params }),
  delete: (params) =>
    request.delete(`dashboard/admin/orders/delete`, { params }),
  dropAll: () => request.get(`dashboard/admin/orders/drop/all`),
  restoreAll: () => request.get(`dashboard/admin/orders/restore/all`),
  getAllUserOrder: (id, params) =>
    request.get(`dashboard/admin/user-orders/${id}/paginate`, { params }),
  getUserTopProducts: (id, params) => request.get(`dashboard/admin/user-orders/${id}`, {params})
};

export default orderService;
