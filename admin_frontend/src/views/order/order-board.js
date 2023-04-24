import React, { useContext, useEffect, useState } from 'react';
import { Button, Space, Card, DatePicker } from 'antd';
import { useNavigate, useParams } from 'react-router-dom';
import { ClearOutlined, PlusCircleOutlined } from '@ant-design/icons';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import {
  addMenu,
  disableRefetch,
  setMenu,
  setMenuData,
} from '../../redux/slices/menu';
import { useTranslation } from 'react-i18next';
import useDidUpdate from '../../helpers/useDidUpdate';

import {
  clearItems,
  fetchAcceptedOrders,
  fetchCanceledOrders,
  fetchDeliveredOrders,
  fetchNewOrders,
  fetchOnAWayOrders,
  fetchOrders,
  fetchReadyOrders,
  handleSearch,
} from '../../redux/slices/orders';

import SearchInput from '../../components/search-input';
import { clearOrder } from '../../redux/slices/order';
import { DebounceSelect } from '../../components/search';
import userService from '../../services/user';
import OrderStatusModal from './orderStatusModal';
import OrderDeliveryman from './orderDeliveryman';

import ShowLocationsMap from './show-locations.map';
import DownloadModal from './downloadModal';
import { toast } from 'react-toastify';
import orderService from '../../services/order';
import { Context } from '../../context/context';
import CustomModal from '../../components/modal';
import moment from 'moment';
import ResultModal from '../../components/result-modal';
import shopService from '../../services/restaurant';
import Incorporate from './dnd/Incorporate';
import { batch } from 'react-redux';
import OrderTypeSwitcher from './order-type-switcher';
import { CgExport } from 'react-icons/cg';
import { export_url } from '../../configs/app-global';
const { RangePicker } = DatePicker;

