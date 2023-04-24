import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { toast } from 'react-toastify';
import {
  Button,
  Card,
  Col,
  DatePicker,
  Form,
  Input,
  Row,
} from 'antd';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { removeFromMenu, setMenuData } from '../../redux/slices/menu';
import { useTranslation } from 'react-i18next';
import TextEditor from './textEditor';
import moment from 'moment';
import messageSubscriberService from '../../services/messageSubscriber';
import { fetchMessageSubscriber } from '../../redux/slices/messegeSubscriber';
import emailService from '../../services/emailSettings';
import subscriberService from '../../services/subscriber';
import { DebounceSelect } from '../../components/search';

const MessageSubciribedAdd = () => {
  const { t } = useTranslation();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const dispatch = useDispatch();
  const [form] = Form.useForm();
  const navigate = useNavigate();
  const [loadingBtn, setLoadingBtn] = useState(false);
  const [loading, setLoading] = useState(false);
  const [userList, setUserList] = useState([]);
  const { defaultLang, languages } = useSelector(
    (state) => state.formLang,
    shallowEqual
  );

  useEffect(() => {
    return () => {
      const data = form.getFieldsValue(true);
      dispatch(setMenuData({ activeMenu, data }));
    };
  }, []);

  const fetchEmailProvider = () => {
    return emailService.get().then(({ data }) =>
      data.map((item) => ({
        label: item.host,
        value: item.id,
      }))
    );
  };

  const onFinish = (values) => {
    const body = {
      ...values,
      type: 'subscribe',
      email_setting_id: values.email_setting_id.value,
      send_to: moment(values.send_to).format('YYYY-MM-DD HH:mm:ss'),
    };
    setLoadingBtn(true);
    const nextUrl = 'message/subscriber';
    messageSubscriberService
      .create(body)
      .then(() => {
        toast.success(t('successfully.created'));
        dispatch(removeFromMenu({ ...activeMenu, nextUrl }));
        navigate(`/${nextUrl}`);
        dispatch(fetchMessageSubscriber());
      })
      .finally(() => setLoadingBtn(false));
  };

  const fetchUserList = () => {
    setLoading(true);
    subscriberService
      .getAll()
      .then((response) => {
        const data = response.data.map((data) => ({
          label: data.user?.firstname + ' ' + data.user?.lastname,
          value: data.user_id,
        }));
        setUserList(data);
      })
      .finally(() => setLoading(false));
  };

  useEffect(() => {
    fetchUserList();
  }, []);

  const clearSelected = () => {
    form.setFieldsValue({ email: [] });
  };

  const allSelect = () => {
    form.setFieldsValue({ email: userList.map((user) => user.value) });
  };

  const getInitialValues = () => {
    const data = activeMenu.data;
    if (!data?.send_to) {
      return data;
    }
    const start = data.send_to;
    return {
      ...data,
      send_to: moment(start, 'YYYY-MM-DD'),
    };
  };

  return (
    <Card title={t('add.subscriber')} className='h-100'>
      <Form
        name='subscriber-add'
        layout='vertical'
        onFinish={onFinish}
        form={form}
        initialValues={{
          type: 'order',
          ...activeMenu.data,
          ...getInitialValues(),
        }}
        className='d-flex flex-column h-100'
      >
        <Row gutter={12}>
          <Col span={12}>
            <Form.Item
              label={t('subject')}
              name='subject'
              rules={[
                {
                  required: true,
                  message: t('required'),
                },
              ]}
            >
              <Input />
            </Form.Item>
          </Col>
          {/* <Col span={12}>
            <Form.Item
              label={t('emails')}
              name='email'
              rules={[
                {
                  required: true,
                  message: t('required'),
                },
              ]}
            >
              <Select
                className='w-100'
                mode='multiple'
                loading={loading}
                dropdownRender={(menu) => (
                  <>
                    {menu}
                    <Divider style={{ margin: '8px 0' }} />
                    <Space style={{ padding: '0 8px 4px' }}>
                      <Button type='button' onClick={allSelect}>
                        Select all
                      </Button>
                      <Button type='button' onClick={clearSelected}>
                        Clear Selected
                      </Button>
                    </Space>
                  </>
                )}
                options={userList.map((item) => ({
                  label: item.label,
                  value: item.value,
                }))}
              />
            </Form.Item>
          </Col> */}

          <Col span={12}>
            <Form.Item
              label={t('email.setting.id')}
              name='email_setting_id'
              rules={[
                {
                  required: true,
                  message: t('required'),
                },
              ]}
            >
              <DebounceSelect
                fetchOptions={fetchEmailProvider}
                className='w-100'
                placeholder={t('email.setting.id')}
              />
            </Form.Item>
          </Col>
          <Col span={24}>
            <TextEditor languages={languages} form={form} lang={defaultLang} />
          </Col>
          <Col span={12}>
            <Form.Item
              label={t('alt.body')}
              name='alt_body'
              rules={[
                {
                  required: true,
                  message: t('required'),
                },
              ]}
            >
              <Input />
            </Form.Item>
          </Col>
          {/* <Col span={12}>
            <Form.Item name='has_date' valuePropName='checked'>
              <Checkbox checked={hasDate} onChange={handleChange}>
                {t('choose.discount.date')}
              </Checkbox>
            </Form.Item>
          </Col> */}

          <Col span={6}>
            <Form.Item
              label={t('send.to')}
              name='send_to'
              rules={[
                {
                  required: true,
                  message: t('required'),
                },
              ]}
            >
              <DatePicker
                showTime
                className='w-100'
                disabledDate={(current) => moment().add(-1, 'days') >= current}
              />
            </Form.Item>
          </Col>
        </Row>
        <div className='flex-grow-1 d-flex flex-column justify-content-end'>
          <div className='pb-5'>
            <Button type='primary' htmlType='submit' loading={loadingBtn}>
              {t('send')}
            </Button>
          </div>
        </div>
      </Form>
    </Card>
  );
};

export default MessageSubciribedAdd;
