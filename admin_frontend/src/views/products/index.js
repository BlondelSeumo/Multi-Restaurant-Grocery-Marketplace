import React, { useContext, useEffect, useState } from 'react';
import {
  ClearOutlined,
  CopyOutlined,
  DeleteOutlined,
  EditOutlined,
  PlusCircleOutlined,
} from '@ant-design/icons';
import { Button, Table, Image, Card, Space, Switch, Tag, Tabs } from 'antd';
import { useNavigate } from 'react-router-dom';
import { toast } from 'react-toastify';
import { export_url, IMG_URL } from '../../configs/app-global';
import { Context } from '../../context/context';
import CustomModal from '../../components/modal';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { addMenu, disableRefetch, setMenuData } from '../../redux/slices/menu';
import productService from '../../services/product';
import { fetchProducts } from '../../redux/slices/product';
import useDidUpdate from '../../helpers/useDidUpdate';
import { DebounceSelect } from '../../components/search';
import brandService from '../../services/brand';
import categoryService from '../../services/category';
import shopService from '../../services/restaurant';
import SearchInput from '../../components/search-input';
import formatSortType from '../../helpers/formatSortType';
import { useTranslation } from 'react-i18next';
import DeleteButton from '../../components/delete-button';
import ProductStatusModal from './productStatusModal';
import FilterColumns from '../../components/filter-column';
import { FaTrashRestoreAlt } from 'react-icons/fa';
import ResultModal from '../../components/result-modal';
import RiveResult from '../../components/rive-result';
import { CgExport, CgImport } from 'react-icons/cg';

const colors = ['blue', 'red', 'gold', 'volcano', 'cyan', 'lime'];
const roles = ['all', 'pending', 'published', 'unpublished', 'deleted_at'];
const { TabPane } = Tabs;

