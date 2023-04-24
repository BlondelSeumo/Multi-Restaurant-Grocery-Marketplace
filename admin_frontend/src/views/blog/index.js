import React, { useContext, useEffect, useState } from 'react';
import {
  CloudUploadOutlined,
  CopyOutlined,
  DeleteOutlined,
  EditOutlined,
  PlusCircleOutlined,
} from '@ant-design/icons';
import { Button, Card, Image, Space, Switch, Table, Tabs, Tooltip } from 'antd';
import { toast } from 'react-toastify';
import CustomModal from '../../components/modal';
import { Context } from '../../context/context';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { useNavigate } from 'react-router-dom';
import { addMenu, disableRefetch, setMenuData } from '../../redux/slices/menu';
import { fetchBlogs } from '../../redux/slices/blog';
import useDidUpdate from '../../helpers/useDidUpdate';
import formatSortType from '../../helpers/formatSortType';
import blogService from '../../services/blog';
import { useTranslation } from 'react-i18next';
import DeleteButton from '../../components/delete-button';
import FilterColumns from '../../components/filter-column';
import { IMG_URL } from '../../configs/app-global';

const roles = ['published', 'deleted_at'];
const { TabPane } = Tabs;

export default function Blogs() {
  const { t } = useTranslation();
  const dispatch = useDispatch();
  const navigate = useNavigate();

  const goToEdit = (row) => {
    dispatch(
      addMenu({
        url: `blog/${row.uuid}`,
        id: 'blog_edit',
        name: t('edit.blog'),
      })
    );
    navigate(`/blog/${row.uuid}`);
  };

  const goToClone = (row) => {
    dispatch(
      addMenu({
        url: `blog/clone/${row.uuid}`,
        id: 'blog_clone',
        name: t('clone.blog'),
      })
    );
    navigate(`/blog/clone/${row.uuid}`);
  };

  const [columns, setColumns] = useState([
    {
      title: t('id'),
      dataIndex: 'id',
      key: 'id',
      sorter: true,
      is_show: true,
    },
    {
      title: t('title'),
      dataIndex: 'translation',
      key: 'translation',
      render: (translation) => translation?.title,
      is_show: true,
    },
    {
      title: t('image'),
      dataIndex: 'img',
      render: (img, row) => {
        return (
          <Image
            width={150}
            height={100}
            src={!row.deleted_at ? IMG_URL + img : 'https://fakeimg.pl/640x360'}
            placeholder
            className='rounded'
            style={{ objectFit: 'contain' }}
          />
        );
      },
      is_show: true,
    },
    {
      title: t('created.at'),
      dataIndex: 'created_at',
      key: 'created_at',
      is_show: true,
    },
    {
      title: t('published.at'),
      dataIndex: 'published_at',
      key: 'published_at',
      is_show: true,
    },
    {
      title: t('active'),
      dataIndex: 'active',
      key: 'active',
      render: (active, row) => (
        <Switch
          disabled={row.deleted_at}
          checked={active}
          onChange={() => {
            setId(row.uuid);
            setIsDelete(false);
            setIsPublish(false);
            setIsModalVisible(true);
          }}
        />
      ),
      is_show: true,
    },
    {
      title: t('options'),
      key: 'options',
      dataIndex: 'options',
      render: (_, row) => {
        return (
          <Space>
            <Tooltip title={t('publish')}>
              <Button
                disabled={row.deleted_at}
                icon={<CloudUploadOutlined />}
                onClick={() => {
                  setId(row.uuid);
                  setIsDelete(false);
                  setIsPublish(true);
                  setIsModalVisible(true);
                }}
              />
            </Tooltip>
            <Button
              disabled={row.deleted_at}
              type='primary'
              icon={<EditOutlined />}
              onClick={() => goToEdit(row)}
            />
            <Button
              icon={<CopyOutlined />}
              onClick={() => goToClone(row)}
              disabled={row.deleted_at}
            />
            <DeleteButton
              disabled={row.deleted_at}
              icon={<DeleteOutlined />}
              onClick={() => {
                setId([row.id]);
                setIsDelete(true);
                setIsPublish(false);
                setIsModalVisible(true);
              }}
            />
          </Space>
        );
      },
      is_show: true,
    },
  ]);

  const { setIsModalVisible } = useContext(Context);
  const [id, setId] = useState(null);
  const [isDelete, setIsDelete] = useState(false);
  const [isPublish, setIsPublish] = useState(false);
  const [loadingBtn, setLoadingBtn] = useState(false);
  const [role, setRole] = useState('published');
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const immutable = activeMenu.data?.role || role;
  const { blogs, meta, loading, params } = useSelector(
    (state) => state.blog,
    shallowEqual
  );
  const data = activeMenu.data;
  const paramsData = {
    sort: data?.sort,
    column: data?.column,
    perPage: data?.perPage,
    page: data?.page,
    status: immutable === 'deleted_at' ? undefined : immutable,
    deleted_at: immutable === 'deleted_at' ? immutable : undefined,
  };

  const blogDelete = () => {
    setLoadingBtn(true);
    const params = {
      ...Object.assign(
        {},
        ...id.map((item, index) => ({
          [`ids[${index}]`]: item,
        }))
      ),
    };

    blogService
      .delete(params)
      .then(() => {
        toast.success(t('successfully.deleted'));
        dispatch(fetchBlogs());
        setIsModalVisible(false);
      })
      .finally(() => setLoadingBtn(false));
  };

  const blogSetActive = () => {
    setLoadingBtn(true);
    blogService
      .setActive(id)
      .then(() => {
        toast.success(t('successfully.updated'));
        dispatch(fetchBlogs());
        setIsModalVisible(false);
      })
      .finally(() => setLoadingBtn(false));
  };

  const blogPublish = () => {
    setLoadingBtn(true);
    blogService
      .publish(id)
      .then(() => {
        toast.success(t('successfully.updated'));
        dispatch(fetchBlogs());
        setIsModalVisible(false);
      })
      .finally(() => setLoadingBtn(false));
  };

  useEffect(() => {
    if (activeMenu.refetch) {
      dispatch(fetchBlogs(paramsData));
      dispatch(disableRefetch(activeMenu));
    }
  }, [activeMenu.refetch]);

  useDidUpdate(() => {
    dispatch(fetchBlogs(paramsData));
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

  const goToAddBlog = () => {
    dispatch(
      addMenu({
        id: 'blogs',
        url: 'blog/add',
        name: t('add.blog'),
      })
    );
    navigate('/blog/add');
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

  return (
    <Card
      title={t('blogs')}
      extra={
        <Space wrap>
          <Button
            type='primary'
            icon={<PlusCircleOutlined />}
            onClick={goToAddBlog}
          >
            {t('add.banner')}
          </Button>

          <DeleteButton size='' onClick={allDelete}>
            {t('delete.selected')}
          </DeleteButton>

          <FilterColumns setColumns={setColumns} columns={columns} />
        </Space>
      }
    >
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
        columns={columns.filter((column) => column.is_show)}
        dataSource={blogs}
        pagination={{
          pageSize: params.perPage,
          page: activeMenu.data?.page || 1,
          total: meta.total,
          defaultCurrent: activeMenu.data?.page,
          current: activeMenu.data?.page,
        }}
        rowKey={(record) => record.id}
        onChange={onChangePagination}
        loading={loading}
      />
      <CustomModal
        click={isPublish ? blogPublish : isDelete ? blogDelete : blogSetActive}
        text={
          isPublish
            ? t('publish.blog')
            : isDelete
            ? t('delete.blog')
            : t('set.active.blog')
        }
        loading={loadingBtn}
      />
    </Card>
  );
}
