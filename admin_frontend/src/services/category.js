import request from './request';

const categoryService = {
  getAll: (params) =>
    request.get('dashboard/admin/categories/paginate', { params }),
  getById: (id, params) =>
    request.get(`dashboard/admin/categories/${id}`, { params }),
  create: (params) =>
    request.post('dashboard/admin/categories', {}, { params }),
  update: (id, params) =>
    request.put(`dashboard/admin/categories/${id}`, {}, { params }),
  delete: (params) =>
    request.delete(`dashboard/admin/categories/delete`, { params }),
  search: (params) =>
    request.get('dashboard/admin/categories/search', { params }),
  setActive: (id) => request.post(`dashboard/admin/categories/active/${id}`),
  dropAll: () => request.get(`dashboard/admin/categories/drop/all`),
  restoreAll: () => request.get(`dashboard/admin/categories/restore/all`),
  export: (params) =>
    request.get('dashboard/admin/categories/export', { params }),
  import: (params) =>
    request.get('dashboard/admin/categories/import', { params }),
};

export default categoryService;
