import request from '../request';

const sellerCategory = {
  getAll: (params) => request.get('dashboard/seller/categories', { params }),
  getById: (uuid, params) =>
    request.get(`dashboard/seller/categories/${uuid}`, { params }),
  delete: (params) =>
    request.delete(`dashboard/seller/categories/delete`, { params }),
  create: (params) =>
    request.post('dashboard/seller/categories', {}, { params }),
  update: (uuid, params) =>
    request.put(`dashboard/seller/categories/${uuid}`, {}, { params }),
  search: (params) =>
    request.get('dashboard/seller/categories/search', { params }),
  select: (params) =>
    request.get('dashboard/seller/categories/select-paginate', { params }),
};

export default sellerCategory;
