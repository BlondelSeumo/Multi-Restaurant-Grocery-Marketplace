import React, { useContext, useEffect, useState } from 'react';
import {
  Button,
  Space,
  Table,
  Card,
  Tabs,
  Tag,
  DatePicker,
  Tooltip,
  Dropdown,
  Menu,
} from 'antd';
import { useNavigate, useParams } from 'react-router-dom';
import {
  ClearOutlined,
  DeleteOutlined,
  DownloadOutlined,
  EditOutlined,
  EyeOutlined,
  PlusCircleOutlined,
} from '@ant-design/icons';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import {
  addMenu,
  disableRefetch,
  setMenu,
  setMenuData,
} from '../../redux/slices/menu';
import { useTranslation } from 'react-i18next';

import useDidUpdate from '../../helpers/useDidUpdate';
import { clearItems, fetchOrders } from '../../redux/slices/orders';
import formatSortType from '../../helpers/formatSortType';
import SearchInput from '../../components/search-input';
import { clearOrder } from '../../redux/slices/order';
import numberToPrice from '../../helpers/numberToPrice';
import { DebounceSelect } from '../../components/search';
import userService from '../../services/user';
import OrderStatusModal from './orderStatusModal';
import OrderDeliveryman from './orderDeliveryman';
import FilterColumns from '../../components/filter-column';
import { fetchOrderStatus } from '../../redux/slices/orderStatus';

import ShowLocationsMap from './show-locations.map';
import DownloadModal from './downloadModal';
import { toast } from 'react-toastify';
import DeleteButton from '../../components/delete-button';
import orderService from '../../services/order';
import { Context } from '../../context/context';
import CustomModal from '../../components/modal';
import moment from 'moment';
import { export_url } from '../../configs/app-global';
import { BiMap } from 'react-icons/bi';
import { FaTrashRestoreAlt } from 'react-icons/fa';
import { CgExport } from 'react-icons/cg';
import ResultModal from '../../components/result-modal';
import shopService from '../../services/restaurant';
import { batch } from 'react-redux';
import { useQueryParams } from '../../helpers/useQueryParams';
import OrderTypeSwitcher from './order-type-switcher';
const { TabPane } = Tabs;
const { RangePicker } = DatePicker;

