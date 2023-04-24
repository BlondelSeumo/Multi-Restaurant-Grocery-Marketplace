// ** React Imports
import { lazy } from 'react';

const SellerFoodRoutes = [
  {
    path: 'seller/product',
    component: lazy(() => import('views/seller-views/product')),
  },
  {
    path: 'seller/product/add',
    component: lazy(() => import('views/seller-views/products/products-add')),
  },
  {
    path: 'seller/product/:uuid',
    component: lazy(() => import('views/seller-views/products/product-edit')),
  },
  {
    path: 'seller/product-clone/:uuid',
    component: lazy(() => import('views/seller-views/products/product-clone')),
  },
  {
    path: 'seller/catalog/product/const',
    component: lazy(() => import('views/seller-views/products/product-import')),
  },
];

export default SellerFoodRoutes;
