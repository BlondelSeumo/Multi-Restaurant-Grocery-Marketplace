// ** React Imports
import { lazy } from 'react';

const FoodRoutes = [
  {
    path: 'catalog/products',
    component: lazy(() => import('views/products')),
  },
  {
    path: 'product/add',
    component: lazy(() => import('views/products/products-add')),
  },
  {
    path: 'product/:uuid',
    component: lazy(() => import('views/products/product-edit')),
  },
  {
    path: 'product-clone/:uuid',
    component: lazy(() => import('views/products/product-clone')),
  },
  {
    path: 'catalog/product/const',
    component: lazy(() => import('views/products/product-import')),
  },
];

export default FoodRoutes;
