import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import extraService from '../../services/extra';
import sellerExtraService from '../../services/seller/extras';

const initialState = {
  loading: false,
  extraValues: [],
  error: '',
};

export const fetchExtraValues = createAsyncThunk(
  'extra/fetchExtraValues',
  (params = {}) => {
    return extraService
      .getAllValues({ ...initialState.params, ...params })
      .then((res) => res);
  }
);

export const fetchSelletExtraValue = createAsyncThunk(
  'extra/fetchSelletExtraValue',
  (params = {}) => {
    return sellerExtraService
      .getAllValues({ ...initialState.params, ...params })
      .then((res) => res);
  }
);

const extraValueSlice = createSlice({
  name: 'extraValue',
  initialState,
  extraReducers: (builder) => {
    builder.addCase(fetchExtraValues.pending, (state) => {
      state.loading = true;
    });
    builder.addCase(fetchExtraValues.fulfilled, (state, action) => {
      const { payload } = action;
      state.loading = false;
      state.extraValues = payload.data;
      state.error = '';
    });
    builder.addCase(fetchExtraValues.rejected, (state, action) => {
      state.loading = false;
      state.extraValues = [];
      state.error = action.error.message;
    });

    builder.addCase(fetchSelletExtraValue.pending, (state) => {
      state.loading = true;
    });
    builder.addCase(fetchSelletExtraValue.fulfilled, (state, action) => {
      const { payload } = action;
      state.loading = false;
      state.extraValues = payload.data;
      state.error = '';
    });
    builder.addCase(fetchSelletExtraValue.rejected, (state, action) => {
      state.loading = false;
      state.extraValues = [];
      state.error = action.error.message;
    });
  },
});

export default extraValueSlice.reducer;
