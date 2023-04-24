// ** React Imports
import { lazy } from 'react';

const BranchRoutes = [
  {
    path: 'shops',
    component: lazy(() => import('views/branches')),
  },
  {
    path: 'shop/add',
    component: lazy(() => import('views/branches/branch-add')),
  },
  {
    path: 'shop/:uuid',
    component: lazy(() => import('views/branches/branch-edit')),
  },
  {
    path: 'shop/:uuid',
    component: lazy(() => import('views/branches/branch-clone')),
  },
];

export default BranchRoutes;