const ProductCategories = () => {
  const { t } = useTranslation();
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const [productDetails, setProductDetails] = useState(null);
  const [active, setActive] = useState(null);
  const [text, setText] = useState(null);
  const [restore, setRestore] = useState(null);
  const [role, setRole] = useState('all');

  const clearData = () => {
    dispatch(
      setMenuData({
        activeMenu,
        data: null,
      })
    );
  };

  const goToEdit = (uuid) => {
    dispatch(
      addMenu({
        id: `product-edit`,
        url: `product/${uuid}`,
        name: t('edit.product'),
      })
    );
    clearData();
    navigate(`/product/${uuid}`);
  };

  const goToClone = (uuid) => {
    dispatch(
      addMenu({
        id: `product-clone`,
        url: `product-clone/${uuid}`,
        name: t('clone.product'),
      })
    );
    clearData();
    navigate(`/product-clone/${uuid}`);
  };

  const [columns, setColumns] = useState([
    {
      title: t('id'),
      dataIndex: 'id',
      is_show: true,
      sorter: (a, b) => a.id - b.id,
    },
    {
      title: t('image'),
      dataIndex: 'img',
      is_show: true,
      render: (img, row) => {
        return (
          <Image
            width={100}
            src={!row.deleted_at ? IMG_URL + img : 'https://fakeimg.pl/640x360'}
            placeholder
            style={{ borderRadius: 4 }}
          />
        );
      },
    },
    {
      title: t('name'),
      dataIndex: 'name',
      is_show: true,
    },
    {
      title: t('translations'),
      dataIndex: 'locales',
      is_show: true,
      render: (_, row) => {
        return (
          <Space>
            {row.locales?.map((item, index) => (
              <Tag className='text-uppercase' color={[colors[index]]}>
                {item}
              </Tag>
            ))}
          </Space>
        );
      },
    },
    {
      title: t('shop'),
      dataIndex: 'shop_id',
      is_show: true,
      render: (_, row) => {
        return row.shop?.translation?.title;
      },
    },
    {
      title: t('category'),
      dataIndex: 'category_name',
      is_show: true,
    },
    {
      title: t('active'),
      dataIndex: 'active',
      is_show: true,
      render: (active, row) => {
        return (
          <Switch
            onChange={() => {
              setIsModalVisible(true);
              setId(row.uuid);
              setActive(true);
            }}
            disabled={row.deleted_at}
            checked={active}
          />
        );
      },
    },
    {
      title: t('status'),
      is_show: true,
      dataIndex: 'status',
      key: 'status',
      render: (status, row) => (
        <div>
          {status === 'new' ? (
            <Tag color='blue'>{t(status)}</Tag>
          ) : status === 'canceled' ? (
            <Tag color='error'>{t(status)}</Tag>
          ) : (
            <Tag color='cyan'>{t(status)}</Tag>
          )}
          {!row.deleted_at ? (
            <EditOutlined onClick={() => setProductDetails(row)} />
          ) : (
            ''
          )}
        </div>
      ),
    },
    {
      title: t('options'),
      dataIndex: 'options',
      is_show: true,
      render: (_, row) => {
        return (
          <Space>
            <Button
              type='primary'
              icon={<EditOutlined />}
              onClick={() => goToEdit(row.uuid)}
              disabled={row.deleted_at}
            />
            <Button
              icon={<CopyOutlined />}
              onClick={() => goToClone(row.uuid)}
              disabled={row.deleted_at}
            />
            <DeleteButton
              icon={<DeleteOutlined />}
              onClick={() => {
                setIsModalVisible(true);
                setId([row.id]);
                setText(true);
                setActive(false);
              }}
              disabled={row.deleted_at}
            />
          </Space>
        );
      },
    },
  ]);

  const [id, setId] = useState(null);
  const { setIsModalVisible } = useContext(Context);
  const [loadingBtn, setLoadingBtn] = useState(false);
  const [downloading, setDownloading] = useState(false);
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const { products, meta, loading, params } = useSelector(
    (state) => state.product,
    shallowEqual
  );
  const immutable = activeMenu.data?.role || role;
  const data = activeMenu.data;
  const paramsData = {
    search: data?.search,
    brand_id: data?.brand?.value,
    category_id: data?.category?.value,
    shop_id: data?.shop?.value,
    sort: data?.sort,
    status:
      immutable === 'deleted_at'
        ? undefined
        : immutable === 'all'
        ? undefined
        : immutable,
    deleted_at: immutable === 'deleted_at' ? immutable : null,
    column: data?.column,
    perPage: data?.perPage,
    page: data?.page,
  };

  const goToImport = () => {
    dispatch(
      addMenu({
        id: 'product-import',
        url: `catalog/product/import`,
        name: t('product.import'),
      })
    );
    navigate(`/catalog/product/import`);
  };

  const productDelete = () => {
    setLoadingBtn(true);
    const params = {
      ...Object.assign(
        {},
        ...id.map((item, index) => ({
          [`ids[${index}]`]: item,
        }))
      ),
    };
    productService
      .delete(params)
      .then(() => {
        setIsModalVisible(false);
        toast.success(t('successfully.deleted'));
        dispatch(fetchProducts(paramsData));
        setText(null);
        setActive(false);
      })
      .finally(() => setLoadingBtn(false));
  };

  const productDropAll = () => {
    setLoadingBtn(true);
    productService
      .dropAll()
      .then(() => {
        toast.success(t('successfully.deleted'));
        dispatch(fetchProducts());
        setRestore(null);
      })
      .finally(() => setLoadingBtn(false));
  };

  const productRestoreAll = () => {
    setLoadingBtn(true);
    productService
      .restoreAll()
      .then(() => {
        toast.success(t('successfully.deleted'));
        dispatch(fetchProducts(paramsData));
        setRestore(null);
      })
      .finally(() => setLoadingBtn(false));
  };

  const handleActive = () => {
    setLoadingBtn(true);
    productService
      .setActive(id)
      .then(() => {
        setIsModalVisible(false);
        dispatch(fetchProducts(paramsData));
        toast.success(t('successfully.updated'));
        setActive(false);
      })
      .finally(() => setLoadingBtn(false));
  };

  function onChangePagination(pagination, filter, sorter) {
    const { pageSize: perPage, current: page } = pagination;
    const { field: column, order } = sorter;
    const sort = formatSortType(order);
    dispatch(
      setMenuData({
        activeMenu,
        data: { ...activeMenu.data, perPage, page, column, sort },
      })
    );
  }

  useDidUpdate(() => {
    dispatch(fetchProducts(paramsData));
  }, [activeMenu.data]);

  useEffect(() => {
    if (activeMenu.refetch) {
      dispatch(fetchProducts(paramsData));
      dispatch(disableRefetch(activeMenu));
    }
  }, [activeMenu.refetch]);

  const excelExport = () => {
    setDownloading(true);
    const body = {
      shop_id: activeMenu.data.shop.value,
    };
    productService
      .export(body)
      .then((res) => {
        const body = export_url + res.data.file_name;
        window.location.href = body;
      })
      .finally(() => setDownloading(false));
  };

  const goToAddProduct = () => {
    dispatch(
      addMenu({
        id: 'product-add',
        url: `product/add`,
        name: t('add.product'),
      })
    );
    clearData();
    navigate(`/product/add`);
  };

  async function fetchBrands(search) {
    return brandService.search(search).then(({ data }) =>
      data.map((item) => ({
        label: item.title,
        value: item.id,
      }))
    );
  }

  async function fetchCategories(search) {
    const params = {
      search: search.length === 0 ? null : search,
      type: 'main',
    };
    return categoryService.search(params).then(({ data }) =>
      data.map((item) => ({
        label: item.translation?.title,
        value: item.id,
      }))
    );
  }

  async function fetchUserShop(search) {
    const params = { search, status: 'approved' };
    return shopService.search(params).then((res) =>
      res.data.map((item) => ({
        label: item.translation !== null ? item.translation.title : 'no name',
        value: item.id,
      }))
    );
  }

  const handleFilter = (items) => {
    const data = activeMenu.data;
    dispatch(
      setMenuData({
        activeMenu,
        data: { ...data, ...items },
      })
    );
  };

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
    dispatch(
      setMenuData({
        activeMenu,
        data: undefined,
      })
    );
  };

  return (
    <React.Fragment>
      <Card className='p-0'>
        <Space wrap size={[14, 20]}>
          <SearchInput
            placeholder={t('search')}
            handleChange={(e) => handleFilter({ search: e })}
            defaultValue={activeMenu.data?.search}
            resetSearch={!activeMenu.data?.search}
            style={{ minWidth: 300 }}
          />
          <DebounceSelect
            placeholder={t('select.shop')}
            fetchOptions={fetchUserShop}
            style={{ minWidth: 180 }}
            onChange={(e) => handleFilter({ shop: e })}
            value={activeMenu.data?.shop}
          />
          <DebounceSelect
            placeholder={t('select.category')}
            fetchOptions={fetchCategories}
            style={{ minWidth: 180 }}
            onChange={(e) => handleFilter({ category: e })}
            value={activeMenu.data?.category}
          />
          <DebounceSelect
            placeholder={t('select.brand')}
            fetchOptions={fetchBrands}
            style={{ minWidth: 180 }}
            onChange={(e) => handleFilter({ brand: e })}
            value={activeMenu.data?.brand}
          />

          <Button onClick={goToImport}>
            <CgImport className='mr-2' />
            {t('import')}
          </Button>
          <Button loading={downloading} onClick={excelExport}>
            <CgExport className='mr-2' />
            {t('export')}
          </Button>
          <FilterColumns columns={columns} setColumns={setColumns} />
          <Button
            icon={<PlusCircleOutlined />}
            type='primary'
            onClick={goToAddProduct}
          >
            {t('add.food')}
          </Button>
          <DeleteButton size='' onClick={allDelete}>
            {t('delete.selected')}
          </DeleteButton>

          {immutable !== 'deleted_at' ? (
            <DeleteButton size='' onClick={() => setRestore({ delete: true })}>
              {t('delete.all')}
            </DeleteButton>
          ) : (
            <DeleteButton
              icon={<FaTrashRestoreAlt className='mr-2' />}
              onClick={() => setRestore({ restore: true })}
            >
              {t('restore.all')}
            </DeleteButton>
          )}
          <Button
            icon={<ClearOutlined />}
            onClick={handleClear}
            disabled={!activeMenu.data}
            style={{ minWidth: 150 }}
          />
        </Space>
      </Card>

      <Card title={t('food')}>
        <Tabs
          className='mt-3'
          activeKey={immutable}
          onChange={(key) => {
            handleFilter({ role: key, page: 1 });
            setRole(key);
          }}
          type='card'
        >
          {roles.map((item) => (
            <TabPane tab={t(item)} key={item} />
          ))}
        </Tabs>
        <Table
          locale={{
            emptyText: <RiveResult />,
          }}
          scroll={{ x: true }}
          rowSelection={rowSelection}
          loading={loading}
          columns={columns?.filter((item) => item.is_show)}
          dataSource={products}
          pagination={{
            pageSize: params.perPage,
            page: activeMenu.data?.page || 1,
            total: meta.total,
            defaultCurrent: activeMenu.data?.page,
            current: activeMenu.data?.page,
          }}
          onChange={onChangePagination}
          rowKey={(record) => record.id}
        />
      </Card>
      {productDetails && (
        <ProductStatusModal
          orderDetails={productDetails}
          handleCancel={() => setProductDetails(null)}
          paramsData={paramsData}
        />
      )}
      <CustomModal
        click={active ? handleActive : productDelete}
        text={
          active ? t('set.active.food') : text ? t('delete') : t('all.delete')
        }
        loading={loadingBtn}
        setText={setId}
        setActive={setActive}
      />
      {restore && (
        <ResultModal
          open={restore}
          handleCancel={() => setRestore(null)}
          click={restore.restore ? productRestoreAll : productDropAll}
          text={restore.restore ? t('restore.modal.text') : t('read.carefully')}
          subTitle={restore.restore ? '' : t('confirm.deletion')}
          loading={loadingBtn}
          setText={setId}
          setActive={setActive}
        />
      )}
    </React.Fragment>
  );
};

export default ProductCategories;
