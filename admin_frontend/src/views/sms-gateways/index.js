import React, { useContext, useEffect, useState } from 'react';
import { Button, Card, Space, Switch, Table } from 'antd';
import { EditOutlined } from '@ant-design/icons';
import { Context } from '../../context/context';
import SmsEditModal from './sms-edit-modal';
import { useTranslation } from 'react-i18next';
import { disableRefetch } from '../../redux/slices/menu';
import { shallowEqual, useDispatch } from 'react-redux';
import { useSelector } from 'react-redux';
import { fetchSms } from '../../redux/slices/sms-geteways';
import SmsAddModal from './sms-add-modal';

export default function SmsGateways() {
  const { t } = useTranslation();
  const [addSMS, setAddSMS] = useState(null);
  const [editSMS, setEditSMS] = useState(null);
  const { setIsModalVisible } = useContext(Context);
  const dispatch = useDispatch();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const { smsGatewaysList, loading } = useSelector(
    (state) => state.sms,
    shallowEqual
  );

  const columns = [
    {
      title: t('id'),
      dataIndex: 'id',
    },
    {
      title: t('title'),
      dataIndex: 'title',
    },
    {
      title: t('from'),
      dataIndex: 'from',
    },
    {
      title: t('type'),
      dataIndex: 'type',
    },
    {
      title: t('active'),
      dataIndex: 'active',
      key: 'active',
      render: (active) => {
        return (
          <Switch
            onChange={() => {
              setIsModalVisible(true);
            }}
            checked={active}
          />
        );
      },
    },
    {
      title: t('options'),
      dataIndex: 'options',
      render: (_, row) => (
        <Button
          type='primary'
          icon={<EditOutlined />}
          onClick={() => setEditSMS(row)}
        />
      ),
    },
  ];

  useEffect(() => {
    if (activeMenu.refetch) {
      dispatch(fetchSms());
      dispatch(disableRefetch(activeMenu));
    }
  }, [activeMenu.refetch]);

  return (
    <Card
      title={t('sms.gateway')}
      extra={
        <Space>
          <Button onClick={(e) => setAddSMS(true)} type='primary'>
            {t('add.sms.gateway')}
          </Button>
        </Space>
      }
    >
      <Table
        scroll={{ x: true }}
        columns={columns}
        rowKey={(record) => record.id}
        dataSource={smsGatewaysList}
        pagination={false}
        loading={loading}
      />
      {addSMS && (
        <SmsAddModal
          modal={addSMS}
          handleCancel={() => setAddSMS(null)}
          refetch={fetchSms}
        />
      )}
      {editSMS && (
        <SmsEditModal
          modal={editSMS}
          handleCancel={() => setEditSMS(null)}
          refetch={fetchSms}
        />
      )}
    </Card>
  );
}
