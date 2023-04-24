import { createSlice } from '@reduxjs/toolkit';

const initialState = {
  user: null,
};

const authSlice = createSlice({
  name: 'auth',
  initialState,
  reducers: {
    setUserData(state, action) {
      const { payload } = action;
      state.user = payload;
    },
    updateUser(state, action) {
      const { payload } = action;
      state.user = {
        ...state.user,
        ...payload,
        fullName: payload.firstname + ' ' + payload.lastname,
      };
    },
    clearUser(state) {
      state.user = null;
    },
  },
});

export const { setUserData, clearUser, updateUser } = authSlice.actions;
export default authSlice.reducer;
