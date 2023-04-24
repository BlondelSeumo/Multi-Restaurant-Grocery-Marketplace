import { Banner, Paginate, SuccessResponse } from "interfaces";
import request from "./request";

const bannerService = {
  getAll: (params?: any): Promise<Paginate<Banner>> =>
    request.get(`/rest/banners/paginate`, { params }),
  getById: (id: string, params?: any): Promise<SuccessResponse<Banner>> =>
    request.get(`/rest/banners/${id}`, { params }),
};

export default bannerService;
