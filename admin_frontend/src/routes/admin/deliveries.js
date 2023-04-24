// ** React Imports
import { lazy } from 'react';

const DeliveryRoutes = [
  {
    path: 'deliveries',
    component: lazy(() => import('views/delivery')),
  },
  {
    path: 'deliveries/list',
    component: lazy(() => import('views/deliveryList/deliveriesList')),
  },
  {
    path: 'deliveries/map',
    component: lazy(() => import('views/deliveriesMap/deliveriesMap')),
  },
  {
    path: 'deliveries/map/:id',
    component: lazy(() => import('views/deliveriesMap/delivery-map-orders')),
  },
  {
    path: 'delivery/orders/:id',
    component: lazy(() => import('views/delivery-orders/order-delivery')),
  },
  {
    path: 'delivery/orders',
    component: lazy(() => import('views/delivery-orders')),
  },
  {
    path: 'delivery/statistics',
    component: lazy(() =>
      import('views/delivery-statistics/delivery-statistics')
    ),
  },
  {
    path: 'deliveryman/orders',
    component: lazy(() => import('views/deliveryman-orders')),
  },
  {
    path: 'deliveryman/order/details/:id',
    component: lazy(() => import('views/deliveryman-orders/order-details')),
  },
];

export default DeliveryRoutes;
