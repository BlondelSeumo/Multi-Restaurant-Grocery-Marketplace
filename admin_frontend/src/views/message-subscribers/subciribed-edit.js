import React, { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { toast } from 'react-toastify';
import {
  Button,
  Card,
  Checkbox,
  Col,
  DatePicker,
  Form,
  Input,
  Row,
  Select,
  Tooltip,
} from 'antd';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import {
  disableRefetch,
  removeFromMenu,
  setMenuData,
} from '../../redux/slices/menu';
import { useTranslation } from 'react-i18next';
import TextEditor from './textEditor';
import moment from 'moment';
import messageSubscriberService from '../../services/messageSubscriber';
import Loading from '../../components/loading';
import { fetchMessageSubscriber } from '../../redux/slices/messegeSubscriber';
import emailService from '../../services/emailSettings';
import { DebounceSelect } from '../../components/search';

const MessageSubciribedAdd = () => {
  const { t } = useTranslation();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const dispatch = useDispatch();
  const [form] = Form.useForm();
  const navigate = useNavigate();
  const [hasDate, setHasDate] = useState(activeMenu.data?.has_date);
  const [loadingBtn, setLoadingBtn] = useState(false);
  const [loading, setLoading] = useState(false);
  const { id } = useParams();
  const { defaultLang, languages } = useSelector(
    (state) => state.formLang,
    shallowEqual
  );

  const handleChange = (event) => setHasDate(event.target.checked);

  useEffect(() => {
    return () => {
      const values = form.getFieldsValue(true);
      const send_to = JSON.stringify(values.send_to);
      const data = { ...values, send_to };
      dispatch(setMenuData({ activeMenu, data }));
    };
  }, []);

  const fetchSubscriber = (id) => {
    setLoading(true);
    messageSubscriberService
      .getById(id)
      .then((res) => {
        setHasDate(true);
        const data = {
          ...res.data,
          send_to: moment(res.data.send_to, 'YYYY-MM-DD HH:mm:ss'),
          has_date: true,
          email_setting_id: {
            label: res.data.email_setting.host,
            value: res.data.email_setting.id,
          },
        };
        form.setFieldsValue(data);
      })
      .finally(() => {
        setLoading(false);
        dispatch(disableRefetch(activeMenu));
      });
  };

  const onFinish = (values) => {
    const body = {
      ...values,
      send_to: moment(values.send_to).format('YYYY-MM-DD HH:mm:ss'),
      email_setting_id: values.email_setting_id.value,
    };
    setLoadingBtn(true);
    const nextUrl = 'message/subscriber';
    messageSubscriberService
      .update(id, body)
      .then(() => {
        toast.success(t('successfully.created'));
        dispatch(removeFromMenu({ ...activeMenu, nextUrl }));
        navigate(`/${nextUrl}`);
        dispatch(fetchMessageSubscriber());
      })
      .finally(() => setLoadingBtn(false));
  };

  useEffect(() => {
    if (activeMenu.refetch) {
      fetchSubscriber(id);
    }
  }, [activeMenu.refetch]);

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

  const fetchEmailProvider = () => {
    return emailService.get().then(({ data }) =>
      data.map((item) => ({
        label: item.host,
        value: item.id,
      }))
    );
  };

  return (
    <>
      {!loading ? (
        <Card title={t('edit.subscriber')} className='h-100'>
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
              <Col span={12}>
                <Form.Item
                  label={t('type')}
                  name='type'
                  rules={[
                    {
                      required: true,
                      message: t('required'),
                    },
                  ]}
                >
                  <Select>
                    <Select.Option value={'order'}>
                      <Tooltip title={t('order.information')}>
                        {t('order')}
                      </Tooltip>
                    </Select.Option>
                    <Select.Option value={'subscribe'}>
                      <Tooltip title={t('subscribe.information')}>
                        {t('subscribe')}
                      </Tooltip>
                    </Select.Option>
                    <Select.Option value={'verify'}>
                      <Tooltip title={t('verify.information')}>
                        {t('verify')}
                      </Tooltip>
                    </Select.Option>
                  </Select>
                </Form.Item>
              </Col>
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
                <TextEditor
                  languages={languages}
                  form={form}
                  lang={defaultLang}
                />
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
                    disabledDate={(current) =>
                      moment().add(-1, 'days') >= current
                    }
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
      ) : (
        <Loading />
      )}
    </>
  );
};

export default MessageSubciribedAdd;
