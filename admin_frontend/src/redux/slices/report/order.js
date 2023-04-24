import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import ReportService from '../../../services/reports';

const initialState = {
  loading: false,
  chartData: [],
  productList: [],
  error: '',
};

export const fetchOrderProduct = createAsyncThunk(
  'orderReport/fetchOrderProduct',
  (params = {}) => {
    return ReportService.getOrderProducts({
      ...params,
    }).then((res) => res);
  }
);
export const fetchOrderProductChart = createAsyncThunk(
  'orderReport/fetchOrderProductChart',
  (params = {}) => {
    return ReportService.getOrderChart({
      ...params,
    }).then((res) => res);
  }
);
const orderCountSlice = createSlice({
  name: 'orderReport',
  initialState,
  extraReducers: (builder) => {
    builder.addCase(fetchOrderProduct.pending, (state) => {
      state.loading = true;
    });
    builder.addCase(fetchOrderProduct.fulfilled, (state, action) => {
      const { payload } = action;
      state.loading = false;
      state.productList = payload.data;
      state.error = '';
    });
    builder.addCase(fetchOrderProduct.rejected, (state, action) => {
      state.loading = false;
      state.productList = [];
      state.error = action.error.message;
    });

    builder.addCase(fetchOrderProductChart.pending, (state) => {
      state.loading = true;
    });
    builder.addCase(fetchOrderProductChart.fulfilled, (state, action) => {
      const { payload } = action;
      state.loading = false;
      state.chartData = payload.data;
      state.error = '';
    });
    builder.addCase(fetchOrderProductChart.rejected, (state, action) => {
      state.loading = false;
      state.chartData = [];
      state.error = action.error.message;
    });
  },

  reducers: {
    filterOrderProduct(state, action) {
      const { payload } = action;
    },
  },
});
export const { filterOrderProduct } = orderCountSlice.actions;
export default orderCountSlice.reducer;
