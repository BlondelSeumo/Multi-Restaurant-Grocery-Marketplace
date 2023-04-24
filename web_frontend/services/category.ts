import { Category, Paginate } from "interfaces";
import request from "./request";

const categoryService = {
  getAllShopCategories: (params: any = {}): Promise<Paginate<Category>> =>
    request.get(`/rest/categories/paginate`, {
      params: { ...params, type: "shop" },
    }),
  getAllProductCategories: (
    id: number,
    params?: any
  ): Promise<Paginate<Category>> =>
    request.get(`/rest/shops/${id}/categories`, { params }),
  getAllRecipeCategories: (params: any = {}): Promise<Paginate<Category>> =>
    request.get(`/rest/categories/paginate`, {
      params: { ...params, type: "receipt" },
    }),
};

export default categoryService;
