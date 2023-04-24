import React, { useEffect, useState, useContext } from 'react';
import {
  DeleteOutlined,
  EditOutlined,
  ExpandOutlined,
  EyeOutlined,
  PlusCircleOutlined,
} from '@ant-design/icons';
import { Button, Card, Space, Table, Tabs, Tooltip } from 'antd';
import { useNavigate } from 'react-router-dom';
import { FaTrashRestoreAlt, FaUserCog } from 'react-icons/fa';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { addMenu, disableRefetch, setMenuData } from '../../redux/slices/menu';
import { useTranslation } from 'react-i18next';
import formatSortType from '../../helpers/formatSortType';
import useDidUpdate from '../../helpers/useDidUpdate';
import UserShowModal from './userShowModal';
import UserRoleModal from './userRoleModal';
import { fetchClients } from '../../redux/slices/client';
import SearchInput from '../../components/search-input';
import FilterColumns from '../../components/filter-column';
import DeleteButton from '../../components/delete-button';
import { toast } from 'react-toastify';
import { Context } from '../../context/context';
import CustomModal from '../../components/modal';
import deliveryService from '../../services/delivery';
import ResultModal from '../../components/result-modal';
import userService from '../../services/user';
import useDemo from '../../helpers/useDemo';

const { TabPane } = Tabs;
const roles = ['published', 'deleted_at'];

