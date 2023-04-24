import { createSlice } from '@reduxjs/toolkit';

const initialState = {
  cartItems: [],
  cartShops: [],
  cartOrder: null,
  cartPayment: null,
  data: [
    {
      user: '',
      userUuid: '',
      address: '',
      paymentType: '',
      deliveries: null,
      bag_id: 0,
      shop: null,
      delivery_fee: 0,
      currentCurrency: null,
      payment_type: null,
      delivery_time: null,
      delivery_date: null,
      currency_shop: null,
      coupon: null,
    },
  ],
  total: {},
  bags: [0],
  currentBag: 0,
  coupons: [],
  currency: null,
};

const cartSlice = createSlice({
  name: 'cart',
  initialState,
  reducers: {
    addToCart(state, action) {
      const { payload } = action;

      const existingIndex = state.cartItems.findIndex(
        (item) => item.stockID.id === payload.stockID.id
      );
      if (existingIndex >= 0) {
        state.cartItems[existingIndex].quantity += payload.quantity;
      } else {
        state.cartItems.push(payload);
      }
    },
    reduceCart(state, action) {
      const itemIndex = state.cartItems.findIndex(
        (item) => item.stockID.id === action.payload.stockID.id
      );

      if (state.cartItems[itemIndex].quantity > 1) {
        state.cartItems[itemIndex].quantity -= 1;
      }
    },
    removeFromCart(state, action) {
      const { payload } = action;
      const nextCartItems = state.cartItems.filter(
        (item) => item.stockID.id !== payload.stockID.id
      );
      state.cartItems = nextCartItems;
      return state;
    },

    setCurrentBag(state, action) {
      const { payload } = action;
      state.currentBag = payload;
    },
    addBag(state, action) {
      const { payload } = action;
      const newBagId = state.bags.length;
      const newData = {
        user: '',
        userUuid: '',
        address: '',
        currentCurrency: '',
        paymentType: '',
        deliveries: [],
        bag_id: newBagId,
        shop: payload.shop,
      };
      state.data.push(newData);
      state.bags.push(newBagId);
      state.currentBag = newBagId;
    },
    removeBag(state, action) {
      const { payload } = action;
      state.data = state.data.filter((item) => item.bag_id !== payload);
      state.bags = state.bags.filter((item) => item !== payload);
      state.currentBag = 0;
    },
    setCartOrder(state, action) {
      const { payload } = action;
      state.cartOrder = payload;
    },
    setCartPayment(state, action) {
      const { payload } = action;
      state.cartPayment = payload;
    },
    setCurrency(state, action) {
      const { payload } = action;
      state.currency = payload;
    },
    setCartShops(state, action) {
      const { payload } = action;
      state.cartShops = payload;
    },
    setCartData(state, action) {
      const { payload } = action;
      const dataIndex = state.data.findIndex(
        (item) => item.bag_id === payload.bag_id
      );
      state.data[dataIndex] = { ...state.data[dataIndex], ...payload };
    },
    setCartTotal(state, action) {
      const { payload } = action;
      state.total = payload;
    },
    setCartCashback(state, action) {
      const { payload } = action;
      state.total.cashback = payload;
    },
    addCoupon(state, action) {
      const { payload } = action;
      const itemIndex = state.coupons.findIndex(
        (item) => item.shop_id === payload.shop_id
      );
      if (itemIndex >= 0) {
        state.coupons[itemIndex].coupon = payload.coupon;
      } else {
        state.coupons.push(payload);
      }
    },
    verifyCoupon(state, action) {
      const { payload } = action;
      const itemIndex = state.coupons.findIndex(
        (item) => item.shop_id === payload.shop_id
      );
      state.coupons[itemIndex].verified = payload.verified;
      state.coupons[itemIndex].price = payload.price;
    },
    clearCart(state) {
      state.cartItems = state.cartItems.filter(
        (item) => item.bag_id !== state.currentBag
      );
      state.coupons = [];
    },
    clearCartShops(state) {
      state.cartShops = [];
      state.total = initialState.total;
    },
  },
});

export const {
  setCurrency,
  addToCart,
  removeFromCart,
  clearCart,
  reduceCart,
  setCartShops,
  setCartData,
  clearCartShops,
  setCurrentBag,
  setCartTotal,
  addBag,
  removeBag,
  addCoupon,
  verifyCoupon,
  setCartCashback,
  setCartOrder,
  setCartPayment,
} = cartSlice.actions;
export default cartSlice.reducer;
