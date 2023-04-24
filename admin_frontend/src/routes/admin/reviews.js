// ** React Imports
import { lazy } from 'react';

const ReviewRoutes = [
  {
    path: 'reviews',
    component: lazy(() => import('views/reviews')),
  },
  {
    path: 'reviews/product',
    component: lazy(() => import('views/reviews')),
  },
  {
    path: 'reviews/order',
    component: lazy(() => import('views/reviews/orderReviews')),
  },
  {
    path: 'reviews/deliveryboy',
    component: lazy(() => import('views/reviews/deliveryBoyReviews')),
  },
];

export default ReviewRoutes;