export default function OrderBoard() {
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const { t } = useTranslation();
  const { type } = useParams();

  const { statusList } = useSelector(
    (state) => state.orderStatus,
    shallowEqual
  );

  const [orderDetails, setOrderDetails] = useState(null);
  const [locationsMap, setLocationsMap] = useState(null);
  const [dowloadModal, setDowloadModal] = useState(null);
  const [downloading, setDownLoading] = useState(false);
  const [orderDeliveryDetails, setOrderDeliveryDetails] = useState(null);
  const [restore, setRestore] = useState(null);
  const [tabType, setTabType] = useState(null);

  const goToEdit = (row) => {
    dispatch(clearOrder());
    dispatch(
      addMenu({
        url: `order/${row.id}`,
        id: 'order_edit',
        name: t('edit.order'),
      })
    );
    navigate(`/order/${row.id}`);
  };

  const goToShow = (row) => {
    dispatch(
      addMenu({
        url: `order/details/${row.id}`,
        id: 'order_details',
        name: t('order.details'),
      })
    );
    navigate(`/order/details/${row.id}`);
  };

  const { setIsModalVisible } = useContext(Context);
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const [id, setId] = useState(null);
  const [text, setText] = useState(null);
  const [loadingBtn, setLoadingBtn] = useState(false);
  const [dateRange, setDateRange] = useState(
    moment().subtract(1, 'months'),
    moment()
  );
  const { layout } = useSelector((state) => state.orders, shallowEqual);
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
        fetchOrderAllItem({ status: tabType });
        setText(null);
      })
      .finally(() => setLoadingBtn(false));
  };

  const orderDropAll = () => {
    setLoadingBtn(true);
    orderService
      .dropAll()
      .then(() => {
        toast.success(t('successfully.deleted'));
        dispatch(fetchOrders());
        setRestore(null);
      })
      .finally(() => setLoadingBtn(false));
  };

  const orderRestoreAll = () => {
    setLoadingBtn(true);
    orderService
      .restoreAll()
      .then(() => {
        toast.success(t('successfully.restored'));
        dispatch(fetchOrders());
        setRestore(null);
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
      user_id: data?.user_id,
      status: data?.role !== 'deleted_at' && data?.role,
      deleted_at: data?.role === 'deleted_at' ? 'deleted_at' : undefined,
      shop_id:
        activeMenu.data?.shop_id !== null ? activeMenu.data?.shop_id : null,
      delivery_type: type !== 'scheduled' ? type : undefined,
      delivery_date_from:
        type === 'scheduled'
          ? moment().add(1, 'day').format('YYYY-MM-DD')
          : undefined,
      date_from: dateRange[0]?.format('YYYY-MM-DD') || null,
      date_to: dateRange[1]?.format('YYYY-MM-DD') || null,
    };
    if (layout === 'table' && data) {
      dispatch(fetchOrders(paramsData));
    } else if (data) {
      dispatch(handleSearch(paramsData));
    }
  }, [data, dateRange, type]);

  const excelExport = () => {
    setDownLoading(true);
    orderService
      .export()
      .then((res) => {
        const body = export_url + res.data.file_name;
        window.location.href = body;
      })
      .finally(() => setDownLoading(false));
  };

  const handleFilter = (item, name) => {
    dispatch(
      setMenuData({
        activeMenu,
        data: { ...data, ...{ [name]: item } },
      })
    );
  };

  async function getUsers(search) {
    const params = {
      search,
      perPage: 10,
    };
    return userService.search(params).then(({ data }) => {
      return data.map((item) => ({
        label: `${item.firstname} ${item.lastname}`,
        value: item.id,
      }));
    });
  }

  const goToOrderCreate = () => {
    dispatch(clearOrder());
    dispatch(
      setMenu({
        id: 'pos.system_01',
        url: 'pos-system',
        name: 'pos.system',
      })
    );
    navigate('/pos-system');
  };

  const handleCloseModal = () => {
    setOrderDetails(null);
    setOrderDeliveryDetails(null);
    setLocationsMap(null);
    setDowloadModal(null);
  };

  async function fetchShops(search) {
    const params = { search, status: 'approved' };
    return shopService.getAll(params).then(({ data }) =>
      data.map((item) => ({
        label: item.translation?.title,
        value: item.id,
      }))
    );
  }

  const fetchOrdersCase = (params) => {
    const paramsWithType = {
      ...params,
      delivery_type: type !== 'scheduled' ? type : undefined,
      delivery_date_from:
        type === 'scheduled'
          ? moment().add(1, 'day').format('YYYY-MM-DD')
          : undefined,
    };
    switch (params.status) {
      case 'new':
        dispatch(fetchNewOrders(paramsWithType));
        break;
      case 'accepted':
        dispatch(fetchAcceptedOrders(paramsWithType));
        break;
      case 'ready':
        dispatch(fetchReadyOrders(paramsWithType));
        break;
      case 'on_a_way':
        dispatch(fetchOnAWayOrders(paramsWithType));
        break;
      case 'delivered':
        dispatch(fetchDeliveredOrders(paramsWithType));
        break;
      case 'canceled':
        dispatch(fetchCanceledOrders(paramsWithType));
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

  useEffect(() => {
    if (activeMenu?.refetch) {
      const params = {
        page: data?.page,
        perPage: 10,
        sort: data?.sort,
        column: data?.column,
        delivery_type: type !== 'scheduled' ? type : undefined,
        delivery_date_from:
          type === 'scheduled'
            ? moment().add(1, 'day').format('YYYY-MM-DD')
            : undefined,
      };
      dispatch(fetchOrders(params));
      dispatch(disableRefetch(activeMenu));
    }
  }, [activeMenu?.refetch]);

  return (
    <>
      <Space className='w-100 justify-content-end mb-3'>
        <OrderTypeSwitcher listType='orders-board' />
        <Button
          type='primary'
          icon={<PlusCircleOutlined />}
          onClick={goToOrderCreate}
          style={{ width: '100%' }}
        >
          {t('add.order')}
        </Button>
      </Space>
      <Card>
        <Space wrap className='order-filter' size={[8, 15]}>
          <SearchInput
            defaultValue={data?.search}
            resetSearch={!data?.search}
            placeholder={t('search')}
            handleChange={(search) => handleFilter(search, 'search')}
          />
          <DebounceSelect
            placeholder={t('select.shop')}
            fetchOptions={fetchShops}
            style={{ width: '100%' }}
            onSelect={(shop) => handleFilter(shop.value, 'shop_id')}
            onDeselect={() => handleFilter(null, 'shop_id')}
            allowClear={true}
            value={data?.shop_id}
          />
          <DebounceSelect
            placeholder={t('select.client')}
            fetchOptions={getUsers}
            onSelect={(user) => handleFilter(user.value, 'user_id')}
            onDeselect={() => handleFilter(null, 'user_id')}
            style={{ width: '100%' }}
            value={data?.user_id}
          />
          <RangePicker
            value={dateRange}
            onChange={(values) => {
              handleFilter((prev) => ({
                ...prev,
                ...{
                  date_from: values?.[0]?.format('YYYY-MM-DD'),
                  date_to: values?.[1]?.format('YYYY-MM-DD'),
                },
              }));
              setDateRange(values);
            }}
            disabledDate={(current) => {
              return current && current > moment().endOf('day');
            }}
            style={{ width: '100%' }}
          />
          <Button
            onClick={excelExport}
            loading={downloading}
            style={{ width: '100%' }}
          >
            <CgExport className='mr-2' />
            {t('export')}
          </Button>
          <Button
            icon={<ClearOutlined />}
            onClick={handleClear}
            style={{ width: '100%' }}
          >
            {t('clear')}
          </Button>
        </Space>
      </Card>

      <Incorporate
        goToEdit={goToEdit}
        goToShow={goToShow}
        fetchOrderAllItem={fetchOrderAllItem}
        fetchOrders={fetchOrdersCase}
        setLocationsMap={setLocationsMap}
        setId={setId}
        setIsModalVisible={setIsModalVisible}
        setText={setText}
        setDowloadModal={setDowloadModal}
        type={type}
        setTabType={setTabType}
      />

      {orderDetails && (
        <OrderStatusModal
          orderDetails={orderDetails}
          handleCancel={handleCloseModal}
          status={statusList}
        />
      )}
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
      <CustomModal
        click={orderDelete}
        text={text ? t('delete') : t('all.delete')}
        loading={loadingBtn}
        setText={setId}
      />
      {restore && (
        <ResultModal
          open={restore}
          handleCancel={() => setRestore(null)}
          click={restore.restore ? orderRestoreAll : orderDropAll}
          text={restore.restore ? t('restore.modal.text') : t('read.carefully')}
          subTitle={restore.restore ? '' : t('confirm.deletion')}
          loading={loadingBtn}
          setText={setId}
        />
      )}
    </>
  );
}
