import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import orderService from '../../services/seller/order';

const initialState = {
  loading: false,
  orders: [],
  new: {
    meta: {},
    error: '',
    params: {
      page: 1,
      perPage: 5,
      status: 'new',
    },
    loading: false,
  },
  accepted: {
    meta: {},
    error: '',
    params: {
      page: 1,
      perPage: 5,
      status: 'accepted',
    },
    loading: false,
  },
  ready: {
    meta: {},
    error: '',
    params: {
      page: 1,
      perPage: 5,
      status: 'ready',
    },
    loading: false,
  },
  on_a_way: {
    meta: {},
    error: '',
    params: {
      page: 1,
      perPage: 5,
      status: 'on_a_way',
    },
    loading: false,
  },
  delivered: {
    meta: {},
    error: '',
    params: {
      page: 1,
      perPage: 5,
      status: 'delivered',
    },
    loading: false,
  },
  canceled: {
    meta: {},
    error: '',
    params: {
      page: 1,
      perPage: 5,
      status: 'canceled',
    },
    loading: false,
  },
  error: '',
  params: {
    page: 1,
    perPage: 5,
  },
  meta: {},
  layout: 'table',
  items: {
    new: [],
    accepted: [],
    ready: [],
    on_a_way: [],
    delivered: [],
    canceled: [],
  },
};
export const handleSearch = createAsyncThunk(
  'order/handleSearch',
  (params = {}) => {
    return orderService
      .getAll({ ...initialState.params, ...params })
      .then((res) => res.data);
  }
);
export const fetchOrders = createAsyncThunk(
  'order/fetchOrders',
  (params = {}) => {
    return orderService
      .getAll({ ...initialState.params, ...params })
      .then((res) => res.data);
  }
);
export const fetchNewOrders = createAsyncThunk(
  'order/fetchNewOrders',
  (params = {}) => {
    return orderService
      .getAll({ ...initialState.new.params, ...params })
      .then((res) => res.data);
  }
);
export const fetchAcceptedOrders = createAsyncThunk(
  'order/fetchAcceptedOrders',
  (params = {}) => {
    return orderService
      .getAll({ ...initialState.accepted.params, ...params })
      .then((res) => res.data);
  }
);
export const fetchReadyOrders = createAsyncThunk(
  'order/fetchReadyOrders',
  (params = {}) => {
    return orderService
      .getAll({ ...initialState.ready.params, ...params })
      .then((res) => res.data);
  }
);
export const fetchOnAWayOrders = createAsyncThunk(
  'order/fetchOnAWayOrders',
  (params = {}) => {
    return orderService
      .getAll({ ...initialState.on_a_way.params, ...params })
      .then((res) => res.data);
  }
);
export const fetchDeliveredOrders = createAsyncThunk(
  'order/fetchDeliveredOrders',
  (params = {}) => {
    return orderService
      .getAll({ ...initialState.delivered.params, ...params })
      .then((res) => res.data);
  }
);
export const fetchCanceledOrders = createAsyncThunk(
  'order/fetchCanceledOrders',
  (params = {}) => {
    return orderService
      .getAll({ ...initialState.canceled.params, ...params })
      .then((res) => res.data);
  }
);

