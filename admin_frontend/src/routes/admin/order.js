// ** React Imports
import { lazy } from 'react';

const OrderRoutes = [
  {
    path: 'orders',
    component: lazy(() => import('views/order/order-list')),
  },
  {
    path: 'orders/:type',
    component: lazy(() => import('views/order/order-list')),
  },
  {
    path: 'orders-board',
    component: lazy(() => import('views/order/order-board')),
  },
  {
    path: 'orders/generate-invoice/:id',
    component: lazy(() => import('components/check')),
  },
  {
    path: 'orders-board/:type',
    component: lazy(() => import('views/order/order-board')),
  },
  {
    path: 'order/details/:id',
    component: lazy(() => import('views/order/order-details')),
  },
  {
    path: 'order/:id',
    component: lazy(() => import('views/order/order-edit')),
  },
];

export default OrderRoutes;
