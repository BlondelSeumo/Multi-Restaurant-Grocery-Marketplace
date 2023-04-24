import { createSlice } from "@reduxjs/toolkit";
import { Currency } from "interfaces";
import { RootState } from "redux/store";

export type CurrencyType = {
  currency: Currency | null;
};

const initialState: CurrencyType = {
  currency: null,
};

const currencySlice = createSlice({
  name: "currency",
  initialState,
  reducers: {
    setCurrency(state, action) {
      const { payload } = action;
      state.currency = payload;
    },
    clearCurrency(state) {
      state.currency = null;
    },
  },
});

export const { setCurrency, clearCurrency } = currencySlice.actions;

export const selectCurrency = (state: RootState) => state.currency.currency;

export default currencySlice.reducer;
