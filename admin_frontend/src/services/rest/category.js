import request from '../request';

const categoryService = {
  getAll: (params) => request.get('rest/categories/paginate', { params }),
  getById: (id, params) => request.get(`rest/categories/${id}`, { params }),
  search: (params) => request.get('rest/categories/search', { params }),
};

export default categoryService;
