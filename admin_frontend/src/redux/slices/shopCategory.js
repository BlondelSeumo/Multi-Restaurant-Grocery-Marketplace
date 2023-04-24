import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import categoryService from '../../services/category';

const initialState = {
  loading: false,
  shopCategories: [],
  error: '',
  params: {
    page: 1,
    perPage: 10,
    type: 'shop',
  },
  meta: {},
};

export const fetchShopCategories = createAsyncThunk(
  'category/fetchShopCategories',
  (params = {}) => {
    return categoryService
      .getAll({ ...initialState.params, ...params })
      .then((res) => res);
  }
);

const shopCategorySlice = createSlice({
  name: 'shop-category',
  initialState,
  extraReducers: (builder) => {
    builder.addCase(fetchShopCategories.pending, (state) => {
      state.loading = true;
    });
    builder.addCase(fetchShopCategories.fulfilled, (state, action) => {
      const { payload } = action;
      state.loading = false;
      state.shopCategories = payload.data.map((item) => ({
        active: item.active,
        img: item.img,
        name: item.translation !== null ? item.translation.title : 'no name',
        key: item.uuid + '_' + item.id,
        uuid: item.uuid,
        id: item.id,
        locales: item.locales,
        // children: item.children?.map((child) => ({
        //   name:
        //     child.translation !== null ? child.translation.title : 'no name',
        //   uuid: child.uuid,
        //   key: item.uuid + '_' + child.id,
        //   img: child.img,
        //   id: item.id,
        //   active: child.active,
        //   locales: item.locales,
        //   children: child.children?.map((three) => ({
        //     name:
        //       three.translation !== null ? three.translation.title : 'no name',
        //     uuid: three.uuid,
        //     key: child.uuid + '_' + three.id,
        //     img: three.img,
        //     id: three.id,
        //     active: three.active,
        //     locales: item.locales,
        //   })),
        // })),
      }));
      state.meta = payload.meta;
      state.params.page = payload.meta.current_page;
      state.params.perPage = payload.meta.per_page;
      state.error = '';
    });
    builder.addCase(fetchShopCategories.rejected, (state, action) => {
      state.loading = false;
      state.shopCategories = [];
      state.error = action.error.message;
    });
  },
});

export default shopCategorySlice.reducer;
