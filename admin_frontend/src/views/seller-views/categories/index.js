import React, { useContext, useEffect, useState } from 'react';
import { Card, Image, Table, Button, Space, Tag } from 'antd';
import { useTranslation } from 'react-i18next';
import getImage from '../../../helpers/getImage';
import CreateCategory from './createCategory';
import { EditOutlined, PlusCircleOutlined } from '@ant-design/icons';
import { fetchSellerCategory } from '../../../redux/slices/category';
import {
  addMenu,
  disableRefetch,
  setMenuData,
} from '../../../redux/slices/menu';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { toast } from 'react-toastify';
import CustomModal from '../../../components/modal';
import { Context } from '../../../context/context';
import sellerCategory from '../../../services/seller/category';
import { useNavigate } from 'react-router-dom';
import FilterColumns from '../../../components/filter-column';
import formatSortType from '../../../helpers/formatSortType';
import useDidUpdate from '../../../helpers/useDidUpdate';
import SearchInput from '../../../components/search-input';
const colors = ['blue', 'red', 'gold', 'volcano', 'cyan', 'lime'];

const body = {
  type: 'main',
};

export default function SellerCategories() {
  const { t } = useTranslation();
  const [isModalOpen, setIsModalOpen] = useState(false);
  const { setIsModalVisible } = useContext(Context);
  const [loadingBtn, setLoadingBtn] = useState(false);
  const [id, setId] = useState(null);
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const [text, setText] = useState(null);
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const { categories, meta, loading, params } = useSelector(
    (state) => state.category,
    shallowEqual
  );
  const data = activeMenu.data;

  const paramsData = {
    search: data?.search,
    sort: data?.sort,
    column: data?.column,
    perPage: data?.perPage,
    page: data?.page,
  };

  const goToEdit = (row) => {
    dispatch(
      addMenu({
        url: `seller/category/${row.uuid}`,
        id: 'category_edit',
        name: t('category'),
      })
    );
    navigate(`/seller/category/${row.uuid}`);
  };

  const [columns, setColumns] = useState([
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
            src={getImage(img)}
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
            />
          </Space>
        );
      },
    },
  ]);

  useEffect(() => {
    if (activeMenu.refetch) {
      dispatch(fetchSellerCategory(paramsData));
      dispatch(disableRefetch(activeMenu));
    }
  }, [activeMenu.refetch]);

  useDidUpdate(() => {
    dispatch(fetchSellerCategory(paramsData));
  }, [activeMenu.data]);

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
    sellerCategory
      .delete(params)
      .then(() => {
        toast.success(t('successfully.deleted'));
        dispatch(fetchSellerCategory(body));
        setIsModalVisible(false);
      })
      .finally(() => setLoadingBtn(false));
  };

  const handleCancel = () => setIsModalOpen(false);

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

  const goToAddCategory = () => {
    dispatch(
      addMenu({
        url: `seller/category/add`,
        id: 'seller/category/add',
        name: t('edit.category'),
      })
    );
    navigate(`/seller/category/add`);
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
    <Card
      title={t('categories')}
      extra={
        <Space wrap>
          <SearchInput
            placeholder={t('search')}
            handleChange={(e) => handleFilter({ search: e })}
            defaultValue={activeMenu.data?.search}
            resetSearch={!activeMenu.data?.search}
          />
          <Button
            size='small'
            type='primary'
            icon={<PlusCircleOutlined />}
            onClick={goToAddCategory}
          >
            {t('add.category')}
          </Button>
          <FilterColumns columns={columns} setColumns={setColumns} />
        </Space>
      }
    >
      <Table
        scroll={{ x: true }}
        rowSelection={rowSelection}
        columns={columns?.filter((item) => item.is_show)}
        dataSource={categories}
        pagination={{
          pageSize: params.perPage,
          page: activeMenu.data?.page || 1,
          total: meta.total,
          defaultCurrent: activeMenu.data?.page,
          current: activeMenu.data?.page,
        }}
        rowKey={(record) => record.key}
        onChange={onChangePagination}
        loading={loading}
      />
      {isModalOpen && (
        <CreateCategory handleCancel={handleCancel} isModalOpen={isModalOpen} />
      )}
      <CustomModal
        click={categoryDelete}
        text={text ? t('delete') : t('all.delete')}
        setText={setId}
        loading={loadingBtn}
      />
    </Card>
  );
}
