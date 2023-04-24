import React, { useEffect, useState, useContext } from 'react';
import {
  CopyOutlined,
  DeleteOutlined,
  EditOutlined,
  ExpandOutlined,
  EyeOutlined,
  PlusCircleOutlined,
} from '@ant-design/icons';
import { Button, Card, Space, Table, Tabs } from 'antd';
import { useNavigate } from 'react-router-dom';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { fetchUsers } from '../../redux/slices/user';
import formatSortType from '../../helpers/formatSortType';
import { addMenu, disableRefetch, setMenuData } from '../../redux/slices/menu';
import useDidUpdate from '../../helpers/useDidUpdate';
import UserShowModal from './userShowModal';
import { useTranslation } from 'react-i18next';
import UserRoleModal from './userRoleModal';
import SearchInput from '../../components/search-input';
import FilterColumns from '../../components/filter-column';
import DeleteButton from '../../components/delete-button';
import { Context } from '../../context/context';
import { toast } from 'react-toastify';
import deliveryService from '../../services/delivery';
import CustomModal from '../../components/modal';
import ResultModal from '../../components/result-modal';
import { FaTrashRestoreAlt } from 'react-icons/fa';
import userService from '../../services/user';
import useDemo from '../../helpers/useDemo';
const { TabPane } = Tabs;

const roles = [
  'admin',
  'seller',
  'moderator',
  'manager',
  'deliveryman',
  'deleted_at',
];

export default function Admin() {
  const { t } = useTranslation();
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const [id, setId] = useState(null);
  const [text, setText] = useState(null);
  const { setIsModalVisible } = useContext(Context);
  const [loadingBtn, setLoadingBtn] = useState(false);
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const { users, loading, meta, params } = useSelector(
    (state) => state.user,
    shallowEqual
  );
  const [uuid, setUuid] = useState(null);
  const [userRole, setUserRole] = useState(null);
  const [role, setRole] = useState('admin');
  const [restore, setRestore] = useState(null);
  const immutable = activeMenu.data?.role || role;
  const {
    isDemo,
    demoDeliveryman,
    demoSeller,
    demoAdmin,
    demoModerator,
    demoMeneger,
  } = useDemo();

  const data = activeMenu.data;
  const paramsData = {
    sort: data?.sort,
    column: data?.column,
    role: immutable,
    perPage: data?.perPage,
    page: data?.page,
    search: data?.search,
  };

  const goToEdit = (row) => {
    dispatch(
      addMenu({
        url: `user/${row.uuid}`,
        id: 'user_edit',
        name: 'User edit',
      })
    );
    navigate(`/user/${row.uuid}`);
  };

  const goToClone = (row) => {
    dispatch(
      addMenu({
        url: `user-clone/${row.uuid}`,
        id: 'user-clone',
        name: 'user.clone',
      })
    );
    navigate(`/user-clone/${row.uuid}`);
  };

  const goToDetail = (row) => {
    dispatch(
      addMenu({
        url: `/users/user/${row.uuid}`,
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
      sorter: true,
      is_show: true,
    },
    {
      title: t('firstname'),
      dataIndex: 'firstname',
      is_show: true,
    },
    {
      title: t('lastname'),
      dataIndex: 'lastname',
      is_show: true,
    },
    {
      title: t('email'),
      dataIndex: 'email',
      is_show: true,
      render: (email) => <div>{isDemo ? '' : email}</div>,
    },
    {
      title: t('role'),
      dataIndex: 'role',
      is_show: true,
    },
    {
      title: t('options'),
      dataIndex: 'options',
      is_show: true,
      render: (_, row) => {
        return (
          <Space>
            <Button icon={<EyeOutlined />} onClick={() => goToDetail(row)} />
            <Button
              icon={<ExpandOutlined />}
              onClick={() => setUuid(row.uuid)}
            />
            <Button
              type='primary'
              icon={<EditOutlined />}
              onClick={() => goToEdit(row)}
              disabled={
                (isDemo && row?.id == demoDeliveryman) ||
                (isDemo && row?.id == demoModerator) ||
                (isDemo && row?.id == demoMeneger) ||
                (isDemo && row?.id == demoSeller) ||
                (isDemo && row?.id === demoAdmin)
              }
            />
            {row?.role !== 'admin' && (
              <Space>
                <Button
                  icon={<CopyOutlined />}
                  onClick={() => goToClone(row)}
                />
                <DeleteButton
                  icon={<DeleteOutlined />}
                  onClick={() => {
                    setId([row.id]);
                    setIsModalVisible(true);
                    setText(true);
                  }}
                />
              </Space>
            )}
          </Space>
        );
      },
    },
  ]);

  const goToAdduser = (e) => {
    dispatch(
      addMenu({
        id: 'user-add-role',
        url: `user/add/${e}`,
        name: t(`add.${e}`),
      })
    );
    navigate(`/user/add/${e}`);
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
        dispatch(fetchUsers(paramsData));
        setIsModalVisible(false);
        setText([]);
      })
      .finally(() => setLoadingBtn(false));
  };

  const userDropAll = () => {
    setLoadingBtn(true);
    userService
      .dropAll()
      .then(() => {
        toast.success(t('successfully.deleted'));
        dispatch(fetchUsers());
        setRestore(null);
      })
      .finally(() => setLoadingBtn(false));
  };

  const userRestoreAll = () => {
    setLoadingBtn(true);
    userService
      .restoreAll()
      .then(() => {
        toast.success(t('successfully.restored'));
        dispatch(fetchUsers(paramsData));
        setRestore(null);
      })
      .finally(() => setLoadingBtn(false));
  };

  useEffect(() => {
    if (activeMenu.refetch) {
      dispatch(fetchUsers(paramsData));
      dispatch(disableRefetch(activeMenu));
    }
  }, [activeMenu.refetch]);

  useDidUpdate(() => {
    dispatch(fetchUsers(paramsData));
  }, [activeMenu.data]);

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
      title={t('users')}
      extra={
        <Space wrap>
          {activeMenu.data?.role !== 'deleted_at' ? (
            <Space>
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
      <div className='d-flex justify-content-between mb-3'>
        <SearchInput
          placeholder={t('search')}
          className='w-25'
          handleChange={(e) => {
            handleFilter({ search: e });
          }}
          defaultValue={activeMenu.data?.search}
          resetSearch={!activeMenu.data?.search}
        />
      </div>
      <Tabs
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
      {immutable != 'admin' &&
      immutable != 'seller' &&
      immutable != 'deleted_at' ? (
        <div style={{ display: 'flex', justifyContent: 'flex-end' }}>
          <Button
            type='primary'
            icon={<PlusCircleOutlined />}
            onClick={() => goToAdduser(immutable)}
            className='mr-2'
          >
            {t(`add.${immutable}`)}
          </Button>
        </div>
      ) : null}
      <Table
        scroll={{ x: true }}
        rowSelection={rowSelection}
        columns={columns?.filter((item) => item.is_show)}
        dataSource={users}
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
          click={restore.restore ? userRestoreAll : userDropAll}
          text={restore.restore ? t('restore.modal.text') : t('read.carefully')}
          subTitle={restore.restore ? '' : t('confirm.deletion')}
          loading={loadingBtn}
          setText={setText}
        />
      )}
    </Card>
  );
}