export default function OrderList() {
  const { type } = useParams();
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const { t } = useTranslation();
  const { defaultCurrency } = useSelector(
    (state) => state.currency,
    shallowEqual
  );
  const { statusList } = useSelector(
    (state) => state.orderStatus,
    shallowEqual
  );
  const [orderDetails, setOrderDetails] = useState(null);
  const [locationsMap, setLocationsMap] = useState(null);
  const [dowloadModal, setDowloadModal] = useState(null);
  const [orderDeliveryDetails, setOrderDeliveryDetails] = useState(null);
  const statuses = [
    { name: 'all', id: '7', active: true },
    ...statusList,
    { name: 'deleted_at', id: '8', active: true },
  ];
  const [restore, setRestore] = useState(null);

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

  const [columns, setColumns] = useState([
    {
      title: t('id'),
      is_show: true,
      dataIndex: 'id',
      key: 'id',
      sorter: true,
    },
    {
      title: t('client'),
      is_show: true,
      dataIndex: 'user',
      key: 'user',
      render: (user) => (
        <div>
          {user?.firstname} {user?.lastname || ''}
        </div>
      ),
    },
    {
      title: t('status'),
      is_show: true,
      dataIndex: 'status',
      key: 'status',
      render: (status, row) => (
        <div className='cursor-pointer'>
          {status === 'new' ? (
            <Tag color='blue'>{t(status)}</Tag>
          ) : status === 'canceled' ? (
            <Tag color='error'>{t(status)}</Tag>
          ) : (
            <Tag color='cyan'>{t(status)}</Tag>
          )}
          {status !== 'delivered' &&
          status !== 'canceled' &&
          !row.deleted_at ? (
            <EditOutlined
              onClick={(e) => {
                e.stopPropagation();
                setOrderDetails(row);
              }}
              disabled={row.deleted_at}
            />
          ) : (
            ''
          )}
        </div>
      ),
    },
    {
      title: t('deliveryman'),
      is_show: true,
      dataIndex: 'deliveryman',
      key: 'deliveryman',
      render: (deliveryman, row) => (
        <div>
          {row.status === 'ready' && row.delivery_type !== 'pickup' ? (
            <Button
              disabled={row.deleted_at}
              type='link'
              onClick={() => setOrderDeliveryDetails(row)}
            >
              <Space>
                {deliveryman
                  ? `${deliveryman.firstname} ${deliveryman.lastname}`
                  : t('add.deliveryman')}
                <EditOutlined />
              </Space>
            </Button>
          ) : (
            <div>
              {deliveryman?.firstname} {deliveryman?.lastname}
            </div>
          )}
        </div>
      ),
    },
    {
      title: t('number.of.products'),
      dataIndex: 'order_details_count',
      key: 'order_details_count',
      is_show: true,
      render: (order_details_count) => {
        return (
          <div className='text-lowercase'>
            {order_details_count || 0} {t('products')}
          </div>
        );
      },
    },
    {
      title: t('amount'),
      is_show: true,
      dataIndex: 'total_price',
      key: 'total_price',
      render: (total_price, row) => {
        const status = row.transaction?.status;
        return (
          <>
            <span>{numberToPrice(total_price, defaultCurrency.symbol)}</span>
            <br />
            <span
              className={
                status === 'progress'
                  ? 'text-primary'
                  : status === 'paid'
                  ? 'text-success'
                  : status === 'rejected'
                  ? 'text-danger'
                  : 'text-info'
              }
            >
              {row.transaction?.status}
            </span>
          </>
        );
      },
    },
    {
      title: t('payment.type'),
      is_show: true,
      dataIndex: 'transaction',
      key: 'transaction',
      render: (transaction) => t(transaction?.payment_system?.tag) || '-',
    },
    {
      title: t('created.at'),
      is_show: true,
      dataIndex: 'created_at',
      key: 'created_at',
    },
    {
      title: t('delivery.date'),
      is_show: true,
      dataIndex: 'delivery_date',
      key: 'delivery_date',
    },
    {
      title: t('options'),
      is_show: true,
      key: 'options',
      render: (_, row) => {
        return (
          <Space>
            <Button
              disabled={row.deleted_at}
              icon={<BiMap />}
              onClick={(e) => {
                e.stopPropagation();
                setLocationsMap(row.id);
              }}
            />
            <Button
              disabled={row.deleted_at}
              icon={<EyeOutlined />}
              onClick={(e) => {
                e.stopPropagation();
                goToShow(row);
              }}
            />
            <Button
              type='primary'
              icon={<EditOutlined />}
              onClick={(e) => {
                e.stopPropagation();
                goToEdit(row);
              }}
              disabled={
                row.status === 'delivered' ||
                row.status === 'canceled' ||
                row.deleted_at
              }
            />
            <DeleteButton
              disabled={row.deleted_at}
              icon={<DeleteOutlined />}
              onClick={(e) => {
                e.stopPropagation();
                setId([row.id]);
                setIsModalVisible(true);
                setText(true);
              }}
            />
            <Button
              disabled={row.deleted_at}
              icon={<DownloadOutlined />}
              onClick={(e) => {
                e.stopPropagation();
                setDowloadModal(row.id);
              }}
            />
          </Space>
        );
      },
    },
  ]);

  const { setIsModalVisible } = useContext(Context);
  const [downloading, setDownloading] = useState(false);
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const querryParams = useQueryParams();
  const [role, setRole] = useState(querryParams.values.status || 'all');
  const immutable = activeMenu.data?.role || role;
  const [id, setId] = useState(null);
  const [text, setText] = useState(null);
  const [loadingBtn, setLoadingBtn] = useState(false);
  const [dateRange, setDateRange] = useState(
    moment().subtract(1, 'months'),
    moment()
  );
  const { orders, loading, params, statistic } = useSelector(
    (state) => state.orders,
    shallowEqual
  );
  const data = activeMenu.data;
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

  function onChangePagination(pagination, filters, sorter) {
    const { pageSize: perPage, current: page } = pagination;
    const { field: column, order } = sorter;
    const sort = formatSortType(order);
    dispatch(
      setMenuData({
        activeMenu,
        data: { ...data, perPage, page, column, sort },
      })
    );
  }

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
        dispatch(fetchOrders(paramsData));
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
        dispatch(fetchOrders(paramsData));
        setRestore(null);
      })
      .finally(() => setLoadingBtn(false));
  };

  useDidUpdate(() => {
    dispatch(fetchOrders(paramsData));
  }, [data, dateRange, type]);

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

  const excelExport = () => {
    setDownloading(true);
    orderService
      .export()
      .then((res) => {
        const body = export_url + res.data.file_name;
        window.location.href = body;
      })
      .finally(() => setDownloading(false));
  };

  const onChangeTab = (status) => {
    const orderStatus = status;
    dispatch(setMenuData({ activeMenu, data: { role: orderStatus, page: 1 } }));
    setRole(status);
    navigate(`?status=${orderStatus}`);
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

  useEffect(() => {
    if (activeMenu?.refetch) {
      dispatch(fetchOrders(paramsData));
      dispatch(disableRefetch(activeMenu));
    }
  }, [activeMenu?.refetch]);

  useEffect(() => {
    dispatch(fetchOrderStatus());
  }, []);

  const rowSelection = {
    selectedRowKeys: id,
    onChange: (key) => {
      setId(key);
    },
  };

  const allDelete = () => {
    if (id === null || id.length === 0) {
      toast.warning(t('select.the.product'));
    } else {
      setIsModalVisible(true);
      setText(false);
    }
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
    dispatch(fetchOrders({ status: null, page: data?.page, perPage: 20 }));
  };

  const menu = (
    <Menu>
      <Menu.Item onClick={() => setRestore({ delete: true })}>
        <Space>
          <DeleteOutlined />
          {t('delete.all')}
        </Space>
      </Menu.Item>
      <Menu.Item onClick={() => setRestore({ restore: true })}>
        <Space>
          <FaTrashRestoreAlt />
          {t('restore.all')}
        </Space>
      </Menu.Item>
    </Menu>
  );

  return (
    <>
      <Space className='justify-content-end w-100 mb-3'>
        <OrderTypeSwitcher listType='orders' />
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
        <Space wrap className='order-filter'>
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
            onClick={handleClear}
            style={{ width: '100%' }}
            icon={<ClearOutlined />}
          >
            {t('clear')}
          </Button>
        </Space>
      </Card>

      <Card>
        <Space className='justify-content-between align-items-start w-100'>
          <Tabs onChange={onChangeTab} type='card' activeKey={immutable}>
            {statuses
              .filter((ex) => ex.active === true)
              .map((item) => (
                <TabPane tab={t(item.name)} key={item.name} />
              ))}
          </Tabs>
          <Space>
            {id !== null && id.length !== 0 && (
              <Tooltip title={t('delete.selected')}>
                <DeleteButton type='primary' onClick={allDelete} danger />
              </Tooltip>
            )}
            <FilterColumns setColumns={setColumns} columns={columns} iconOnly />

            <Dropdown overlay={menu}>
              <Button>{t('options')}</Button>
            </Dropdown>
          </Space>
        </Space>
        <Table
          scroll={{ x: true }}
          rowSelection={rowSelection}
          columns={columns?.filter((items) => items.is_show)}
          dataSource={orders}
          loading={loading}
          pagination={{
            pageSize: params.perPage,
            page: activeMenu.data?.page || 1,
            total: statistic?.orders_count,
            defaultCurrent: activeMenu.data?.page,
            current: activeMenu.data?.page,
          }}
          rowKey={(record) => record.id}
          onChange={onChangePagination}
          onRow={(record) => {
            return {
              onClick: () => {
                if (immutable === 'deleted_at') {
                  return;
                }
                goToShow(record);
              },
            };
          }}
        />
      </Card>

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
