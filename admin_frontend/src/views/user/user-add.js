import React, { useState, useEffect } from 'react';
import {
  Button,
  Card,
  Col,
  DatePicker,
  Form,
  Input,
  InputNumber,
  Row,
  Select,
} from 'antd';
import { useNavigate } from 'react-router-dom';
import { toast } from 'react-toastify';
import { useDispatch, useSelector } from 'react-redux';
import { removeFromMenu, setMenuData } from '../../redux/slices/menu';
import userService from '../../services/user';
import { fetchClients } from '../../redux/slices/client';
import { useTranslation } from 'react-i18next';
import moment from 'moment';
import MediaUpload from '../../components/upload';

export default function UserAdd() {
  const { t } = useTranslation();
  const [form] = Form.useForm();
  const [error, setError] = useState(null);
  const [loadingBtn, setLoadingBtn] = useState(false);
  const navigate = useNavigate();

  const activeMenu = useSelector((list) => list.menu.activeMenu);
  const dispatch = useDispatch();
  const [image, setImage] = useState(
    activeMenu?.data?.image ? [activeMenu?.data?.image] : []
  );

  useEffect(() => {
    return () => {
      const data = form.getFieldsValue(true);
      data.birthday = JSON.stringify(data?.birthday);
      dispatch(
        setMenuData({ activeMenu, data: { ...activeMenu.data, ...data } })
      );
    };
  }, []);

  const onFinish = (values) => {
    setLoadingBtn(true);
    const body = {
      firstname: values.firstname,
      lastname: values.lastname,
      email: values.user_email,
      phone: values.phone,
      birthday: moment(values.birthday).format('YYYY-MM-DD'),
      gender: values.gender,
      password_confirmation: values.password_confirmation,
      password: values.password,
      images: [image[0]?.name],
    };
    const nextUrl = 'users/user';
    userService
      .create(body)
      .then(() => {
        toast.success(t('successfully.created'));
        dispatch(removeFromMenu({ ...activeMenu, nextUrl }));
        navigate(`/${nextUrl}`);
        dispatch(fetchClients());
      })
      .catch((err) => setError(err.response.data.params))
      .finally(() => setLoadingBtn(false));
  };

  const getInitialTimes = () => {
    if (!activeMenu.data?.birthday) {
      return {};
    }
    const birthday = JSON.parse(activeMenu.data?.birthday);
    return {
      birthday: moment(birthday),
    };
  };

  return (
    <Card title={t('add.user')}>
      <Form
        form={form}
        layout='vertical'
        initialValues={{
          gender: 'male',
          ...activeMenu.data,
          ...getInitialTimes(),
        }}
        onFinish={onFinish}
      >
        <Row gutter={12}>
          <Col span={24}>
            <Form.Item
              rules={[{ required: false, message: t('required') }]}
              label={t('avatar')}
            >
              <MediaUpload
                type='users'
                imageList={image}
                setImageList={setImage}
                form={form}
                multiple={false}
              />
            </Form.Item>
          </Col>

          <Col span={12}>
            <Form.Item
              label={t('firstname')}
              name='firstname'
              help={error?.firstname ? error.firstname[0] : null}
              validateStatus={error?.firstname ? 'error' : 'success'}
              rules={[{ required: true, message: t('required') }]}
            >
              <Input />
            </Form.Item>
          </Col>

          <Col span={12}>
            <Form.Item
              label={'lastname'}
              name='lastname'
              help={error?.lastname ? error.lastname[0] : null}
              validateStatus={error?.lastname ? 'error' : 'success'}
              rules={[{ required: true, message: t('required') }]}
            >
              <Input />
            </Form.Item>
          </Col>

          <Col span={12}>
            <Form.Item
              label={t('phone')}
              name='phone'
              help={error?.phone ? error.phone[0] : null}
              validateStatus={error?.phone ? 'error' : 'success'}
              rules={[{ required: true, message: t('required') }]}
            >
              <InputNumber min={0} className='w-100' />
            </Form.Item>
          </Col>

          <Col span={12}>
            <Form.Item
              label={t('birthday')}
              name='birthday'
              rules={[{ required: true, message: t('required') }]}
            >
              <DatePicker
                className='w-100'
                disabledDate={(current) => moment().add(-1, 'days') <= current}
              />
            </Form.Item>
          </Col>

          <Col span={12}>
            <Form.Item
              label={t('gender')}
              name='gender'
              rules={[{ required: true, message: t('required') }]}
            >
              <Select picker='dayTime' className='w-100'>
                <Select.Option value='male'>{t('male')}</Select.Option>
                <Select.Option value='female'>{t('female')}</Select.Option>
              </Select>
            </Form.Item>
          </Col>

          <Col span={12}>
            <Form.Item
              label={t('email')}
              name='user_email'
              help={error?.email ? error.email[0] : null}
              validateStatus={error?.email ? 'error' : 'success'}
              rules={[{ required: true, message: t('required') }]}
            >
              <Input type='email' />
            </Form.Item>
          </Col>

          <Col span={12}>
            <Form.Item
              label={t('password')}
              name='password'
              help={error?.password ? error.password[0] : null}
              validateStatus={error?.password ? 'error' : 'success'}
              rules={[{ required: true, message: t('required') }]}
            >
              <Input.Password type='password' className='w-100' />
            </Form.Item>
          </Col>

          <Col span={12}>
            <Form.Item
              label={t('password.confirmation')}
              help={
                error?.password_confirmation
                  ? error.password_confirmation[0]
                  : null
              }
              validateStatus={
                error?.password_confirmation ? 'error' : 'success'
              }
              name='password_confirmation'
              dependencies={['password']}
              hasFeedback
              rules={[
                {
                  required: true,
                  message: t('required'),
                },
                ({ getFieldValue }) => ({
                  validator(rule, value) {
                    if (!value || getFieldValue('password') === value) {
                      return Promise.resolve();
                    }
                    return Promise.reject(t('two.passwords.dont.match'));
                  },
                }),
              ]}
            >
              <Input.Password type='password' />
            </Form.Item>
          </Col>

          <Button type='primary' htmlType='submit' loading={loadingBtn}>
            {t('save')}
          </Button>
        </Row>
      </Form>
    </Card>
  );
}
