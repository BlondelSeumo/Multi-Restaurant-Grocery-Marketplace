import React, { useEffect } from 'react';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { disableRefetch } from '../../redux/slices/menu';
import {
  fetchDeliverymanStatisticsCount,
  fetchSellerStatisticsCount,
  fetchStatistics,
} from '../../redux/slices/statistics/count';
import {
  fetchSellerTopCustomers,
  fetchTopCustomers,
} from '../../redux/slices/statistics/topCustomers';
import {
  fetchSellerTopProducts,
  fetchTopProducts,
} from '../../redux/slices/statistics/topProducts';
import Loading from '../../components/loading';
import {
  fetchOrderCounts,
  fetchSellerOrderCounts,
} from '../../redux/slices/statistics/orderCounts';
import {
  fetchOrderSales,
  fetchSellerOrderSales,
} from '../../redux/slices/statistics/orderSales';
import GeneralDashboard from './generalDashboard';
import DeliverymanDashboard from './deliverymanDashboard';
import ManagerDashboard from './managerDashboard';
import ModeratorDashboard from './moderatorDashboard';

export default function Dashboard() {
  const dispatch = useDispatch();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const { user } = useSelector((state) => state.auth, shallowEqual);
  const { loading } = useSelector(
    (state) => state.statisticsCount,
    shallowEqual
  );

  function getDashboardsByRole() {
    const body = { time: 'subWeek' };
    switch (user?.role) {
      case 'admin':
        dispatch(fetchStatistics(body));
        dispatch(fetchTopCustomers(body));
        dispatch(fetchTopProducts(body));
        dispatch(fetchOrderCounts(body));
        dispatch(fetchOrderSales(body));
        break;
      case 'manager':
        dispatch(fetchTopCustomers(body));
        dispatch(fetchTopProducts(body));
        break;
      case 'seller':
        dispatch(fetchSellerStatisticsCount(body));
        dispatch(fetchSellerTopCustomers(body));
        dispatch(fetchSellerTopProducts());
        dispatch(fetchSellerOrderCounts());
        dispatch(fetchSellerOrderSales());
        break;
      case 'moderator':
        break;
      case 'deliveryman':
        dispatch(fetchDeliverymanStatisticsCount());
        break;

      default:
        break;
    }
  }

  useEffect(() => {
    if (activeMenu.refetch) {
      getDashboardsByRole();
      dispatch(disableRefetch(activeMenu));
    }
  }, [activeMenu.refetch]);

  const renderDashboardByRole = () => {
    switch (user.role) {
      case 'admin':
        return <GeneralDashboard />;
      case 'manager':
        return <ManagerDashboard />;
      case 'seller':
        return <GeneralDashboard />;
      case 'moderator':
        return <ModeratorDashboard />;
      case 'deliveryman':
        return <DeliverymanDashboard />;

      default:
        return null;
    }
  };

  return (
    <div className='h-100'>
      {!loading ? renderDashboardByRole() : <Loading size='large' />}
    </div>
  );
}
