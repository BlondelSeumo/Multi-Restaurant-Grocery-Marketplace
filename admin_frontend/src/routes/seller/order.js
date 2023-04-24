// ** React Imports
import { lazy } from 'react';

const SellerOrderRoutes = [
  {
    path: 'seller/orders',
    component: lazy(() => import('views/seller-views/order/order')),
  },
  {
    path: 'seller/orders/:type',
    component: lazy(() => import('views/seller-views/order/order')),
  },
  {
    path: 'seller/orders-board',
    component: lazy(() => import('views/seller-views/order/orders-board')),
  },
  {
    path: 'seller/orders/generate-invoice/:id',
    component: lazy(() => import('components/check')),
  },
  {
    path: 'seller/orders-board/:type',
    component: lazy(() => import('views/seller-views/order/orders-board')),
  },
  {
    path: 'seller/order/details/:id',
    component: lazy(() => import('views/seller-views/order/order-details')),
  },
  {
    path: 'seller/order/:id',
    component: lazy(() => import('views/seller-views/order/order-edit')),
  },
];

export default SellerOrderRoutes;
