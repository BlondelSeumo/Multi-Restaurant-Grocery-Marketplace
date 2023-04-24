import React, { useEffect, useState } from 'react';
import { Button, Col, Form, InputNumber, Row, Space } from 'antd';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import extraService from '../../../services/seller/extras';
import { DeleteOutlined, PlusOutlined } from '@ant-design/icons';
import productService from '../../../services/seller/product';
import { setMenuData } from '../../../redux/slices/menu';
import { AsyncSelect } from '../../../components/async-select';
import { useTranslation } from 'react-i18next';
import { useParams } from 'react-router-dom';
import { DebounceSelect } from '../../../components/search';
import Loading from '../../../components/loading';
import { fetchSelletExtraGroups } from '../../../redux/slices/extraGroup';
import DeleteButton from '../../../components/delete-button';

const ProductStock = ({ prev, next }) => {
  const { t } = useTranslation();
  const [form] = Form.useForm();
  const dispatch = useDispatch();
  const { uuid } = useParams();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const { myShop } = useSelector((state) => state.myShop, shallowEqual);
  const [loading, setLoading] = useState(null);
  const { extraGroups } = useSelector(
    (state) => state.extraGroup,
    shallowEqual
  );

  const [loadingBtn, setLoadingBtn] = useState(null);

  const onFinish = (values) => {
    setLoadingBtn(true);
    const { stocks } = values;
    let extras;
    const isProductWithExtras = !!activeMenu.data?.extras?.length;
    if (isProductWithExtras) {
      extras = stocks.map((item) => ({
        price: item.price,
        quantity: item.quantity,
        ids: activeMenu.data?.extras.map(
          (_, idx) => item[`extras[${idx}]`].value
        ),
        addons: item.addons ? item.addons?.map((i) => i.value) : [],
      }));
    } else {
      extras = [
        {
          price: stocks[0].price,
          quantity: stocks[0].quantity,
          addons: stocks[0].addons ? stocks[0].addons.map((i) => i.value) : [],
        },
      ];
    }
    productService
      .stocks(uuid, { extras })
      .then(() => next())
      .finally(() => setLoadingBtn(false));
  };

  function fetchProduct(uuid) {
    setLoading(true);
    productService
      .getById(uuid)
      .then((res) => {
        const data = {
          extras: res.data.stocks[0]?.extras.map((el) => el.extra_group_id),
          stocks: res.data.stocks.map((stock) => ({
            ...stock,
            ...Object.assign(
              {},
              ...stock.extras.map((extra, idx) => ({
                [`extras[${idx}]`]: {
                  label: extra.value,
                  value: extra.id,
                },
              }))
            ),
            addons: stock?.addons?.map((item) => ({
              label: item?.product?.translation?.title,
              value: item?.product?.id,
            })),
            quantity: stock.quantity || 0,
            extras: undefined,
          })),
        };
        form.setFieldsValue({
          stocks: res.data?.stocks?.length ? data.stocks : [''],
        });
      })
      .finally(() => {
        setLoading(false);
      });
  }

  function addToMenu() {
    const extras = form.getFieldsValue(true);
    dispatch(
      setMenuData({
        activeMenu,
        data: { ...activeMenu.data, stocks: extras.stocks },
      })
    );
  }

  function getExtrasName(extrasId) {
    const extraItem = extraGroups.find((item) => item.id === extrasId);
    return extraItem?.translation?.title;
  }

  function fetchExtra(id) {
    return extraService.getGroupById(id).then((res) =>
      res.data.extra_values.map((item) => ({
        label: item.value,
        value: item.id,
      }))
    );
  }

  const fetchAddons = (search) => {
    const params = {
      search,
      addon: 1,
      shop_id: myShop.id,
    };
    return productService.getAll(params).then((res) =>
      res.data.map((item) => ({
        label: item?.translation?.title,
        value: item?.id,
      }))
    );
  };

  useEffect(() => {
    fetchProduct(uuid);
  }, []);

  useEffect(() => {
    const params = { valid: true };
    dispatch(fetchSelletExtraGroups(params));
  }, []);

  return (
    <>
      {loading ? (
        <Loading />
      ) : (
        <Form layout='vertical' form={form} onFinish={onFinish}>
          <Form.List name='stocks'>
            {(fields, { add, remove }) => {
              return (
                <div>
                  {fields.map((field, index) => (
                    <Row
                      key={field.key}
                      gutter={12}
                      align='middle'
                      style={{ flexWrap: 'nowrap', overflowX: 'auto' }}
                      hidden={!activeMenu.data?.extras?.length && field.key}
                    >
                      <Col>
                        <Form.Item
                          label={t('price')}
                          name={[index, 'price']}
                          rules={[{ required: true, message: t('requried') }]}
                        >
                          <InputNumber
                            min={1}
                            className='w-100'
                            style={{ minWidth: 200 }}
                          />
                        </Form.Item>
                      </Col>
                      <Col>
                        <Form.Item
                          label={t('quantity')}
                          name={[index, 'quantity']}
                          rules={[{ required: true, message: t('required') }]}
                        >
                          <InputNumber
                            min={1}
                            className='w-100'
                            style={{ minWidth: 200 }}
                          />
                        </Form.Item>
                      </Col>
                      <Col>
                        <Form.Item
                          label={t('addons')}
                          name={[index, 'addons']}
                          rules={[{ required: false, message: t('required') }]}
                        >
                          <DebounceSelect
                            mode='multiple'
                            style={{ minWidth: '300px' }}
                            fetchOptions={fetchAddons}
                            allowClear={true}
                          />
                        </Form.Item>
                      </Col>
                      {activeMenu.data?.extras?.map((item, idx) => (
                        <Col key={'extra' + item}>
                          <Form.Item
                            label={getExtrasName(item)}
                            name={[index, `extras[${idx}]`]}
                            rules={[{ required: true, message: t('required') }]}
                          >
                            <AsyncSelect
                              fetchOptions={() => fetchExtra(item)}
                              className='w-100'
                              style={{ minWidth: 200 }}
                            />
                          </Form.Item>
                        </Col>
                      ))}
                      <Col>
                        {field.key ? (
                          <DeleteButton
                            type='primary'
                            danger
                            icon={<DeleteOutlined />}
                            onClick={() => remove(field.name)}
                          />
                        ) : (
                          ''
                        )}
                      </Col>
                    </Row>
                  ))}
                  {activeMenu.data?.extras?.length ? (
                    <Button
                      type='dashed'
                      style={{ width: '100%' }}
                      onClick={() => {
                        add();
                        addToMenu();
                      }}
                    >
                      <Space>
                        <PlusOutlined />
                        {t('add')}
                      </Space>
                    </Button>
                  ) : (
                    ''
                  )}
                </div>
              );
            }}
          </Form.List>
          <Space className='mt-4'>
            <Button onClick={prev}>{t('prev')}</Button>
            <Button type='primary' htmlType='submit' loading={!!loadingBtn}>
              {t('next')}
            </Button>
          </Space>
        </Form>
      )}
    </>
  );
};

export default ProductStock;
