import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import statisticService from '../../../services/statistics';
import sellerStatisticService from '../../../services/seller/statistics';
import deliverymanStatisticService from '../../../services/deliveryman/statistics';

const initialState = {
  loading: false,
  counts: {},
  error: '',
};

export const fetchStatistics = createAsyncThunk(
  'statistics/fetchStatistics',
  (params = {}) => {
    return statisticService.getAll(params).then((res) => res);
  }
);

export const fetchSellerStatisticsCount = createAsyncThunk(
  'statistics/fetchSellerStatisticsCount',
  (params = {}) => {
    return sellerStatisticService.getAll(params).then((res) => res);
  }
);

export const fetchDeliverymanStatisticsCount = createAsyncThunk(
  'statistics/fetchDeliverymanStatisticsCount',
  (params = {}) => {
    return deliverymanStatisticService.getAllCount(params).then((res) => res);
  }
);

const statisticsCountSlice = createSlice({
  name: 'statisticsCount',
  initialState,
  extraReducers: (builder) => {
    builder.addCase(fetchStatistics.pending, (state) => {
      state.loading = true;
    });
    builder.addCase(fetchStatistics.fulfilled, (state, action) => {
      const { payload } = action;
      state.loading = false;
      state.counts = payload.data;
      state.error = '';
    });
    builder.addCase(fetchStatistics.rejected, (state, action) => {
      state.loading = false;
      state.counts = {};
      state.error = action.error.message;
    });

    builder.addCase(fetchSellerStatisticsCount.pending, (state) => {
      state.loading = true;
    });
    builder.addCase(fetchSellerStatisticsCount.fulfilled, (state, action) => {
      const { payload } = action;

      state.loading = false;
      state.counts = payload.data;
      state.error = '';
    });
    builder.addCase(fetchSellerStatisticsCount.rejected, (state, action) => {
      state.loading = false;
      state.counts = {};
      state.error = action.error.message;
    });

    builder.addCase(fetchDeliverymanStatisticsCount.pending, (state) => {
      state.loading = true;
    });
    builder.addCase(
      fetchDeliverymanStatisticsCount.fulfilled,
      (state, action) => {
        const { payload } = action;
        state.loading = false;
        state.counts = payload.data;
        state.error = '';
      }
    );
    builder.addCase(
      fetchDeliverymanStatisticsCount.rejected,
      (state, action) => {
        state.loading = false;
        state.counts = {};
        state.error = action.error.message;
      }
    );
  },
});

export default statisticsCountSlice.reducer;
