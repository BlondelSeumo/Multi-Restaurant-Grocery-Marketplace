// ** React Imports
import { lazy } from 'react';

const SellerAppRoutes = [
  {
    path: 'seller/galleries',
    component: lazy(() => import('views/seller-views/gallaries')),
  },
  {
    path: 'seller/payouts',
    component: lazy(() => import('views/seller-views/payouts')),
  },
  {
    path: 'seller/subscriptions',
    component: lazy(() => import('views/seller-views/subscriptions')),
  },
  {
    path: 'seller/transactions',
    component: lazy(() => import('views/seller-views/transactions')),
  },
  {
    path: 'seller/invites',
    component: lazy(() => import('views/seller-views/invites')),
  },
  {
    path: 'seller/discounts',
    component: lazy(() => import('views/seller-views/discounts')),
  },
  {
    path: 'seller/pos-system',
    component: lazy(() => import('views/seller-views/pos-system')),
  },
  {
    path: 'seller/refunds',
    component: lazy(() => import('views/seller-views/refund')),
  },
  {
    path: 'seller/refund/details/:id',
    component: lazy(() => import('views/seller-views/refund/refund-details')),
  },
];

export default SellerAppRoutes;
