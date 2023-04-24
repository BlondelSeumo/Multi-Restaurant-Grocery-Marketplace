import {
  Card,
  Col,
  Row,
  Space,
  Typography,
  Table,
  Tag,
  Button,
  DatePicker,
  Spin,
  Select,
} from 'antd';
import React, { useContext, useEffect, useState } from 'react';
import SearchInput from '../../components/search-input';
import { CloudDownloadOutlined } from '@ant-design/icons';
import ReportService from '../../services/reports';
import { disableRefetch } from '../../redux/slices/menu';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import ReportChart from '../../components/report/chart';
import { AsyncSelect } from '../../components/async-select';
import moment from 'moment';
import { ReportContext } from '../../context/report';
import FilterColumns from '../../components/filter-column';
import {
  clearCompare,
  fetchReportProduct,
  fetchReportProductChart,
  productCompare,
} from '../../redux/slices/report/products';
import useDidUpdate from '../../helpers/useDidUpdate';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import QueryString from 'qs';
import { t } from 'i18next';
import numberToPrice from '../../helpers/numberToPrice';
import { useMemo } from 'react';
import shopService from '../../services/shop';
const { Text, Title } = Typography;
const { RangePicker } = DatePicker;
const { Option } = Select;

const ReportProducts = () => {
  const dispatch = useDispatch();
  const location = useLocation();
  const navigate = useNavigate();
  const category_id = QueryString.parse(location.search, [])['?category_id'];
  const product_id = QueryString.parse(location.search, [])['?product_id'];
  const [shopId, setShopId] = useState();
  const [shopList, setShopList] = useState();
  const [isShoplistLoading, setIsShoplistLoading] = useState(false);
  const { date_from, date_to, by_time, chart, handleChart, handleDateRange } =
    useContext(ReportContext);

  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);

  const {
    loading,
    chartData: reportData,
    productList,
  } = useSelector((state) => state.productReport, shallowEqual);

  const [selectedRowKeys, setSelectedRowKeys] = useState([]);
  const [downloading, setDownloading] = useState(false);
  const [search, setSearch] = useState('');
  const { defaultCurrency } = useSelector(
    (state) => state.currency,
    shallowEqual
  );

  const expandedRowRender = (row) => {
    const columns = [
      {
        title: t('extras.name'),
        dataIndex: 'Extras name',
        render: (_, data) =>
          data.extras?.map((extra) => extra.group.translation?.title).join(','),
        key: 'Extras name',
      },
      {
        title: t('item.sold'),
        dataIndex: 'order_quantity',
        key: 'order_quantity',
      },
      {
        title: t('net.sales'),
        dataIndex: 'upgradeNum',
        render: (_, data) => numberToPrice(data.price, defaultCurrency?.symbol),
        key: 'upgradeNum',
      },
      {
        title: t('orders'),
        dataIndex: 'count',
        key: 'count',
      },
      {
        title: t('stock'),
        dataIndex: 'quantity',
        key: 'quantity',
      },
    ];
    return (
      <Table
        scroll={{ x: true }}
        columns={columns}
        dataSource={row.stocks}
        pagination={false}
      />
    );
  };

  const [columns, setColumns] = useState([
    {
      title: t('product.title'),
      dataIndex: 'translation_title',
      key: 'translation_title',
      render: (_, data) => {
        return (
          <Link to={`/report/products?product_id=${data.id}`}>
            {data?.translation.title}
          </Link>
        );
      },
      is_show: true,
      sorter: (a, b) =>
        a?.translation?.title.localeCompare(b?.translation?.title),
    },
    {
      title: t('bar.code'),
      dataIndex: 'bar_code',
      key: 'bar_code',
      is_show: true,
      render: (_, data) => {
        return <>{data?.bar_code || '-'}</>;
      },
    },
    {
      title: t('item.sold'),
      dataIndex: 'quantity',
      key: 'quantity',
      sorter: (a, b) => a.quantity - b.quantity,
      is_show: true,
    },
    {
      title: t('net.sales'),
      dataIndex: 'price',
      key: 'price',
      is_show: true,
      render: (price) => numberToPrice(price, defaultCurrency?.symbol),
      sorter: (a, b) => a.price - b.price,
    },
    {
      title: t('orders'),
      key: 'count',
      dataIndex: 'count',
      is_show: true,
      sorter: (a, b) => a.count - b.count,
    },
    {
      title: t('category'),
      key: 'category',
      dataIndex: 'category',
      is_show: true,
      render: (_, row) => {
        return (
          <Link to={`/report/products?category_id=${row.category?.id}`}>
            {row?.category?.translation?.title}
          </Link>
        );
      },
    },
    {
      title: t('status'),
      key: 'active',
      dataIndex: 'active',
      render: (_, data) => {
        const status = Boolean(data?.active);
        return (
          <Tag color={status ? 'green' : 'red'} key={data.id}>
            {status ? 'Active' : 'Inactive'}
          </Tag>
        );
      },
      is_show: true,
    },
  ]);

  const chart_type = useMemo(
    () => [
      {
        label: 'item.sold',
        value: 'quantity',
        qty: 'quantity',
        price: false,
      },
      { label: 'net.sales', value: 'price', qty: 'price', price: true },
      { label: 'orders', value: 'count', qty: 'count', price: false },
    ],
    []
  );

  const fetchReport = () => {
    const params = {
      date_from,
      date_to,
      type: by_time,
      chart,
    };
    if (category_id) params.categories = [category_id];
    if (product_id) params.products = [product_id];
    if (chart_type.find((item) => item.value === chart)) {
      dispatch(fetchReportProductChart(params));
    }
  };

  const fetchShops = () => {
    if (!shopList) {
      setIsShoplistLoading(true);
      shopService.selectPaginate({ perPage: 30 }).then((res) => {
        setIsShoplistLoading(false);
        setShopList(res);
      });
    }
  };

  const fetchProduct = (page, perPage) => {
    const params = {
      date_from,
      date_to,
      type: by_time,
      page,
      perPage,
      search: search || null,
    };
    if (category_id) params.categories = [category_id];
    if (product_id) params.products = [product_id];
    if (shopId) params.shop_id = shopId;
    dispatch(fetchReportProduct(params));
  };

  useEffect(() => {
    handleChart(chart_type[0].value);
  }, []);

  useEffect(() => {
    if (activeMenu.refetch) {
      fetchProduct();
      fetchReport();
      dispatch(disableRefetch(activeMenu));
    }
  }, [activeMenu.refetch]);

  useDidUpdate(() => {
    fetchProduct();
  }, [date_to, search, category_id, product_id, by_time, date_from, shopId]);

  useDidUpdate(() => {
    fetchReport();
  }, [date_to, by_time, chart, category_id, product_id, date_from]);

  const onChangePagination = (pagination) => {
    const { pageSize: perPage, current: page } = pagination;
    fetchProduct(page, perPage);
  };

  const excelExport = () => {
    setDownloading(true);
    ReportService.getReportProductList({
      date_from,
      date_to,
      type: by_time,
      export: 'excel',
    })
      .then((res) => {
        const body = res.data.data.link;
        if (body) {
          window.location.href = body;
        }
      })
      .finally(() => setDownloading(false));
  };

  const onSelectChange = (newSelectedRowKeys) => {
    setSelectedRowKeys(newSelectedRowKeys);
  };

  const rowSelection = {
    selectedRowKeys,
    onChange: onSelectChange,
  };

  const Compare = () => {
    dispatch(productCompare(selectedRowKeys));
  };

  const clear = () => {
    dispatch(clearCompare());
    setShopId(undefined);
    setSelectedRowKeys([]);
    fetchProduct();
    fetchReport();
    navigate(`/report/products`);
  };

  return (
    <Spin size='large' spinning={loading}>
      <Row gutter={24} className='mb-3'>
        <Col span={12}>
          <Space>
            <RangePicker
              defaultValue={[moment(date_from), moment(date_to)]}
              onChange={handleDateRange}
            />
          </Space>
        </Col>
      </Row>
      <Row gutter={24} className='report-products'>
        {chart_type?.map((item) => (
          <Col
            span={8}
            key={item.label}
            onClick={() => handleChart(item.value)}
          >
            <Card className={chart === item.value && 'active'}>
              <Row className='mb-5'>
                <Col>
                  <Text>{t(item.label)}</Text>
                </Col>
              </Row>
              <Row gutter={24}>
                <Col span={12}>
                  <Title level={2}>
                    {!item?.price
                      ? reportData[item.qty]
                      : numberToPrice(
                          reportData[item.qty],
                          defaultCurrency?.symbol
                        )}
                  </Title>
                </Col>
              </Row>
            </Card>
          </Col>
        ))}
      </Row>
      <ReportChart reportData={reportData} chart_data='quantities_sum' />
      <Card>
        <Space className='d-flex justify-content-between align-center'>
          <Typography.Text strong level={3}>
            Products
          </Typography.Text>
          <Space className='d-flex justify-content-between'>
            <SearchInput
              style={{ minWidth: '300px' }}
              handleChange={(e) => setSearch(e)}
            />
            <Select
              notFoundContent={isShoplistLoading ? <Spin size='small' /> : null}
              onFocus={fetchShops}
              value={shopId}
              placeholder='Select shop'
              onSelect={(value) => setShopId(value)}
              style={{ width: '200px' }}
            >
              {shopList?.map((shop) => (
                <Option key={shop.id} value={shop.id}>
                  {shop.translation.title}
                </Option>
              ))}
            </Select>
            <Button
              color='geekblue'
              onClick={Compare}
              disabled={Boolean(!selectedRowKeys.length)}
            >
              {t('compare')}
            </Button>
            <Button
              type={
                Boolean(selectedRowKeys.length) ||
                !!category_id ||
                !!product_id ||
                !!shopId
                  ? 'primary'
                  : 'default'
              }
              danger={
                Boolean(selectedRowKeys.length) ||
                !!category_id ||
                !!product_id ||
                !!shopId
              }
              onClick={clear}
            >
              {t('clear')}
            </Button>
            <Button
              icon={<CloudDownloadOutlined />}
              loading={downloading}
              onClick={excelExport}
            >
              {t('download')}
            </Button>
            <FilterColumns columns={columns} setColumns={setColumns} />
          </Space>
        </Space>

        <Table
          expandable={{
            expandedRowRender,
            defaultExpandedRowKeys: ['0'],
          }}
          rowSelection={rowSelection}
          columns={columns?.filter((item) => item.is_show)}
          dataSource={productList.data?.data}
          rowKey={(row) => row.id}
          loading={loading}
          pagination={{
            pageSize: 10,
            page: productList?.data?.meta.page || 1,
            total: productList?.data?.meta.total,
            defaultCurrent: 1,
          }}
          onChange={onChangePagination}
          scroll={{
            x: 1500,
          }}
        />
      </Card>
    </Spin>
  );
};

export default ReportProducts;
