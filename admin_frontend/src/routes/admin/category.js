// ** React Imports
import { lazy } from 'react';

const CategoryRoutes = [
  {
    path: 'catalog/categories',
    component: lazy(() => import('views/categories')),
  },
  {
    path: 'category/add',
    component: lazy(() => import('views/categories/category-add')),
  },
  {
    path: 'category/:uuid',
    component: lazy(() => import('views/categories/category-edit')),
  },
  {
    path: 'category-clone/:uuid',
    component: lazy(() => import('views/categories/category-clone')),
  },
  {
    path: 'catalog/categories/import',
    component: lazy(() => import('views/categories/category-import')),
  },
];

export default CategoryRoutes;
