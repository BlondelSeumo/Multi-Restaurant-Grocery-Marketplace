import React, { useState } from 'react';
import { Button, Col, Form, Modal, Row, Select } from 'antd';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { useTranslation } from 'react-i18next';
import productService from '../../../services/seller/product';
import { setRefetch } from '../../../redux/slices/menu';
import { fetchAddons } from '../../../redux/slices/addons';

const allStatuses = ['published', 'pending', 'unpublished'];

export default function ProductStatusModal({
  orderDetails: data,
  handleCancel,
}) {
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const [form] = Form.useForm();
  const dispatch = useDispatch();
  const { t } = useTranslation();
  const [loading, setLoading] = useState(false);

  const onFinish = (values) => {
    setLoading(true);
    const params = { ...values };
    productService
      .updateStatus(data.uuid, params)
      .then(() => {
        handleCancel();
        const data = activeMenu.data;
        const paramsData = {
          status:
            data?.role === 'deleted_at' ? null : data?.role || 'published',
          deleted_at: data?.role === 'deleted_at' ? data.role : null,
          perPage: data?.perPage,
          page: data?.page,
        };
        dispatch(fetchAddons(paramsData));
        dispatch(setRefetch(activeMenu));
      })
      .finally(() => setLoading(false));
  };

  return (
    <Modal
      visible={!!data}
      title={data.title}
      onCancel={handleCancel}
      footer={[
        <Button type='primary' onClick={() => form.submit()} loading={loading}>
          {t('save')}
        </Button>,
        <Button type='default' onClick={handleCancel}>
          {t('cancel')}
        </Button>,
      ]}
    >
      <Form
        form={form}
        layout='vertical'
        onFinish={onFinish}
        initialValues={{ status: data.status }}
      >
        <Row gutter={12}>
          <Col span={24}>
            <Form.Item
              label={t('status')}
              name='status'
              rules={[
                {
                  required: true,
                  message: t('required'),
                },
              ]}
            >
              <Select>
                {allStatuses.map((item, idx) => (
                  <Select.Option key={idx} value={item}>
                    {t(item)}
                  </Select.Option>
                ))}
              </Select>
            </Form.Item>
          </Col>
        </Row>
      </Form>
    </Modal>
  );
}
