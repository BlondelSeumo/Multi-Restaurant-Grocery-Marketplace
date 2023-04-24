import React, { useEffect, useState, useContext } from 'react';
import { Button, Space, Card } from 'antd';
import { useNavigate, useParams } from 'react-router-dom';
import { ClearOutlined, PlusCircleOutlined } from '@ant-design/icons';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import {
  addMenu,
  disableRefetch,
  setMenuData,
} from '../../../redux/slices/menu';
import { useTranslation } from 'react-i18next';
import useDidUpdate from '../../../helpers/useDidUpdate';
import {
  fetchOrders as fetchSellerOrders,
  handleSearch,
} from '../../../redux/slices/sellerOrders';
import SearchInput from '../../../components/search-input';
import { DebounceSelect } from '../../../components/search';
import userService from '../../../services/seller/user';
import { fetchRestOrderStatus } from '../../../redux/slices/orderStatus';
import DeleteButton from '../../../components/delete-button';
import { Context } from '../../../context/context';
import { toast } from 'react-toastify';
import orderService from '../../../services/seller/order';
import Incorporate from './dnd/Incorporate';
import {
  clearItems,
  fetchAcceptedOrders,
  fetchCanceledOrders,
  fetchDeliveredOrders,
  fetchNewOrders,
  fetchOnAWayOrders,
  fetchReadyOrders,
} from '../../../redux/slices/sellerOrders';
import { batch } from 'react-redux';
import OrderDeliveryman from './orderDeliveryman';
import OrderTypeSwitcher from './order-type-switcher';
import { clearOrder } from '../../../redux/slices/order';
import ShowLocationsMap from './show-locations.map';
import DownloadModal from './downloadModal';
import CustomModal from '../../../components/modal';

