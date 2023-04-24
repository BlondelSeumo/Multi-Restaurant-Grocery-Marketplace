import React, { useContext, useEffect, useState } from 'react';
import {
  CopyOutlined,
  DeleteOutlined,
  EditOutlined,
  PlusCircleOutlined,
} from '@ant-design/icons';
import { Button, Card, Image, Space, Table, Tabs, Tag } from 'antd';
import { toast } from 'react-toastify';
import { export_url, IMG_URL } from '../../configs/app-global';
import CustomModal from '../../components/modal';
import { Context } from '../../context/context';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { useNavigate } from 'react-router-dom';
import { addMenu, disableRefetch, setMenuData } from '../../redux/slices/menu';
import brandService from '../../services/brand';
import { fetchBrands } from '../../redux/slices/brand';
import { useTranslation } from 'react-i18next';
import DeleteButton from '../../components/delete-button';
import FilterColumns from '../../components/filter-column';
import SearchInput from '../../components/search-input';
import useDidUpdate from '../../helpers/useDidUpdate';
import { FaTrashRestoreAlt } from 'react-icons/fa';
import ResultModal from '../../components/result-modal';
import { CgExport, CgImport } from 'react-icons/cg';
import formatSortType from '../../helpers/formatSortType';

const roles = ['published', 'deleted_at'];
const { TabPane } = Tabs;