const User = () => {
  const { t } = useTranslation();
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const [id, setId] = useState(null);
  const [text, setText] = useState(null);
  const { setIsModalVisible } = useContext(Context);
  const [loadingBtn, setLoadingBtn] = useState(false);
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const { clients, loading, meta, params } = useSelector(
    (state) => state.client,
    shallowEqual
  );
  const { isDemo } = useDemo();

  const [uuid, setUuid] = useState(null);
  const [userRole, setUserRole] = useState(null);
  const [restore, setRestore] = useState(null);
  const [role, setRole] = useState('published');
  const immutable = activeMenu.data?.role || role;
  const data = activeMenu.data;
  const paramsData = {
    sort: data?.sort,
    column: data?.column,
    perPage: data?.perPage,
    page: data?.page,
    search: data?.search,
    status: immutable === 'deleted_at' ? null : immutable || 'published',
    deleted_at: immutable === 'deleted_at' ? immutable : null,
  };

  const goToEdit = (row) => {
    dispatch(
      addMenu({
        url: `user/${row.uuid}`,
        id: 'user_edit',
        name: 'User edit',
      })
    );
    navigate(`/user/${row.uuid}`, { state: 'user' });
  };

  const goToDetail = (row) => {
    dispatch(
      addMenu({
        url: `users/user/${row.uuid}`,
        id: 'user_info',
        name: t('user.info'),
      })
    );
    navigate(`/users/user/${row.uuid}`, { state: { user_id: row.id } });
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
      title: t('firstname'),
      dataIndex: 'firstname',
      key: 'firstname',
      is_show: true,
    },
    {
      title: t('lastname'),
      dataIndex: 'lastname',
      key: 'lastname',
      is_show: true,
    },
    {
      title: t('email'),
      dataIndex: 'email',
      key: 'email',
      is_show: true,
      render: (email) => <div>{isDemo ? '' : email}</div>,
    },
    {
      title: t('role'),
      dataIndex: 'role',
      key: 'role',
      is_show: true,
    },
    {
      title: t('options'),
      dataIndex: 'options',
      key: 'options',
      is_show: true,
      render: (_, row) => {
        return (
          <Space>
            <Button
              disabled={row.deleted_at}
              icon={<EyeOutlined />}
              onClick={() => goToDetail(row)}
            />
            <Button
              icon={<ExpandOutlined />}
              onClick={() => setUuid(row.uuid)}
              disabled={row.deleted_at}
            />
            <Button
              type='primary'
              icon={<EditOutlined />}
              onClick={() => goToEdit(row)}
              disabled={row.deleted_at}
            />
            <Tooltip title={t('change.user.role')}>
              <Button
                disabled={row.deleted_at}
                onClick={() => setUserRole(row)}
                icon={<FaUserCog />}
              />
            </Tooltip>
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

  const userDelete = () => {
    setLoadingBtn(true);
    const params = {
      ...Object.assign(
        {},
        ...id.map((item, index) => ({
          [`ids[${index}]`]: item,
        }))
      ),
    };

    deliveryService
      .delete(params)
      .then(() => {
        toast.success(t('successfully.deleted'));
        dispatch(fetchClients(paramsData));
        setIsModalVisible(false);
        setText([]);
      })
      .finally(() => setLoadingBtn(false));
  };

  const clientDropAll = () => {
    setLoadingBtn(true);
    userService
      .dropAll()
      .then(() => {
        toast.success(t('successfully.deleted'));
        dispatch(fetchClients());
        setRestore(null);
      })
      .finally(() => setLoadingBtn(false));
  };

  const clientRestoreAll = () => {
    setLoadingBtn(true);
    userService
      .restoreAll()
      .then(() => {
        toast.success(t('successfully.restored'));
        dispatch(fetchClients(paramsData));
        setRestore(null);
      })
      .finally(() => setLoadingBtn(false));
  };

  useEffect(() => {
    if (activeMenu.refetch) {
      dispatch(fetchClients(paramsData));
      dispatch(disableRefetch(activeMenu));
    }
  }, [activeMenu.refetch]);

  useDidUpdate(() => {
    dispatch(fetchClients(paramsData));
  }, [activeMenu.data]);

  const goToAddClient = () => {
    dispatch(
      addMenu({
        id: 'user-add',
        url: 'user/add',
        name: t('add.client'),
      })
    );
    navigate('/user/add');
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

  return (
    <Card
      title={t('clients')}
      extra={
        <Space wrap>
          {activeMenu.data?.role !== 'deleted_at' ? (
            <Space>
              <Button
                type='primary'
                icon={<PlusCircleOutlined />}
                onClick={goToAddClient}
              >
                {t('add.client')}
              </Button>
              <DeleteButton size='' onClick={allDelete}>
                {t('delete.selected')}
              </DeleteButton>
              <DeleteButton onClick={() => setRestore({ delete: true })}>
                {t('delete.all')}
              </DeleteButton>
            </Space>
          ) : (
            <DeleteButton
              icon={<FaTrashRestoreAlt className='mr-2' />}
              onClick={() => setRestore({ restore: true })}
            >
              {t('restore.all')}
            </DeleteButton>
          )}
          <FilterColumns columns={columns} setColumns={setColumns} />
        </Space>
      }
    >
      <div className='d-flex justify-content-between'>
        <SearchInput
          placeholder={t('search')}
          className='w-25'
          handleChange={(e) => handleFilter({ search: e })}
          defaultValue={activeMenu.data?.search}
          resetSearch={!activeMenu.data?.search}
        />
      </div>

      <Tabs
        className='mt-3'
        scroll={{ x: true }}
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
        dataSource={clients}
        loading={loading}
        pagination={{
          pageSize: params.perPage,
          page: activeMenu.data?.page || 1,
          total: meta.total,
          defaultCurrent: activeMenu.data?.page,
          current: activeMenu.data?.page,
        }}
        rowKey={(record) => record.id}
        onChange={onChangePagination}
      />
      <CustomModal
        click={userDelete}
        text={text ? t('delete') : t('all.delete')}
        loading={loadingBtn}
        setText={setId}
      />
      {uuid && <UserShowModal uuid={uuid} handleCancel={() => setUuid(null)} />}
      {userRole && (
        <UserRoleModal data={userRole} handleCancel={() => setUserRole(null)} />
      )}

      {restore && (
        <ResultModal
          open={restore}
          handleCancel={() => setRestore(null)}
          click={restore.restore ? clientRestoreAll : clientDropAll}
          text={restore.restore ? t('restore.modal.text') : t('read.carefully')}
          subTitle={restore.restore ? '' : t('confirm.deletion')}
          loading={loadingBtn}
          setText={setText}
        />
      )}
    </Card>
  );
};

export default User;
