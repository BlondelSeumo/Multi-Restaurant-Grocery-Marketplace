import React, { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { toast } from 'react-toastify';
import {
  Button,
  Card,
  Col,
  Form,
  Input,
  InputNumber,
  Row,
  Select,
  Spin,
} from 'antd';
import { IMG_URL } from '../../configs/app-global';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import {
  disableRefetch,
  removeFromMenu,
  setMenuData,
} from '../../redux/slices/menu';
import { useTranslation } from 'react-i18next';
import MediaUpload from '../../components/upload';
import { AsyncSelect } from '../../components/async-select';
import { DeleteOutlined, PlusOutlined } from '@ant-design/icons';
import LanguageList from '../../components/language-list';
import boxService from '../../services/box';
import { DebounceSelect } from '../../components/search';
import shopService from '../../services/restaurant';
import productService from '../../services/product';

const { TextArea } = Input;

const BoxEdit = () => {
  const { t } = useTranslation();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const { id } = useParams();
  const dispatch = useDispatch();
  const { defaultLang, languages } = useSelector(
    (state) => state.formLang,
    shallowEqual
  );

  const [image, setImage] = useState(
    activeMenu.data?.image ? [activeMenu.data?.image] : []
  );
  const [form] = Form.useForm();
  const shop_id = Form.useWatch('shop_id', form);
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
  const [loadingBtn, setLoadingBtn] = useState(false);

  useEffect(() => {
    return () => {
      const data = form.getFieldsValue(true);
      dispatch(setMenuData({ activeMenu, data }));
    };
  }, []);

  const createImage = (name) => {
    return {
      name,
      url: IMG_URL + name,
    };
  };

  const fetchBox = (id) => {
    setLoading(true);
    boxService
      .getById(id)
      .then((res) => {
        let box = res.data;
        form.setFieldsValue({
          ...box,
          shop_id: {
            value: box.shop.id,
            label: box.shop.translation?.title,
          },
          title: {
            [defaultLang]: box.translation.title,
          },
          description: {
            [defaultLang]: box.translation.description,
          },
          stocks: box.stocks.map((item) => ({
            stock_id: {
              value: item.id,
              label: item.product.translation.title,
            },
            ...item,
          })),
        });
        setImage([createImage(box.img)]);
      })
      .finally(() => {
        setLoading(false);
        dispatch(disableRefetch(activeMenu));
      });
  };

  function fetchProductsStock(shop_id) {
    return productService.getStock({ shop_id }).then((res) =>
      res.data.map((stock) => ({
        label:
          stock.product.translation.title +
          ' ' +
          stock.extras.map((ext) => ext.value).join(', '),
        value: stock.id,
      }))
    );
  }

  async function fetchShops(search) {
    const params = { search, status: 'approved' };
    return shopService.getAll(params).then(({ data }) =>
      data.map((item) => ({
        label: item.translation?.title,
        value: item.id,
      }))
    );
  }

  const onFinish = (values) => {
    const body = {
      ...values,
      images: image?.map((img) => img.name),
      stocks: values.stocks.map((stock) => ({
        min_quantity: stock.min_quantity,
        stock_id: stock.stock_id.value,
      })),
      shop_id: values.shop_id.value,
    };

    setLoadingBtn(true);
    const nextUrl = 'catalog/box';
    boxService
      .update(id, body)
      .then(() => {
        toast.success(t('successfully.updated'));
        navigate(`/${nextUrl}`);
        dispatch(removeFromMenu({ ...activeMenu, nextUrl }));
      })
      .finally(() => setLoadingBtn(false));
  };

  useEffect(() => {
    if (activeMenu.refetch) {
      fetchBox(id);
    }
  }, [activeMenu.refetch]);

  return (
    <Card title={t('edit.box')} extra={<LanguageList />}>
      {!loading ? (
        <Form
          name='basic'
          layout='vertical'
          onFinish={onFinish}
          form={form}
          initialValues={{ ...activeMenu.data }}
        >
          <Row gutter={12}>
            <Col span={12}>
              <Form.Item
                label={t('shop/restaurant')}
                name='shop_id'
                rules={[
                  {
                    required: true,
                    message: t('required'),
                  },
                ]}
              >
                <DebounceSelect
                  placeholder={t('select.shop')}
                  disabled
                  fetchOptions={fetchShops}
                  style={{ minWidth: 150 }}
                  allowClear={false}
                />
              </Form.Item>
            </Col>
            <Col span={12}>
              {languages.map((item) => (
                <Form.Item
                  key={'name' + item.id}
                  label={t('name')}
                  name={['title', item.locale]}
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
                label={t('discount.type')}
                name='discount_type'
                rules={[{ required: true, message: t('required') }]}
              >
                <Select
                  options={[
                    { label: t('fix'), value: 'fix' },
                    { label: t('percent'), value: 'percent' },
                  ]}
                />
              </Form.Item>
            </Col>
            <Col span={12}>
              <Form.Item
                label={t('discount.price')}
                name='discount_price'
                rules={[{ required: true, message: t('required') }]}
              >
                <InputNumber className='w-100' min={0} />
              </Form.Item>
            </Col>
            <Col span={24}>
              {languages.map((item) => (
                <Form.Item
                  key={'description' + item.id}
                  label={t('description')}
                  name={['description', item.locale]}
                  rules={[
                    {
                      required: item.locale === defaultLang,
                      message: t('required'),
                    },
                  ]}
                  hidden={item.locale !== defaultLang}
                >
                  <TextArea rows={3} />
                </Form.Item>
              ))}
              {shop_id && (
                <Form.List
                  name='stocks'
                  initialValue={[
                    { stock_id: undefined, min_quantity: undefined },
                  ]}
                >
                  {(fields, { add, remove }) => (
                    <>
                      {fields.map(({ key, name, ...restField }, i) => (
                        <Row gutter={12} align='middle'>
                          <Col span={11}>
                            <Form.Item
                              {...restField}
                              label={t('stock')}
                              name={[name, 'stock_id']}
                              rules={[
                                {
                                  required: true,
                                  message: t('required'),
                                },
                              ]}
                            >
                              <AsyncSelect
                                fetchOptions={() =>
                                  fetchProductsStock(shop_id?.value)
                                }
                                debounceTimeout={200}
                              />
                            </Form.Item>
                          </Col>
                          <Col span={11}>
                            <Form.Item
                              {...restField}
                              label={t('min.quantity')}
                              name={[name, 'min_quantity']}
                              rules={[
                                {
                                  required: true,
                                  message: t('required'),
                                },
                              ]}
                            >
                              <InputNumber min={0} className='w-100' />
                            </Form.Item>
                          </Col>
                          {i !== 0 && (
                            <Col
                              span={2}
                              className='d-flex justify-content-end'
                            >
                              <Button
                                onClick={() => remove(name)}
                                danger
                                className='w-100'
                                type='primary'
                                icon={<DeleteOutlined />}
                              />
                            </Col>
                          )}
                        </Row>
                      ))}
                      <Form.Item>
                        <Button
                          onClick={() => add()}
                          block
                          icon={<PlusOutlined />}
                        >
                          {t('add.stock')}
                        </Button>
                      </Form.Item>
                    </>
                  )}
                </Form.List>
              )}
            </Col>

            <Col span={12}>
              <Form.Item label={t('image')}>
                <MediaUpload
                  type='boxes'
                  imageList={image}
                  setImageList={setImage}
                  form={form}
                  multiple={false}
                />
              </Form.Item>
            </Col>
          </Row>
          <Button type='primary' htmlType='submit' loading={loadingBtn}>
            {t('submit')}
          </Button>
        </Form>
      ) : (
        <div className='d-flex justify-content-center align-items-center'>
          <Spin size='large' className='py-5' />
        </div>
      )}
    </Card>
  );
};

export default BoxEdit;
