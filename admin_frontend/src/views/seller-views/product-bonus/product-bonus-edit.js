import React, { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { toast } from 'react-toastify';
import {
  Button,
  Card,
  Col,
  DatePicker,
  Form,
  InputNumber,
  Row,
  Switch,
} from 'antd';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import {
  disableRefetch,
  removeFromMenu,
  setMenuData,
} from '../../../redux/slices/menu';
import productService from '../../../services/seller/product';
import { useTranslation } from 'react-i18next';
import moment from 'moment';
import { fetchBonus } from '../../../redux/slices/product-bonus';
import bonusService from '../../../services/seller/bonus';
import { AsyncSelect } from '../../../components/async-select';
import Loading from '../../../components/loading';

const ProductBonusAdd = () => {
  const { t } = useTranslation();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const dispatch = useDispatch();
  const [form] = Form.useForm();
  const navigate = useNavigate();
  const [loadingBtn, setLoadingBtn] = useState(false);
  const [loading, setLoading] = useState(false);
  const { id } = useParams();

  useEffect(() => {
    return () => {
      const data = form.getFieldsValue(true);
      data.expired_at = JSON.stringify(data?.expired_at);
      dispatch(setMenuData({ activeMenu, data: data }));
    };
  }, []);

  function getProducts(bonus) {
    form.setFieldsValue({
      ...bonus,
      expired_at: moment(bonus.expired_at, 'YYYY-MM-DD'),
      bonusable_id: {
        label:
          bonus?.bonusable?.product.translation.title +
          ' ' +
          bonus.bonusable?.extras.map((ext) => ext.value).join(', '),
        value: bonus.bonusable?.id,
      },
      bonus_stock_id: {
        label:
          bonus?.bonusStock?.product.translation.title +
          ' ' +
          bonus.bonusStock?.extras.map((ext) => ext.value).join(', '),
        value: bonus?.bonusable?.id,
      },
    });
    setLoading(false);
  }

  const getBonus = (id) => {
    setLoading(true);
    bonusService
      .getById(id)
      .then((res) => {
        let bonus = res.data;
        getProducts(bonus);
      })
      .finally(() => dispatch(disableRefetch(activeMenu)));
  };

  useEffect(() => {
    if (activeMenu.refetch) {
      getBonus(id);
    }
  }, [activeMenu.refetch]);

  const onFinish = (values) => {
    const body = {
      expired_at: moment(values.expired_at).format('YYYY-MM-DD'),
      status: values.status,
      type: 'count',
      bonusable_id: values.bonusable_id.value,
      value: values.value,
      bonus_stock_id: values.bonus_stock_id.value,
      bonus_quantity: values.bonus_quantity,
    };
    setLoadingBtn(true);
    const nextUrl = 'seller/bonus/product';
    bonusService
      .update(id, body)
      .then(() => {
        toast.success(t('successfully.created'));
        dispatch(removeFromMenu({ ...activeMenu, nextUrl }));
        navigate(`/${nextUrl}`);
        dispatch(fetchBonus());
      })
      .finally(() => setLoadingBtn(false));
  };

  function fetchProductsStock() {
    return productService.getStock().then((res) =>
      res.data.map((stock) => ({
        label:
          stock.product.translation.title +
          ' ' +
          stock.extras?.map((ext) => ext.value).join(', '),
        value: stock.id,
      }))
    );
  }

  const getInitialTimes = () => {
    if (!activeMenu.data?.expired_at) {
      return {};
    }
    const data = JSON.parse(activeMenu.data?.expired_at);
    return {
      expired_at: moment(data, 'HH:mm:ss'),
    };
  };

  return (
    <Card title={t('add.bonus')} className='h-100'>
      {!loading ? (
        <Form
          name='bonus-add'
          layout='vertical'
          onFinish={onFinish}
          form={form}
          initialValues={{
            status: true,
            ...activeMenu.data,
            ...getInitialTimes(),
          }}
          className='d-flex flex-column h-100'
        >
          <Row gutter={12}>
            <Col span={12}>
              <Form.Item
                label={t('products.stock')}
                name={'bonusable_id'}
                rules={[
                  {
                    required: true,
                    message: t('required'),
                  },
                ]}
              >
                <AsyncSelect
                  fetchOptions={fetchProductsStock}
                  debounceTimeout={200}
                />
              </Form.Item>
            </Col>
            <Col span={12}>
              <Form.Item
                label={t('product.quantity')}
                name={'value'}
                rules={[
                  {
                    required: true,
                    message: t('required'),
                  },
                ]}
              >
                <InputNumber className='w-100' />
              </Form.Item>
            </Col>
            <Col span={12}>
              <Form.Item
                label={t('bonus.product.stock')}
                name={'bonus_stock_id'}
                rules={[
                  {
                    required: true,
                    message: t('required'),
                  },
                ]}
              >
                <AsyncSelect
                  fetchOptions={fetchProductsStock}
                  debounceTimeout={200}
                />
              </Form.Item>
            </Col>
            <Col span={12}>
              <Form.Item
                label={t('bonus.product.quantity')}
                name={'bonus_quantity'}
                rules={[
                  {
                    required: true,
                    message: t('required'),
                  },
                ]}
              >
                <InputNumber className='w-100' />
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
                  disabledDate={(current) =>
                    moment().add(-1, 'days') >= current
                  }
                />
              </Form.Item>
            </Col>
            <Col span={8}>
              <Form.Item
                label={t('active')}
                name='status'
                valuePropName='checked'
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
      ) : (
        <Loading />
      )}
    </Card>
  );
};

export default ProductBonusAdd;
