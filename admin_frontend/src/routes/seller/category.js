// ** React Imports
import { lazy } from 'react';

const SellerCategoryImport = [
  {
    path: 'seller/categories',
    component: lazy(() => import('views/seller-views/categories')),
  },
  {
    path: 'seller/category/add',
    component: lazy(() => import('views/seller-views/categories/category-add')),
  },
  {
    path: 'seller/category/:uuid',
    component: lazy(() =>
      import('views/seller-views/categories/category-edit')
    ),
  },
];

export default SellerCategoryImport;
