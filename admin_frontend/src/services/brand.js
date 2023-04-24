import request from './request';

const brandService = {
  get: (params) => request.get('dashboard/admin/brands', { params }),
  getAll: (params) =>
    request.get('dashboard/admin/brands/paginate', { params }),
  export: (params) => request.get('dashboard/admin/brands/export', { params }),
  import: (params) => request.get('dashboard/admin/brands/import', { params }),
  getById: (id, params) =>
    request.get(`dashboard/admin/brands/${id}`, { params }),
  create: (params) => request.post('dashboard/admin/brands', {}, { params }),
  update: (id, params) =>
    request.put(`dashboard/admin/brands/${id}`, {}, { params }),
  delete: (params) =>
    request.delete(`dashboard/admin/brands/delete`, { params }),
  search: (search = '') =>
    request.get(`dashboard/admin/brands/search?search=${search}`),
  dropAll: () => request.get(`dashboard/admin/brands/drop/all`),
  restoreAll: () => request.get(`dashboard/admin/brands/restore/all`),
};

export default brandService;