export default function SellerOrdersBoard() {
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const { t } = useTranslation();
  const [id, setId] = useState(null);
  const { setIsModalVisible } = useContext(Context);
  const [text, setText] = useState(null);
  const [loadingBtn, setLoadingBtn] = useState(false);
  const [locationsMap, setLocationsMap] = useState(null);
  const [dowloadModal, setDowloadModal] = useState(null);
  const [orderDeliveryDetails, setOrderDeliveryDetails] = useState(null);
  const [type, setType] = useState(null);

  const goToShow = (row) => {
    dispatch(
      addMenu({
        url: `seller/order/details/${row.id}`,
        id: 'order_details',
        name: t('order.details'),
      })
    );
    navigate(`/seller/order/details/${row.id}`);
  };

  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);

  const data = activeMenu?.data;

  const orderDelete = () => {
    setLoadingBtn(true);
    const params = {
      ...Object.assign(
        {},
        ...id.map((item, index) => ({
          [`ids[${index}]`]: item,
        }))
      ),
    };
    orderService
      .delete(params)
      .then(() => {
        toast.success(t('successfully.deleted'));
        setIsModalVisible(false);
        fetchOrderAllItem({ status: type });
        setText(null);
      })
      .finally(() => setLoadingBtn(false));
  };

  useDidUpdate(() => {
    const paramsData = {
      search: data?.search,
      sort: data?.sort,
      column: data?.column,
      perPage: data?.perPage,
      page: data?.page,
      user_id: data?.userId,
      status: data?.status,
    };
    dispatch(handleSearch(paramsData));
  }, [data]);

  useEffect(() => {
    if (activeMenu?.refetch) {
      const params = {
        status: data?.status,
        perPage: 10,
      };
      dispatch(fetchSellerOrders(params));
      dispatch(fetchRestOrderStatus());
      dispatch(disableRefetch(activeMenu));
    }
  }, [activeMenu?.refetch]);

  const handleFilter = (item, name) => {
    dispatch(
      setMenuData({
        activeMenu,
        data: { ...data, [name]: item },
      })
    );
  };

  async function getUsers(search) {
    const params = {
      search,
      perPage: 10,
    };
    return userService.getAll(params).then(({ data }) => {
      return data.map((item) => ({
        label: `${item.firstname} ${item.lastname || ''}`,
        value: item.id,
      }));
    });
  }

  const allDelete = () => {
    if (id === null || id.length === 0) {
      toast.warning(t('select.the.product'));
    } else {
      setIsModalVisible(true);
      setText(false);
    }
  };

  const fetchOrdersCase = (params) => {
    switch (params.status) {
      case 'new':
        dispatch(fetchNewOrders(params));
        break;
      case 'accepted':
        dispatch(fetchAcceptedOrders(params));
        break;
      case 'ready':
        dispatch(fetchReadyOrders(params));
        break;
      case 'on_a_way':
        dispatch(fetchOnAWayOrders(params));
        break;
      case 'delivered':
        dispatch(fetchDeliveredOrders(params));
        break;
      case 'canceled':
        dispatch(fetchCanceledOrders(params));
        break;
      default:
        console.log(`Sorry, we are out of`);
    }
  };

  const fetchOrderAllItem = () => {
    fetchOrdersCase({ status: 'new' });
    fetchOrdersCase({ status: 'accepted' });
    fetchOrdersCase({ status: 'ready' });
    fetchOrdersCase({ status: 'on_a_way' });
    fetchOrdersCase({ status: 'delivered' });
    fetchOrdersCase({ status: 'canceled' });
  };

  const handleClear = () => {
    batch(() => {
      dispatch(clearItems());
      dispatch(
        setMenuData({
          activeMenu,
          data: null,
        })
      );
    });
    fetchOrderAllItem();
  };

  const handleCloseModal = () => {
    setOrderDeliveryDetails(null);
    setLocationsMap(null);
    setDowloadModal(null);
  };

  const goToAddOrder = () => {
    dispatch(clearOrder());
    dispatch(
      addMenu({
        id: 'pos.system',
        url: 'seller/pos-system',
        name: t('add.order'),
      })
    );
    navigate('/seller/pos-system');
  };

  return (
    <>
      <Space className='justify-content-end w-100 mb-3'>
        <OrderTypeSwitcher listType='seller/orders-board' />
        <Button
          type='primary'
          icon={<PlusCircleOutlined />}
          onClick={goToAddOrder}
          style={{ width: '100%' }}
        >
          {t('add.order')}
        </Button>
      </Space>
      <Card>
        <Space wrap>
          <SearchInput
            placeholder={t('search')}
            handleChange={(search) => handleFilter(search, 'search')}
            defaultValue={activeMenu.data?.search}
          />
          <DebounceSelect
            placeholder={t('select.client')}
            fetchOptions={getUsers}
            onSelect={(user) => handleFilter(user.value, 'search')}
            onDeselect={() => handleFilter(null, 'search')}
            style={{ minWidth: 200 }}
          />
          <Button icon={<ClearOutlined />} onClick={handleClear}>
            {t('clear')}
          </Button>
          <DeleteButton size='' onClick={allDelete}>
            {t('delete.selected')}
          </DeleteButton>
        </Space>
      </Card>

      <Incorporate
        goToShow={goToShow}
        fetchOrderAllItem={fetchOrderAllItem}
        fetchOrders={fetchOrdersCase}
        setLocationsMap={setLocationsMap}
        setId={setId}
        setIsModalVisible={setIsModalVisible}
        setText={setText}
        setDowloadModal={setDowloadModal}
        type={type}
        setType={setType}
      />
      <CustomModal
        click={orderDelete}
        text={text ? t('delete') : t('all.delete')}
        loading={loadingBtn}
        setText={setId}
        setActive={setId}
      />
      {orderDeliveryDetails && (
        <OrderDeliveryman
          orderDetails={orderDeliveryDetails}
          handleCancel={handleCloseModal}
        />
      )}
      {locationsMap && (
        <ShowLocationsMap id={locationsMap} handleCancel={handleCloseModal} />
      )}
      {dowloadModal && (
        <DownloadModal id={dowloadModal} handleCancel={handleCloseModal} />
      )}
    </>
  );
}
