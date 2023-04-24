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
import { fetchSelletExtraValue } from '../../../../redux/slices/extraValue';
import extraService from '../../../../services/seller/extras';
import ExtraValueModal from './extra-value-modal';
import ExtraDeleteModal from './extra-delete-modal';
import DeleteButton from '../../../../components/delete-button';
import { IMG_URL } from '../../../../configs/app-global';
import { disableRefetch } from '../../../../redux/slices/menu';
import FilterColumns from '../../../../components/filter-column';

export default function SellerExtraValue() {
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
      render: (_, row) => row.group?.translation?.title,
    },
    {
      title: t('value'),
      dataIndex: 'value',
      key: 'value',
      is_show: true,
      render: (_, row) => (
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
        dispatch(fetchSelletExtraValue());
      })
      .finally(() => setLoadingBtn(false));
  };

  useEffect(() => {
    if (activeMenu.refetch) {
      dispatch(fetchSelletExtraValue());
      dispatch(disableRefetch(activeMenu));
    }
  }, [activeMenu.refetch]);

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
    </Card>
  );
}
