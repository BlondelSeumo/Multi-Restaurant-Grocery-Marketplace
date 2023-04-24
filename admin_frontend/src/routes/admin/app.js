// ** React Imports
import { lazy } from 'react';
import { Navigate } from 'react-router-dom';
const NotFound = lazy(() => import('../../views/not-found'));

const AppRoutes = [
  {
    path: 'dashboard',
    component: lazy(() => import('views/dashboard')),
  },
  {
    path: '/',
    component: <Navigate to='dashboard' />,
  },
  {
    path: '*',
    component: <NotFound />,
  },
  {
    path: 'payouts',
    component: lazy(() => import('views/admin-payouts')),
  },
  {
    path: 'pos-system',
    component: lazy(() => import('views/pos-system')),
  },
  {
    path: 'cashback',
    component: lazy(() => import('views/cashback')),
  },
  {
    path: 'stories',
    component: lazy(() => import('views/story')),
  },
  {
    path: 'email/subscriber',
    component: lazy(() => import('views/email-subscribers')),
  },
  {
    path: 'subscriber',
    component: lazy(() => import('views/subscriber')),
  },
  {
    path: 'chat',
    component: lazy(() => import('views/chat')),
  },
  {
    path: 'transactions',
    component: lazy(() => import('views/transactions')),
  },
  {
    path: 'payout-requests',
    component: lazy(() => import('views/payout-requests')),
  },
  {
    path: 'catalog',
    component: lazy(() => import('views/catalog')),
  },
  {
    path: 'bonus/list',
    component: lazy(() => import('views/bonus')),
  },
];

export default AppRoutes;
