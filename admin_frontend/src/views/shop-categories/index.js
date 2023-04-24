import React, { useContext, useEffect, useState } from 'react';
import {
  ClearOutlined,
  CopyOutlined,
  DeleteOutlined,
  EditOutlined,
  PlusCircleOutlined,
} from '@ant-design/icons';
import { Button, Card, Image, Space, Table, Tabs, Tag } from 'antd';
import { export_url } from '../../configs/app-global';
import { Context } from '../../context/context';
import { useNavigate } from 'react-router-dom';
import { toast } from 'react-toastify';
import CustomModal from '../../components/modal';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { addMenu, disableRefetch, setMenuData } from '../../redux/slices/menu';
import categoryService from '../../services/category';
import { fetchShopCategories } from '../../redux/slices/shopCategory';
import { useTranslation } from 'react-i18next';
import DeleteButton from '../../components/delete-button';
import FilterColumns from '../../components/filter-column';
import SearchInput from '../../components/search-input';
import useDidUpdate from '../../helpers/useDidUpdate';
import { FaTrashRestoreAlt } from 'react-icons/fa';
import { CgExport, CgImport } from 'react-icons/cg';
import ResultModal from '../../components/result-modal';
const colors = ['blue', 'red', 'gold', 'volcano', 'cyan', 'lime'];

const { TabPane } = Tabs;
const roles = ['published', 'deleted_at'];

