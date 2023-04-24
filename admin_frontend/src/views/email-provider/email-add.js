import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { toast } from 'react-toastify';
import { Button, Card, Col, Form, Input, InputNumber, Row, Switch } from 'antd';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { removeFromMenu, setMenuData } from '../../redux/slices/menu';
import { fetchBanners } from '../../redux/slices/banner';
import { useTranslation } from 'react-i18next';
import emailService from '../../services/emailSettings';

const EmailProviderAdd = () => {
  const { t } = useTranslation();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const dispatch = useDispatch();
  const [form] = Form.useForm();
  const navigate = useNavigate();
  const [loadingBtn, setLoadingBtn] = useState(false);

  useEffect(() => {
    return () => {
      const data = form.getFieldsValue(true);
      dispatch(setMenuData({ activeMenu, data }));
    };
  }, []);

  const onFinish = (values) => {
    const body = {
      smtp_auth: values.smtp_auth,
      smtp_debug: values.smtp_debug,
      port: values.port,
      password: values.password,
      from_to: values.from_to,
      host: values.host,
      active: values.active,
      from_site: values.from_site,
    };
    setLoadingBtn(true);
    const nextUrl = 'settings/emailProviders';
    emailService
      .create(body)
      .then(() => {
        toast.success(t('successfully.created'));
        dispatch(removeFromMenu({ ...activeMenu, nextUrl }));
        navigate(`/${nextUrl}`);
        dispatch(fetchBanners());
      })
      .finally(() => setLoadingBtn(false));
  };

  return (
    <Card title={t('add.email.provider')} className='h-100'>
      <Form
        name='email-provider-add'
        layout='vertical'
        onFinish={onFinish}
        form={form}
        initialValues={{
          smtp_debug: true,
          smtp_auth: true,
          active: true,
          ...activeMenu.data,
        }}
        className='d-flex flex-column h-100'
      >
        <Row gutter={12}>
          <Col span={12}>
            <Form.Item
              rules={[
                {
                  required: true,
                  type: 'email',
                  message: t('required.email'),
                },
              ]}
              label={t('email')}
              name='from_to'
            >
              <Input placeholder='Email' />
            </Form.Item>
          </Col>

          <Col span={12}>
            <Form.Item
              rules={[
                {
                  required: true,
                  message: t('required'),
                },
              ]}
              label={t('password')}
              name='password'
            >
              <Input.Password />
            </Form.Item>
          </Col>

          <Col span={12}>
            <Form.Item
              rules={[
                {
                  required: true,
                  message: t('required'),
                },
              ]}
              label={t('host')}
              name='host'
            >
              <Input />
            </Form.Item>
          </Col>

          <Col span={12}>
            <Form.Item
              rules={[
                {
                  required: true,
                  message: t('required'),
                },
              ]}
              label={t('port')}
              name='port'
            >
              <InputNumber className='w-100' />
            </Form.Item>
          </Col>

          <Col span={8}>
            <Form.Item
              label={t('active')}
              name='active'
              valuePropName='checked'
            >
              <Switch />
            </Form.Item>
          </Col>

          <Col span={8}>
            <Form.Item
              valuePropName='checked'
              label={t('smtp_debug')}
              name='smtp_debug'
            >
              <Switch />
            </Form.Item>
          </Col>

          <Col span={8}>
            <Form.Item
              valuePropName='checked'
              label={t('smtp_auth')}
              name='smtp_auth'
            >
              <Switch />
            </Form.Item>
          </Col>
        </Row>
        <div className='flex-grow-1 d-flex flex-column justify-content-end'>
          <div className='pb-5'>
            <Button type='primary' htmlType='submit' loading={loadingBtn}>
              {t('submit')}
            </Button>
          </div>
        </div>
      </Form>
    </Card>
  );
};

export default EmailProviderAdd;
