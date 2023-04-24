import React, { useEffect, useState } from 'react';
import {
  Form,
  Input,
  DatePicker,
  Select,
  Card,
  Button,
  Row,
  Col,
  InputNumber,
} from 'antd';
import LanguageList from '../../components/language-list';
import { useNavigate, useParams } from 'react-router-dom';
import couponService from '../../services/coupon';
import moment from 'moment';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { disableRefetch, removeFromMenu } from '../../redux/slices/menu';
import { useTranslation } from 'react-i18next';
import { fetchCoupon } from '../../redux/slices/coupons';

const CouponEdit = () => {
  const { t } = useTranslation();
  const [form] = Form.useForm();
  const [data, setData] = useState({});
  const [loading, setLoading] = useState(false);
  const [loadingBtn, setLoadingBtn] = useState(false);
  const { defaultLang, languages } = useSelector(
    (state) => state.formLang,
    shallowEqual
  );
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const { id } = useParams();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const { myShop } = useSelector((state) => state.myShop, shallowEqual);

  function getLanguageFields(data) {
    if (!data?.translations) {
      return {};
    }
    const { translations } = data;
    const result = languages.map((item) => ({
      [`title[${item.locale}]`]: translations.find(
        (el) => el.locale === item.locale
      )?.title,
    }));
    return Object.assign({}, ...result);
  }

  function getCoupon(id) {
    setLoading(true);
    couponService
      .getById(id)
      .then(({ data }) => {
        setData(data);
        form.setFieldsValue({
          ...getLanguageFields(data),
          expired_at: moment(data.expired_at, 'YYYY-MM-DD'),
        });
      })
      .finally(() => {
        setLoading(false);
        dispatch(disableRefetch(activeMenu));
      });
  }

  const onFinish = (values) => {
    setLoadingBtn(true);
    const params = {
      ...values,
      shop_id: myShop?.id,
      expired_at: moment(values.expired_at).format('YYYY-MM-DD'),
      qty: Number(values.qty),
      price: Number(values.price),
    };
    const nextUrl = 'coupons';
    if (id) {
      couponService
        .update(id, params)
        .then((res) => {
          dispatch(removeFromMenu({ ...activeMenu, nextUrl }));
          navigate(`/${nextUrl}`);
          dispatch(fetchCoupon());
        })
        .finally(() => setLoadingBtn(false));
    } else {
      couponService
        .create(params)
        .then((res) => {
          dispatch(removeFromMenu({ ...activeMenu, nextUrl }));
          navigate(`/${nextUrl}`);
        })
        .finally(() => setLoadingBtn(false));
    }
  };

  useEffect(() => {
    if (activeMenu.refetch) {
      getCoupon(id);
    }
  }, [activeMenu.refetch]);

  return (
    <Card title={t('edit.coupon')} extra={<LanguageList />} loading={loading}>
      <Form
        form={form}
        name='basic'
        initialValues={{
          ...data,
        }}
        layout='vertical'
        onFinish={onFinish}
      >
        <Row gutter={12}>
          <Col span={12}>
            {languages.map((item) => (
              <Form.Item
                key={'title' + item.id}
                label={t('title')}
                name={`title[${item.locale}]`}
                rules={[
                  {
                    required: item.locale === defaultLang,
                    message: t('required'),
                  },
                ]}
                hidden={item.locale !== defaultLang}
              >
                <Input />
              </Form.Item>
            ))}
          </Col>

          <Col span={12}>
            <Form.Item
              label={t('name')}
              name='name'
              rules={[{ required: true, message: t('required') }]}
            >
              <Input />
            </Form.Item>
          </Col>

          <Col span={12}>
            <Form.Item
              label={t('type')}
              name='type'
              rules={[{ required: true, message: t('required') }]}
            >
              <Select>
                <Select.Option value='fix'>{t('fix')}</Select.Option>
                <Select.Option value='percent'>{t('percent')}</Select.Option>
              </Select>
            </Form.Item>
          </Col>

          <Col span={12}>
            <Form.Item
              name='expired_at'
              label={t('expired.at')}
              rules={[{ required: true, message: t('required') }]}
            >
              <DatePicker
                className='w-100'
                placeholder=''
                disabledDate={(current) => moment().add(-1, 'days') >= current}
              />
            </Form.Item>
          </Col>

          <Col span={12}>
            <Form.Item
              label={t('quantity')}
              name='qty'
              rules={[{ required: true, message: t('required') }]}
            >
              <InputNumber min={0} className='w-100' />
            </Form.Item>
          </Col>

          <Col span={12}>
            <Form.Item
              label={t('price')}
              name='price'
              rules={[{ required: true, message: t('required') }]}
            >
              <InputNumber min={0} className='w-100' />
            </Form.Item>
          </Col>
        </Row>
        <Button type='primary' htmlType='submit' loading={loadingBtn}>
          {t('submit')}
        </Button>
      </Form>
    </Card>
  );
};

export default CouponEdit;
