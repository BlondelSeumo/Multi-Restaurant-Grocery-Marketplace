import { Card, Select, Table, Tag } from 'antd';
import React, { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { shallowEqual, useSelector } from 'react-redux';
import numberToPrice from '../../helpers/numberToPrice';
import refundService from '../../services/refund';

const UserRefunds = ({ id }) => {
  const { t } = useTranslation();
  const [data, setData] = useState({});
  const [loading, setLoading] = useState(false);
  const { defaultCurrency } = useSelector(
    (state) => state.currency,
    shallowEqual
  );
  const fetchRefunds = (params) => {
    setLoading(true);
    refundService
      .getAll({ user_uuid: id, ...params })
      .then((res) => {
        setData(res);
      })
      .finally(() => {
        setLoading(false);
      });
  };

  useEffect(() => {
    fetchRefunds();
  }, []);
 
  return (
    <Card
      title={t('order.refunds')}
      extra={
        <Select
          options={[
            { label: t('pending'), value: 'pending' },
            { label: t('accepted'), value: 'accepted' },
            { label: t('canceled'), value: 'canceled' },
          ]}
          onChange={(value) => fetchRefunds({ status: value })}
          placeholder={t('status')}
          allowClear
        />
      }
    >
      <Table
        loading={loading}
        dataSource={data?.data}
        pagination={{
          total: data?.meta?.total,
          current: data?.meta?.current_page,
          pageSize: data?.meta?.per_page,
        }}
        columns={[
          {
            title: t('cause'),
            dataIndex: 'cause',
            key: 'cause',
          },
          {
            title: t('answer'),
            dataIndex: 'answer',
            key: 'answer',
          },
          {
            title: t('total.price'),
            dataIndex: 'total_price',
            key: 'total_price',
            render: (_, row) => numberToPrice(row.order?.total_price, defaultCurrency?.symbol),
          },
          {
            title: t('status'),
            dataIndex: 'status',
            key: 'status',
            render: (status) => <Tag>{status}</Tag>,
          },
          {
            title: t('created.date.&.time'),
            dataIndex: 'created_at',
            key: 'created_at',
          },
        ]}
        onChange={(pagination) =>
          fetchRefunds({
            page: pagination.current,
            perPage: pagination.pageSize,
          })
        }
      />
    </Card>
  );
};

export default UserRefunds;