const orderSlice = createSlice({
  name: 'sellerOrders',
  initialState,
  extraReducers: (builder) => {
    //handleSearch
    builder.addCase(handleSearch.pending, (state) => {
      state.loading = true;
    });
    builder.addCase(handleSearch.fulfilled, (state, action) => {
      const { payload } = action;
      const groupByStatus = payload.orders.reduce((group, order) => {
        const { status } = order;
        group[status] = group[status] ?? [];
        group[status].push(order);
        return group;
      }, {});
      state.loading = false;
      state.items = {
        new: [],
        accepted: [],
        ready: [],
        on_a_way: [],
        delivered: [],
        canceled: [],
        ...groupByStatus,
      };
      state.meta = payload.meta;
      state.params.page = payload.meta.current_page;
      state.params.perPage = payload.meta.per_page;
      state.error = '';
    });
    builder.addCase(handleSearch.rejected, (state, action) => {
      state.loading = false;
      state.items = {
        new: [],
        accepted: [],
        ready: [],
        on_a_way: [],
        delivered: [],
        canceled: [],
      };
      state.error = action.error.message;
    });

    //fetchOrders
    builder.addCase(fetchOrders.pending, (state) => {
      state.loading = true;
    });
    builder.addCase(fetchOrders.fulfilled, (state, action) => {
      const { payload } = action;
      state.loading = false;
      state.orders = payload.orders;
      state.meta = { ...payload.meta, total: payload.statistic.orders_count };
      state.params.page = payload.meta.current_page;
      state.params.perPage = payload.meta.per_page;
      state.error = '';
    });
    builder.addCase(fetchOrders.rejected, (state, action) => {
      state.loading = false;
      state.orders = [];
      state.error = action.error.message;
    });

    //fetch new orders
    builder.addCase(fetchNewOrders.pending, (state) => {
      state.new.loading = true;
    });
    builder.addCase(fetchNewOrders.fulfilled, (state, action) => {
      const { payload } = action;
      state.new.loading = false;
      state.items = {
        ...state.items,
        new: [...payload.orders, ...state.items.new],
      };
      state.new.meta = payload.meta;
      state.new.params.page = payload.meta.current_page;
      state.new.params.perPage = payload.meta.per_page;
      state.new.error = '';
    });
    builder.addCase(fetchNewOrders.rejected, (state, action) => {
      state.new.loading = false;
      state.items.new = [];
      state.new.error = action.error.message;
    });

    //fetch accepted orders
    builder.addCase(fetchAcceptedOrders.pending, (state) => {
      state.accepted.loading = true;
    });
    builder.addCase(fetchAcceptedOrders.fulfilled, (state, action) => {
      const { payload } = action;
      state.accepted.loading = false;
      state.items = {
        ...state.items,
        accepted: [...payload.orders, ...state.items.accepted],
      };
      state.accepted.meta = payload.meta;
      state.accepted.params.page = payload.meta.current_page;
      state.accepted.params.perPage = payload.meta.per_page;
      state.accepted.error = '';
    });
    builder.addCase(fetchAcceptedOrders.rejected, (state, action) => {
      state.accepted.loading = false;
      state.items.accepted = [];
      state.accepted.error = action.error.message;
    });

    //fetch ready orders
    builder.addCase(fetchReadyOrders.pending, (state) => {
      state.ready.loading = true;
    });
    builder.addCase(fetchReadyOrders.fulfilled, (state, action) => {
      const { payload } = action;
      state.ready.loading = false;
      state.items = {
        ...state.items,
        ready: [...payload.orders, ...state.items.ready],
      };
      state.ready.meta = payload.meta;
      state.ready.params.page = payload.meta.current_page;
      state.ready.params.perPage = payload.meta.per_page;
      state.ready.error = '';
    });
    builder.addCase(fetchReadyOrders.rejected, (state, action) => {
      state.ready.loading = false;
      state.items.ready = [];
      state.ready.error = action.error.message;
    });

    //fetch on a way orders
    builder.addCase(fetchOnAWayOrders.pending, (state) => {
      state.on_a_way.loading = true;
    });
    builder.addCase(fetchOnAWayOrders.fulfilled, (state, action) => {
      const { payload } = action;
      state.on_a_way.loading = false;
      state.items = {
        ...state.items,
        on_a_way: [...payload.orders, ...state.items.on_a_way],
      };
      state.on_a_way.meta = payload.meta;
      state.on_a_way.params.page = payload.meta.current_page;
      state.on_a_way.params.perPage = payload.meta.per_page;
      state.on_a_way.error = '';
    });
    builder.addCase(fetchOnAWayOrders.rejected, (state, action) => {
      state.on_a_way.loading = false;
      state.items.on_a_way = [];
      state.on_a_way.error = action.error.message;
    });

    //fetch delivered orders
    builder.addCase(fetchDeliveredOrders.pending, (state) => {
      state.delivered.loading = true;
    });
    builder.addCase(fetchDeliveredOrders.fulfilled, (state, action) => {
      const { payload } = action;
      state.delivered.loading = false;
      state.items = {
        ...state.items,
        delivered: [...payload.orders, ...state.items.delivered],
      };
      state.delivered.meta = payload.meta;
      state.delivered.params.page = payload.meta.current_page;
      state.delivered.params.perPage = payload.meta.per_page;
      state.delivered.error = '';
    });
    builder.addCase(fetchDeliveredOrders.rejected, (state, action) => {
      state.delivered.loading = false;
      state.items.delivered = [];
      state.delivered.error = action.error.message;
    });

    //fetch canceled orders
    builder.addCase(fetchCanceledOrders.pending, (state) => {
      state.canceled.loading = true;
    });
    builder.addCase(fetchCanceledOrders.fulfilled, (state, action) => {
      const { payload } = action;
      state.canceled.loading = false;
      state.items = {
        ...state.items,
        canceled: [...payload.orders, ...state.items.canceled],
      };
      state.canceled.meta = payload.meta;
      state.canceled.params.page = payload.meta.current_page;
      state.canceled.params.perPage = payload.meta.per_page;
      state.canceled.error = '';
    });
    builder.addCase(fetchCanceledOrders.rejected, (state, action) => {
      state.canceled.loading = false;
      state.items.canceled = [];
      state.canceled.error = action.error.message;
    });
  },

  reducers: {
    changeLayout(state, action) {
      state.layout = action.payload;
    },
    setItems(state, action) {
      state.items = action.payload;
    },
    clearCurrentOrders(state, action) {
      state.items[action.payload] = [];
    },
    clearItems(state, action) {
      state.items = {
        new: [],
        accepted: [],
        ready: [],
        on_a_way: [],
        delivered: [],
        canceled: [],
      };
    },
  },
});
export const { changeLayout, setItems, clearCurrentOrders, clearItems } =
  orderSlice.actions;
export default orderSlice.reducer;