const Categories = () => {
  const { t } = useTranslation();
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const [restore, setRestore] = useState(null);

  const goToEdit = (row) => {
    dispatch(
      addMenu({
        url: `shop/category/${row.uuid}`,
        id: 'category_edit',
        name: t('edit.category'),
      })
    );
    navigate(`/shop/category/${row.uuid}`, { state: 'edit' });
  };

  const goToAddCategory = () => {
    dispatch(
      addMenu({
        id: 'shop/category-add',
        url: 'shop/category/add',
        name: t('add.category'),
      })
    );
    navigate('/shop/category/add');
  };

  const goToImport = () => {
    dispatch(
      addMenu({
        data: activeMenu.data,
        url: `catalog/categories/import`,
        id: 'category_import',
        name: t('import.category'),
      })
    );
    navigate(`/catalog/categories/import`);
  };

  const goToClone = (uuid) => {
    dispatch(
      addMenu({
        data: activeMenu.data,
        id: `shop.category-clone`,
        url: `shop/category-clone/${uuid}`,
        name: t('shop.category.clone'),
      })
    );
    navigate(`/shop/category-clone/${uuid}`, { state: 'clone' });
  };

  const [columns, setColumns] = useState([
    {
      title: t('id'),
      dataIndex: 'id',
      key: 'id',
      is_show: true,
    },
    {
      title: t('name'),
      dataIndex: 'name',
      key: 'name',
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
      title: t('image'),
      dataIndex: 'img',
      key: 'img',
      is_show: true,
      render: (img, row) => {
        return (
          <Image
            src={row.deleted_at ? 'https://via.placeholder.com/150' : img}
            alt='img_gallery'
            width={100}
            className='rounded'
            preview
            placeholder
            key={img + row.id}
          />
        );
      },
    },
    {
      title: t('status'),
      dataIndex: 'active',
      key: 'active',
      is_show: true,
      render: (active) =>
        active ? (
          <Tag color='cyan'> {t('active')}</Tag>
        ) : (
          <Tag color='yellow'>{t('inactive')}</Tag>
        ),
    },
    {
      title: t('options'),
      key: 'options',
      dataIndex: 'options',
      is_show: true,
      render: (_, row) => {
        return (
          <Space>
            <Button
              disabled={row.deleted_at}
              type='primary'
              icon={<EditOutlined />}
              onClick={() => goToEdit(row)}
            />
            <Button
              icon={<CopyOutlined />}
              onClick={() => goToClone(row.uuid)}
              disabled={row.deleted_at}
            />
            <DeleteButton
              disabled={row.deleted_at}
              icon={<DeleteOutlined />}
              onClick={() => {
                setId([row.id]);
                setType(false);
                setIsModalVisible(true);
                setText(true);
              }}
            />
          </Space>
        );
      },
    },
  ]);

  const { setIsModalVisible } = useContext(Context);
  const [id, setId] = useState(null);
  const [type, setType] = useState(false);
  const [loadingBtn, setLoadingBtn] = useState(false);
  const [downloading, setDownloading] = useState(false);
  const [text, setText] = useState(null);
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const [role, setRole] = useState('published');
  const immutable = activeMenu.data?.role || role;
  const { shopCategories, meta, loading } = useSelector(
    (state) => state.shopCategory,
    shallowEqual
  );
  const data = activeMenu.data;
  const paramsData = {
    search: data?.search,
    perPage: data?.perPage,
    page: data?.page,
    status: data?.role === 'deleted_at' ? null : data?.role || 'published',
    deleted_at: data?.role === 'deleted_at' ? data?.role : null,
  };

  const categoryDelete = () => {
    setLoadingBtn(true);
    const params = {
      ...Object.assign(
        {},
        ...id.map((item, index) => ({
          [`ids[${index}]`]: item,
        }))
      ),
    };
    categoryService
      .delete(params)
      .then(() => {
        dispatch(fetchShopCategories(paramsData));
        toast.success(t('successfully.deleted'));
      })
      .finally(() => {
        setIsModalVisible(false);
        setLoadingBtn(false);
        setText(null);
      });
  };

  const shopCategoryDropAll = () => {
    setLoadingBtn(true);
    categoryService
      .dropAll()
      .then(() => {
        toast.success(t('successfully.deleted'));
        dispatch(fetchShopCategories());
        setRestore(null);
      })
      .finally(() => setLoadingBtn(false));
  };

  const shopCategoryRestoreAll = () => {
    setLoadingBtn(true);
    categoryService
      .restoreAll()
      .then(() => {
        toast.success(t('successfully.restored'));
        dispatch(fetchShopCategories());
        setRestore(null);
      })
      .finally(() => setLoadingBtn(false));
  };

  useEffect(() => {
    if (activeMenu.refetch) {
      dispatch(fetchShopCategories(paramsData));
      dispatch(disableRefetch(activeMenu));
    }
  }, [activeMenu.refetch]);

  useDidUpdate(() => {
    dispatch(fetchShopCategories(paramsData));
  }, [activeMenu.data]);

  const onChangePagination = (pageNumber) => {
    const { pageSize, current } = pageNumber;
    dispatch(
      setMenuData({
        activeMenu,
        data: { ...activeMenu.data, perPage: pageSize, page: current },
      })
    );
  };

  const excelExport = () => {
    setDownloading(true);
    categoryService
      .export()
      .then((res) => {
        const body = export_url + res.data.file_name;
        window.location.href = body;
      })
      .finally(() => setDownloading(false));
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

  const handleFilter = (items) => {
    const data = activeMenu.data;
    dispatch(
      setMenuData({
        activeMenu,
        data: { ...data, ...items },
      })
    );
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
    <>
      <Card className='p-0'>
        <Space wrap size={[14, 20]}>
          <SearchInput
            placeholder={t('search')}
            className='w-25'
            handleChange={(e) => {
              handleFilter({ search: e });
            }}
            defaultValue={activeMenu.data?.search}
            resetSearch={!activeMenu.data?.search}
            style={{ minWidth: 300 }}
          />

          <DeleteButton size='' onClick={allDelete}>
            {t('delete.selected')}
          </DeleteButton>

          {activeMenu.data?.role !== 'deleted_at' ? (
            <DeleteButton onClick={() => setRestore({ delete: true })}>
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

          <Button style={{ minWidth: 150 }} onClick={goToImport}>
            <CgImport className='mr-2' />
            {t('import')}
          </Button>

          <Button
            style={{ minWidth: 150 }}
            loading={downloading}
            onClick={excelExport}
          >
            <CgExport className='mr-2' />
            {t('export')}
          </Button>

          <Button
            icon={<ClearOutlined />}
            onClick={handleClear}
            disabled={!activeMenu.data}
            style={{ minWidth: 100 }}
          />
          <Button
            type='primary'
            icon={<PlusCircleOutlined />}
            onClick={goToAddCategory}
          >
            {t('add.category')}
          </Button>
          <FilterColumns columns={columns} setColumns={setColumns} />
        </Space>
      </Card>

      <Card title={t('shop.categories')}>
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
          scroll={{ x: true }}
          rowSelection={rowSelection}
          columns={columns?.filter((item) => item.is_show)}
          dataSource={shopCategories}
          pagination={{
            pageSize: meta.per_page,
            page: data?.page || 1,
            total: meta.total,
            defaultCurrent: data?.page,
          }}
          rowKey={(record) => record.id}
          onChange={onChangePagination}
          loading={loading}
        />
      </Card>

      <CustomModal
        click={categoryDelete}
        text={
          type ? t('set.active.category') : text ? t('delete') : t('all.delete')
        }
        setText={setId}
        loading={loadingBtn}
      />
      {restore && (
        <ResultModal
          open={restore}
          handleCancel={() => setRestore(null)}
          click={restore.restore ? shopCategoryRestoreAll : shopCategoryDropAll}
          text={restore.restore ? t('restore.modal.text') : t('read.carefully')}
          subTitle={restore.restore ? '' : t('confirm.deletion')}
          loading={loadingBtn}
          setText={setId}
        />
      )}
    </>
  );
};

export default Categories;
