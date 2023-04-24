import React, { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { toast } from 'react-toastify';
import {
  Button,
  Card,
  Col,
  Form,
  Input,
  Row,
  Select,
  Spin,
  Switch,
} from 'antd';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import {
  disableRefetch,
  removeFromMenu,
  setMenuData,
} from '../../redux/slices/menu';
import { useTranslation } from 'react-i18next';
import Paystack from '../../assets/images/paystack.svg';
import { FaPaypal } from 'react-icons/fa';
import { SiStripe, SiRazorpay } from 'react-icons/si';
import { paymentPayloadService } from '../../services/paymentPayload';
import { AsyncSelect } from '../../components/async-select';
import currencyService from '../../services/currency';
import i18n from '../../configs/i18next';
import { fetchPaymentPayloads } from '../../redux/slices/paymentPayload';

const PaymentPayloadEdit = () => {
  const { t } = useTranslation();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const dispatch = useDispatch();
  const [form] = Form.useForm();
  const navigate = useNavigate();
  const { id } = useParams();
  const [loadingBtn, setLoadingBtn] = useState(false);
  const [loading, setLoading] = useState(false);
  const { languages } = useSelector((state) => state.formLang, shallowEqual);
  const [activePayment, setActivePayment] = useState(null);
  const { defaultCurrency } = useSelector(
    (state) => state.currency,
    shallowEqual
  );

  useEffect(() => {
    return () => {
      const data = form.getFieldsValue(true);
      dispatch(setMenuData({ activeMenu, data }));
    };
  }, []);

  const getPayload = (id) => {
    setLoading(true);
    paymentPayloadService
      .getById(id)
      .then(({ data }) => {
        setActivePayment({
          label: data.payment.tag,
          value: data.payment.id,
        });
        form.setFieldsValue({
          ...data.payload,
          payment_id: data.payment.tag,
          paypal_validate_ssl: Boolean(data.payload.paypal_validate_ssl),
        });
      })
      .finally(() => {
        setLoading(false);
        dispatch(disableRefetch(activeMenu));
      });
  };

  const onFinish = (values) => {
    delete values.payment_id;
    setLoadingBtn(true);
    const body = {
      payment_id: activePayment.value,
      payload: {
        ...values,
        paypal_validate_ssl: values?.paypal_validate_ssl
          ? Number(values.paypal_validate_ssl)
          : undefined,
      },
    };
    paymentPayloadService
      .update(id, body)
      .then(() => {
        const nextUrl = 'payment-payloads';
        toast.success(t('successfully.updated'));
        dispatch(removeFromMenu({ ...activeMenu, nextUrl }));
        navigate(`/${nextUrl}`);
        dispatch(fetchPaymentPayloads());
      })
      .finally(() => setLoadingBtn(false));
  };

  useEffect(() => {
    if (activeMenu.refetch) {
      getPayload(id);
    }
  }, [activeMenu.refetch]);

  const handleAddIcon = (data) => {
    switch (data) {
      case 'Paypal':
        return <FaPaypal size={80} />;
      case 'Stripe':
        return <SiStripe size={80} />;
      case 'Razorpay':
        return <SiRazorpay size={80} />;
      case 'Paystack':
        return <img src={Paystack} alt='img' width='80' height='80' />;
      default:
        return null;
    }
  };

  return (
    <Card title={t('edit.payment.payloads')} className='h-100'>
      {!loading ? (
        <Form
          name='edit.payment.payloads'
          layout='vertical'
          onFinish={onFinish}
          form={form}
          initialValues={{ ...activeMenu.data }}
          className='d-flex flex-column h-100'
        >
          <Row gutter={12}>
            <Col
              span={
                activePayment?.label === 'cash' ||
                activePayment?.label === 'wallet'
                  ? 12
                  : 24
              }
            >
              <Form.Item
                label={t('payment')}
                name='payment_id'
                rules={[
                  {
                    required: true,
                    message: t('required'),
                  },
                ]}
              >
                <Select
                  notFoundContent={
                    loading ? <Spin size='small' /> : 'no results'
                  }
                  allowClear
                  disabled
                />
              </Form.Item>
            </Col>

            {activePayment?.label === 'cash' ||
            activePayment?.label === 'wallet' ? (
              ''
            ) : (
              <>
                <Col span={24} offset={10}>
                  <>{handleAddIcon(activePayment?.label)}</>
                </Col>

                {activePayment?.label === 'paystack' ? (
                  <>
                    <Col span={12}>
                      <Form.Item
                        label={t('paystack.pk')}
                        name='paystack_pk'
                        rules={[{ required: true, message: t('required') }]}
                      >
                        <Input />
                      </Form.Item>
                    </Col>
                    <Col span={12}>
                      <Form.Item
                        label={t('paystack.sk')}
                        name='paystack_sk'
                        rules={[{ required: true, message: t('required') }]}
                      >
                        <Input />
                      </Form.Item>
                    </Col>{' '}
                    <Col span={12}>
                      <Form.Item
                        label={t('currency')}
                        name='currency'
                        rules={[{ required: true, message: t('required') }]}
                      >
                        <AsyncSelect
                          placeholder={t('select.currency')}
                          valuePropName='label'
                          defaultValue={{
                            value: defaultCurrency.id,
                            label: defaultCurrency.title,
                          }}
                          fetchOptions={() =>
                            currencyService.getAll().then(({ data }) => {
                              return data
                                .filter((item) => item.active)
                                .map((item) => ({
                                  value: item.id,
                                  label: `${item.title}`,
                                  key: item.id,
                                }));
                            })
                          }
                        />
                      </Form.Item>
                    </Col>
                  </>
                ) : activePayment?.label === 'paypal' ? (
                  <>
                    <Col span={12}>
                      <Form.Item
                        label={t('paypal.mode')}
                        name='paypal_mode'
                        rules={[{ required: true, message: t('required') }]}
                      >
                        <Select
                          options={[
                            { value: 'live', label: t('live') },
                            { value: 'sandbox', label: t('sandbox') },
                          ]}
                        />
                      </Form.Item>
                    </Col>
                    <Col span={12}>
                      <Form.Item
                        label={t('paypal.sandbox.client.id')}
                        name='paypal_sandbox_client_id'
                        rules={[{ required: true, message: t('required') }]}
                      >
                        <Input />
                      </Form.Item>
                    </Col>
                    <Col span={12}>
                      <Form.Item
                        label={t('paypal.sandbox.client.secret')}
                        name='paypal_sandbox_client_secret'
                        rules={[{ required: true, message: t('required') }]}
                      >
                        <Input />
                      </Form.Item>
                    </Col>
                    <Col span={12}>
                      <Form.Item
                        label={t('paypal.sandbox.app.id')}
                        name='paypal_sandbox_app_id'
                        rules={[{ required: true, message: t('required') }]}
                      >
                        <Input />
                      </Form.Item>
                    </Col>
                    <Col span={12}>
                      <Form.Item
                        label={t('paypal.live.client.id')}
                        name='paypal_live_client_id'
                        rules={[{ required: true, message: t('required') }]}
                      >
                        <Input />
                      </Form.Item>
                    </Col>
                    <Col span={12}>
                      <Form.Item
                        label={t('paypal.live.client.secret')}
                        name='paypal_live_client_secret'
                        rules={[{ required: true, message: t('required') }]}
                      >
                        <Input />
                      </Form.Item>
                    </Col>
                    <Col span={12}>
                      <Form.Item
                        label={t('paypal.live.app.id')}
                        name='paypal_live_app_id'
                        rules={[{ required: true, message: t('required') }]}
                      >
                        <Input />
                      </Form.Item>
                    </Col>
                    <Col span={12}>
                      <Form.Item
                        label={t('paypal.payment.action')}
                        name='paypal_payment_action'
                        rules={[{ required: true, message: t('required') }]}
                      >
                        <Select
                          options={[
                            { value: 'Sale', label: t('sale') },
                            { value: 'Order', label: t('order') },
                            {
                              value: 'Authorization',
                              label: t('authorization'),
                            },
                          ]}
                        />
                      </Form.Item>
                    </Col>
                    <Col span={12}>
                      <Form.Item
                        label={t('paypal.currency')}
                        name='paypal_currency'
                        rules={[{ required: true, message: t('required') }]}
                      >
                        <AsyncSelect
                          placeholder={t('select.currency')}
                          fetchOptions={() =>
                            currencyService.getAll().then(({ data }) => {
                              return data
                                .filter((item) => item.active)
                                .map((item) => ({
                                  value: item.id,
                                  label: `${item.title} (${item.symbol || ''})`,
                                  key: item.id,
                                }));
                            })
                          }
                        />
                      </Form.Item>
                    </Col>
                    <Col span={12}>
                      <Form.Item
                        label={t('paypal.locale')}
                        name='paypal_locale'
                        rules={[{ required: true, message: t('required') }]}
                        valuePropName='value'
                      >
                        <Select
                          placeholder={t('select.locale')}
                          defaultValue={{
                            label: languages.find(
                              (item) => item.locale === i18n.language
                            )?.title,
                            value: i18n.language,
                          }}
                          options={languages?.map((lang) => ({
                            value: lang.locale,
                            label: lang.title,
                          }))}
                        />
                      </Form.Item>
                    </Col>
                    <Col span={12}>
                      <Form.Item
                        label={t('paypal.validate.ssl')}
                        name='paypal_validate_ssl'
                        valuePropName='checked'
                      >
                        <Switch />
                      </Form.Item>
                    </Col>
                  </>
                ) : activePayment?.label === 'stripe' ? (
                  <>
                    <Col span={12}>
                      <Form.Item
                        label={t('stripe.pk')}
                        name='stripe_pk'
                        rules={[{ required: true, message: t('required') }]}
                      >
                        <Input />
                      </Form.Item>
                    </Col>
                    <Col span={12}>
                      <Form.Item
                        label={t('stripe.sk')}
                        name='stripe_sk'
                        rules={[{ required: true, message: t('required') }]}
                      >
                        <Input />
                      </Form.Item>
                    </Col>{' '}
                    <Col span={12}>
                      <Form.Item
                        label={t('currency')}
                        name='currency'
                        rules={[{ required: true, message: t('required') }]}
                      >
                        <AsyncSelect
                          placeholder={t('select.currency')}
                          valuePropName='label'
                          defaultValue={{
                            value: defaultCurrency.id,
                            label: defaultCurrency.title,
                          }}
                          fetchOptions={() =>
                            currencyService.getAll().then(({ data }) => {
                              return data
                                .filter((item) => item.active)
                                .map((item) => ({
                                  value: item.id,
                                  label: `${item.title}`,
                                  key: item.id,
                                }));
                            })
                          }
                        />
                      </Form.Item>
                    </Col>
                  </>
                ) : activePayment?.label === 'razorpay' ? (
                  <>
                    <Col span={12}>
                      <Form.Item
                        label={t('razorpay.key')}
                        name='razorpay_key'
                        rules={[{ required: true, message: t('required') }]}
                      >
                        <Input />
                      </Form.Item>
                    </Col>
                    <Col span={12}>
                      <Form.Item
                        label={t('razorpay.secret')}
                        name='razorpay_secret'
                        rules={[{ required: true, message: t('required') }]}
                      >
                        <Input />
                      </Form.Item>
                    </Col>{' '}
                    <Col span={12}>
                      <Form.Item
                        label={t('currency')}
                        name='currency'
                        rules={[{ required: true, message: t('required') }]}
                      >
                        <AsyncSelect
                          placeholder={t('select.currency')}
                          valuePropName='label'
                          defaultValue={{
                            value: defaultCurrency.id,
                            label: defaultCurrency.title,
                          }}
                          fetchOptions={() =>
                            currencyService.getAll().then(({ data }) => {
                              return data
                                .filter((item) => item.active)
                                .map((item) => ({
                                  value: item.id,
                                  label: `${item.title}`,
                                  key: item.id,
                                }));
                            })
                          }
                        />
                      </Form.Item>
                    </Col>
                  </>
                ) : null}
              </>
            )}
          </Row>
          <div className='flex-grow-1 d-flex flex-column justify-content-end'>
            <div className='pb-5'>
              <Button
                type='primary'
                htmlType='submit'
                loading={loadingBtn}
                disabled={loadingBtn}
              >
                {t('submit')}
              </Button>
            </div>
          </div>
        </Form>
      ) : (
        <div className='d-flex justify-content-center align-items-center'>
          <Spin size='large' className='py-5' />
        </div>
      )}
    </Card>
  );
};

export default PaymentPayloadEdit;
