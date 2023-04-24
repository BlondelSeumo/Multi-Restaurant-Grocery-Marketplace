import React, { useEffect, useState, useContext } from 'react';
import { Button, Card, Space, Table } from 'antd';
import {
  DeleteOutlined,
  EditOutlined,
  EyeOutlined,
  PlusCircleOutlined,
} from '@ant-design/icons';
import { toast } from 'react-toastify';
import { useTranslation } from 'react-i18next';
import extraService from '../../../services/extra';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { fetchExtraGroups } from '../../../redux/slices/extraGroup';
import { disableRefetch, setMenuData } from '../../../redux/slices/menu';
import ExtraGroupModal from './extra-group-modal';
import DeleteButton from '../../../components/delete-button';
import ExtraGroupShowModal from './extra-group-show-modal';
import FilterColumns from '../../../components/filter-column';
import CustomModal from '../../../components/modal';
import { Context } from '../../../context/context';
import ResultModal from '../../../components/result-modal';
import SearchInput from '../../../components/search-input';
import useDidUpdate from '../../../helpers/useDidUpdate';

export default function ExtraGroup() {
  const { t } = useTranslation();
  const dispatch = useDispatch();
  const { setIsModalVisible } = useContext(Context);
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const { extraGroups, loading } = useSelector(
    (state) => state.extraGroup,
    shallowEqual
  );

  const [id, setId] = useState(null);
  const [show, setShow] = useState(null);
  const [modal, setModal] = useState(null);
  const [loadingBtn, setLoadingBtn] = useState(false);
  const [text, setText] = useState(null);
  const [restore, setRestore] = useState(null);
  const data = activeMenu.data;
  const paramsData = {
    search: data?.search,
    column: data?.column,
    perPage: data?.perPage,
    sort: data?.sort,
    page: data?.page,
  };

  const [columns, setColumns] = useState([
    {
      title: t('id'),
      dataIndex: 'id',
      key: 'id',
      is_show: true,
    },
    {
      title: t('title'),
      dataIndex: 'translation',
      key: 'translation',
      is_show: true,
      render: (translation) => translation?.title,
    },
    {
      title: t('type'),
      dataIndex: 'type',
      key: 'type',
      is_show: true,
    },
    {
      title: t('options'),
      is_show: true,
      render: (record) => (
        <Space>
          <Button
            type='primary'
            icon={<EyeOutlined />}
            onClick={() => setShow(record.id)}
          />
          <Button
            type='primary'
            icon={<EditOutlined />}
            onClick={() => setModal(record)}
          />
          <DeleteButton
            type='primary'
            danger
            icon={<DeleteOutlined />}
            onClick={() => {
              setIsModalVisible(true);
              setId([record.id]);
              setText(true);
            }}
          />
        </Space>
      ),
    },
  ]);

  const handleCancel = () => {
    setShow(null);
    setModal(null);
  };

  const onDeleteExtra = () => {
    setLoadingBtn(true);
    const params = {
      ...Object.assign(
        {},
        ...id.map((item, index) => ({
          [`ids[${index}]`]: item,
        }))
      ),
    };
    extraService
      .deleteGroup(params)
      .then(() => {
        setIsModalVisible(false);
        toast.success(t('successfully.deleted'));
        setId(null);
        dispatch(fetchExtraGroups());
      })
      .finally(() => setLoadingBtn(false));
  };

  const extraGroupDropAll = () => {
    setLoadingBtn(true);
    extraService
      .dropAllGroup()
      .then(() => {
        toast.success(t('successfully.deleted'));
        dispatch(fetchExtraGroups());
        setRestore(null);
      })
      .finally(() => setLoadingBtn(false));
  };

  const extraGroupRestoreAll = () => {
    setLoadingBtn(true);
    extraService
      .restoreAllGroup()
      .then(() => {
        toast.success(t('successfully.restored'));
        dispatch(fetchExtraGroups());
        setRestore(null);
      })
      .finally(() => setLoadingBtn(false));
  };

  useDidUpdate(() => {
    dispatch(fetchExtraGroups(paramsData));
  }, [activeMenu.data]);

  useEffect(() => {
    if (activeMenu.refetch) {
      dispatch(fetchExtraGroups(paramsData));
      dispatch(disableRefetch(activeMenu));
    }
  }, [activeMenu.refetch]);

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

  const handleFilter = (item, name) => {
    dispatch(
      setMenuData({
        activeMenu,
        data: { ...activeMenu.data, [name]: item },
      })
    );
  };

  return (
    <Card
      title={t('extra.group')}
      extra={
        <Space wrap>
          <Button
            type='primary'
            icon={<PlusCircleOutlined />}
            onClick={() => setModal({})}
          >
            {t('add.extra')}
          </Button>
          <DeleteButton size='' onClick={allDelete}>
            {t('delete.selected')}
          </DeleteButton>

          <FilterColumns columns={columns} setColumns={setColumns} />
        </Space>
      }
    >
      <div className='d-flex justify-content-between'>
        <SearchInput
          placeholder={t('search')}
          handleChange={(search) => handleFilter(search, 'search')}
          defaultValue={activeMenu.data?.search}
          resetSearch={!activeMenu.data?.search}
          className={'w-25'}
        />
      </div>

      <Table
        scroll={{ x: true }}
        rowSelection={rowSelection}
        loading={loading}
        columns={columns?.filter((item) => item.is_show)}
        dataSource={extraGroups}
        rowKey={(record) => record.id}
        pagination={false}
      />
      {modal && <ExtraGroupModal modal={modal} handleCancel={handleCancel} />}
      <CustomModal
        click={onDeleteExtra}
        text={text ? t('delete') : t('all.delete')}
        loading={loadingBtn}
        setText={setId}
      />
      {show && <ExtraGroupShowModal open={show} handleClose={handleCancel} />}
      {restore && (
        <ResultModal
          open={restore}
          handleCancel={() => setRestore(null)}
          click={restore.restore ? extraGroupRestoreAll : extraGroupDropAll}
          text={restore.restore ? t('restore.modal.text') : t('read.carefully')}
          subTitle={restore.restore ? '' : t('confirm.deletion')}
          loading={loadingBtn}
          setText={setId}
        />
      )}
    </Card>
  );
}
