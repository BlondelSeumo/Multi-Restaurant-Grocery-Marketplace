import React, { useState, useEffect } from 'react';
import { Button, Space, Table, Image, Card } from 'antd';
import {
  DeleteOutlined,
  EditOutlined,
  PlusCircleOutlined,
  PlusOutlined,
} from '@ant-design/icons';
import { toast } from 'react-toastify';
import { useTranslation } from 'react-i18next';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { fetchExtraValues } from '../../../redux/slices/extraValue';
import extraService from '../../../services/extra';
import ExtraValueModal from './extra-value-modal';
import ExtraDeleteModal from './extra-delete-modal';
import DeleteButton from '../../../components/delete-button';
import { IMG_URL } from '../../../configs/app-global';
import { disableRefetch, setMenuData } from '../../../redux/slices/menu';
import FilterColumns from '../../../components/filter-column';
import { FaTrashRestoreAlt } from 'react-icons/fa';
import ResultModal from '../../../components/result-modal';

export default function ExtraValue() {
  const { t } = useTranslation();
  const dispatch = useDispatch();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const { extraGroups } = useSelector(
    (state) => state.extraGroup,
    shallowEqual
  );
  const { extraValues, loading } = useSelector(
    (state) => state.extraValue,
    shallowEqual
  );

  const [id, setId] = useState(null);
  const [modal, setModal] = useState(null);
  const [loadingBtn, setLoadingBtn] = useState(false);
  const [restore, setRestore] = useState(null);
  const [columns, setColumns] = useState([
    {
      title: t('id'),
      dataIndex: 'id',
      key: 'id',
      is_show: true,
    },
    {
      title: t('title'),
      dataIndex: 'extra_group_id',
      key: 'extra_group_id',
      is_show: true,
      render: (id) =>
        extraGroups?.find((item) => item.id === id)?.translation?.title,
    },
    {
      title: t('value'),
      dataIndex: 'value',
      key: 'value',
      is_show: true,
      render: (value, row) => (
        <Space className='extras'>
          {row.group.type === 'color' ? (
            <div
              className='extra-color-wrapper-contain'
              style={{ backgroundColor: row.value }}
            />
          ) : null}
          {row.group.type === 'image' ? (
            <Image
              width={100}
              src={IMG_URL + row.value}
              className='borderRadius'
            />
          ) : null}
          {row.group.type === 'image' ? null : <span>{row.value}</span>}
        </Space>
      ),
    },
    {
      title: t('options'),
      is_show: true,
      render: (record) => (
        <Space>
          <Button
            type='primary'
            icon={<EditOutlined />}
            onClick={() => setModal(record)}
          />
          <DeleteButton
            type='primary'
            danger
            icon={<DeleteOutlined />}
            onClick={() => setId([record.id])}
          />
        </Space>
      ),
    },
  ]);

  const handleCancel = () => setModal(null);

  const deleteExtra = () => {
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
      .deleteValue(params)
      .then(() => {
        toast.success(t('successfully.deleted'));
        setId(null);
        dispatch(fetchExtraValues());
      })
      .finally(() => setLoadingBtn(false));
  };

  const extraValueDropAll = () => {
    setLoadingBtn(true);
    extraService
      .dropAllValue()
      .then(() => {
        toast.success(t('successfully.deleted'));
        dispatch(fetchExtraValues());
        setRestore(null);
      })
      .finally(() => setLoadingBtn(false));
  };

  const extraValueRestoreAll = () => {
    setLoadingBtn(true);
    extraService
      .restoreAllValue()
      .then(() => {
        toast.success(t('successfully.restored'));
        dispatch(fetchExtraValues());
        setRestore(null);
      })
      .finally(() => setLoadingBtn(false));
  };

  useEffect(() => {
    if (activeMenu.refetch) {
      dispatch(fetchExtraValues());
      dispatch(disableRefetch(activeMenu));
    }
  }, [activeMenu.refetch]);

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
      title={t('extra.value')}
      extra={
        <Space wrap>
          <Button
            type='primary'
            icon={<PlusCircleOutlined />}
            onClick={() => setModal({})}
          >
            {t('add.extra')}
          </Button>

          {activeMenu.data?.role !== 'deleted_at' && (
            <DeleteButton size='' onClick={() => setRestore({ delete: true })}>
              {t('delete.all')}
            </DeleteButton>
          )}
          {activeMenu.data?.role === 'deleted_at' && (
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
      <Table
        scroll={{ x: true }}
        loading={loading}
        columns={columns?.filter((item) => item.is_show)}
        dataSource={extraValues}
        rowKey={(record) => record.id}
        pagination={false}
      />
      {modal && <ExtraValueModal modal={modal} handleCancel={handleCancel} />}
      {id && (
        <ExtraDeleteModal
          id={id}
          click={deleteExtra}
          text={t('delete.extra')}
          loading={loadingBtn}
          handleClose={() => setId(null)}
        />
      )}

      {restore && (
        <ResultModal
          open={restore}
          handleCancel={() => setRestore(null)}
          click={restore.restore ? extraValueRestoreAll : extraValueDropAll}
          text={restore.restore ? t('restore.modal.text') : t('read.carefully')}
          subTitle={restore.restore ? '' : t('confirm.deletion')}
          loading={loadingBtn}
          setText={setId}
        />
      )}
    </Card>
  );
}