const Brands = () => {
  const { t } = useTranslation();
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const [downloading, setDownloading] = useState(false);
  const { setIsModalVisible } = useContext(Context);
  const [id, setId] = useState(null);
  const [loadingBtn, setLoadingBtn] = useState(false);
  const [text, setText] = useState(null);
  const [restore, setRestore] = useState(null);
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const [role, setRole] = useState('published');
  const immutable = activeMenu.data?.role || role;
  const data = activeMenu?.data;
  const paramsData = {
    sort: data?.sort,
    column: data?.column,
    search: data?.search ? data.search : undefined,
    perPage: data?.perPage,
    page: data?.page,
    status: data?.role === 'deleted_at' ? null : data?.role || 'published',
    deleted_at: data?.role === 'deleted_at' ? data?.role : null,
  };

  const { brands, meta, loading } = useSelector(
    (state) => state.brand,
    shallowEqual
  );

  const goToClone = (id) => {
    dispatch(
      addMenu({
        id: `brand-clone`,
        url: `brand-clone/${id}`,
        name: t('brand.clone'),
      })
    );
    navigate(`/brand-clone/${id}`, { state: 'clone' });
  };

  const goToEdit = (row) => {
    dispatch(
      addMenu({
        url: `brand/${row.id}`,
        id: 'brand_edit',
        name: t('edit.brand'),
      })
    );
    navigate(`/brand/${row.id}`, { state: 'edit' });
  };

  const goToAddBrand = () => {
    dispatch(
      addMenu({
        id: 'brand/add',
        url: 'brand/add',
        name: t('add.brand'),
      })
    );
    navigate('/brand/add');
  };

  const goToImport = () => {
    dispatch(
      addMenu({
        url: `catalog/brands/import`,
        id: 'brand_import',
        name: t('import.brand'),
      })
    );
    navigate(`/catalog/brands/import`);
  };

  const [columns, setColumns] = useState([
    {
      title: t('id'),
      dataIndex: 'id',
      key: 'id',
      is_show: true,
      sorter: true,
    },
    {
      title: t('title'),
      dataIndex: 'title',
      key: 'title',
      is_show: true,
    },
    {
      title: t('image'),
      dataIndex: 'img',
      key: 'img',
      is_show: true,
      render: (img, row) => {
        return (
          <Image
            src={!row.deleted_at ? IMG_URL + img : 'https://fakeimg.pl/640x360'}
            alt='img_gallery'
            width={100}
            height='auto'
            className='rounded'
            preview
            placeholder
            key={img + row.id}
          />
        );
      },
    },
    {
      title: t('active'),
      dataIndex: 'active',
      key: 'active',
      is_show: true,
      render: (active) =>
        active ? (
          <Tag color='cyan'>{t('active')}</Tag>
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
              type='primary'
              icon={<EditOutlined />}
              onClick={() => goToEdit(row)}
              disabled={row.deleted_at}
            />
            <Button
              icon={<CopyOutlined />}
              onClick={() => goToClone(row.id)}
              disabled={row.deleted_at}
            />
            <DeleteButton
              disabled={row.deleted_at}
              icon={<DeleteOutlined />}
              onClick={() => {
                setId([row.id]);
                setIsModalVisible(true);
                setText(true);
              }}
            />
          </Space>
        );
      },
    },
  ]);

  const brandDelete = () => {
    setLoadingBtn(true);
    const params = {
      ...Object.assign(
        {},
        ...id.map((item, index) => ({
          [`ids[${index}]`]: item,
        }))
      ),
    };
    brandService
      .delete(params)
      .then(() => {
        toast.success(t('successfully.deleted'));
        dispatch(fetchBrands(paramsData));
        setIsModalVisible(false);
        setText(null);
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
        data: { ...data, perPage, page, column, sort },
      })
    );
  }

  useDidUpdate(() => {
    dispatch(fetchBrands(paramsData));
  }, [activeMenu.data]);

  useEffect(() => {
    if (activeMenu.refetch) {
      dispatch(fetchBrands(paramsData));
      dispatch(disableRefetch(activeMenu));
    }
  }, [activeMenu.refetch]);

  const excelExport = () => {
    setDownloading(true);
    brandService
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

  const brandDropAll = () => {
    setLoadingBtn(true);
    brandService
      .dropAll()
      .then(() => {
        toast.success(t('successfully.deleted'));
        dispatch(fetchBrands());
        setRestore(null);
      })
      .finally(() => setLoadingBtn(false));
  };

  const brandRestoreAll = () => {
    setLoadingBtn(true);
    brandService
      .restoreAll()
      .then(() => {
        toast.success(t('successfully.deleted'));
        dispatch(fetchBrands());
        setRestore(null);
      })
      .finally(() => setLoadingBtn(false));
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

  return (
    <>
      <Card className='p-0'>
        <Space wrap>
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
          <Button onClick={goToImport}>
            <CgImport className='mr-2' />
            {t('import')}
          </Button>
          <Button loading={downloading} onClick={excelExport}>
            <CgExport className='mr-2' />
            {t('export')}
          </Button>
          {activeMenu.data?.role === 'published' ? (
            <DeleteButton size='' onClick={() => setRestore({ delete: true })}>
              {t('delete.all')}
            </DeleteButton>
          ) : (
            <DeleteButton
              size=''
              icon={<FaTrashRestoreAlt className='mr-2' />}
              onClick={() => setRestore({ restore: true })}
            >
              {t('restore.all')}
            </DeleteButton>
          )}
          <DeleteButton size='' onClick={allDelete}>
            {t('delete.selected')}
          </DeleteButton>
          <Button
            type='primary'
            icon={<PlusCircleOutlined />}
            onClick={goToAddBrand}
          >
            {t('add.brands')}
          </Button>
          <FilterColumns columns={columns} setColumns={setColumns} />
        </Space>
      </Card>

      <Card title={t('brands')}>
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
          dataSource={brands}
          pagination={{
            pageSize: meta.per_page,
            page: meta.current_page,
            total: meta.total,
          }}
          rowKey={(record) => record.id}
          onChange={onChangePagination}
          loading={loading}
        />
      </Card>
      <CustomModal
        click={brandDelete}
        text={text ? t('delete') : t('all.delete')}
        setText={setId}
        loading={loadingBtn}
      />

      {restore && (
        <ResultModal
          open={restore}
          handleCancel={() => setRestore(null)}
          click={restore.restore ? brandRestoreAll : brandDropAll}
          text={restore.restore ? t('restore.modal.text') : t('read.carefully')}
          subTitle={restore.restore ? '' : t('confirm.deletion')}
          loading={loadingBtn}
          setText={setId}
        />
      )}
    </>
  );
};

export default Brands;
