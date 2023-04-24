import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import extraService from '../../services/extra';
import extraSellerService from '../../services/seller/extras';

const initialState = {
  loading: false,
  extraGroups: [],
  error: '',
};

export const fetchExtraGroups = createAsyncThunk(
  'extra/fetchExtraGroups',
  (params = {}) => {
    return extraService
      .getAllGroups({ ...initialState.params, ...params })
      .then((res) => res);
  }
);

export const fetchSelletExtraGroups = createAsyncThunk(
  'extra/fetchSellerExtraGroups',
  (params = {}) => {
    return extraSellerService
      .getAllGroups({ ...initialState.params, ...params })
      .then((res) => res);
  }
);

const extraGroupSlice = createSlice({
  name: 'extraGroup',
  initialState,
  extraReducers: (builder) => {
    builder.addCase(fetchExtraGroups.pending, (state) => {
      state.loading = true;
    });
    builder.addCase(fetchExtraGroups.fulfilled, (state, action) => {
      const { payload } = action;
      state.loading = false;
      state.extraGroups = payload.data;
      state.error = '';
    });
    builder.addCase(fetchExtraGroups.rejected, (state, action) => {
      state.loading = false;
      state.extraGroups = [];
      state.error = action.error.message;
    });

    builder.addCase(fetchSelletExtraGroups.pending, (state) => {
      state.loading = true;
    });
    builder.addCase(fetchSelletExtraGroups.fulfilled, (state, action) => {
      const { payload } = action;
      state.loading = false;
      state.extraGroups = payload.data;
      state.error = '';
    });
    builder.addCase(fetchSelletExtraGroups.rejected, (state, action) => {
      state.loading = false;
      state.extraGroups = [];
      state.error = action.error.message;
    });
  },
});

export default extraGroupSlice.reducer;
