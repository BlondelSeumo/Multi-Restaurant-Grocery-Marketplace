import { IBlog, Paginate, SuccessResponse } from "interfaces";
import request from "./request";

const blogService = {
  getAll: (params?: any): Promise<Paginate<IBlog>> =>
    request.get(`/rest/blogs/paginate?type=blog`, { params }),
  getById: (id: string, params?: any): Promise<SuccessResponse<IBlog>> =>
    request.get(`/rest/blogs/${id}`, { params }),
};

export default blogService;
