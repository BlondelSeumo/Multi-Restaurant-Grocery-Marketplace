import React, { useEffect, useState } from 'react';
import { Button, Col, Descriptions, Row, Space, Spin } from 'antd';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { useNavigate, useParams } from 'react-router-dom';
import { removeFromMenu } from '../../../redux/slices/menu';
import { fetchSellerProducts } from '../../../redux/slices/product';
import { useTranslation } from 'react-i18next';
import productService from '../../../services/seller/product';
import { IMG_URL } from '../../../configs/app-global';

const ProductFinish = ({ prev }) => {
  const { t } = useTranslation();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const { defaultLang, languages } = useSelector(
    (state) => state.formLang,
    shallowEqual
  );
  const navigate = useNavigate();
  const dispatch = useDispatch();
  const [data, setData] = useState({});
  const [loading, setLoading] = useState(null);
  const { uuid } = useParams();
  const { params } = useSelector((state) => state.product, shallowEqual);

  function finish() {
    const body = {
      ...params,
      status: 'published',
    };
    const nextUrl = 'seller/products';
    dispatch(removeFromMenu({ ...activeMenu, nextUrl }));
    dispatch(fetchSellerProducts(body));
    navigate(`/${nextUrl}`);
  }

  function getLanguageFields(data) {
    if (!data?.translations) {
      return {};
    }
    const { translations } = data;
    const result = languages.map((item) => ({
      [`title[${item.locale}]`]: translations.find(
        (el) => el.locale === item.locale
      )?.title,
      [`description[${item.locale}]`]: translations.find(
        (el) => el.locale === item.locale
      )?.description,
    }));
    return Object.assign({}, ...result);
  }

  function fetchProduct(uuid) {
    setLoading(true);
    productService
      .getById(uuid)
      .then((res) => {
        const data = {
          ...res.data,
          ...getLanguageFields(res.data),
          properties: res.data.properties.map((item, index) => ({
            id: index,
            [`key[${item.locale}]`]: item.key,
            [`value[${item.locale}]`]: item.value,
          })),
          translation: undefined,
          translations: undefined,
        };
        setData(data);
      })
      .finally(() => setLoading(false));
  }

  useEffect(() => {
    fetchProduct(uuid);
  }, []);

  return !loading ? (
    <>
      <Descriptions title={t('product.info')} bordered>
        <Descriptions.Item label={`${t('title')} (${defaultLang})`} span={3}>
          {data[`title[${defaultLang}]`]}
        </Descriptions.Item>
        <Descriptions.Item
          label={`${t('description')} (${defaultLang})`}
          span={3}
        >
          {data[`description[${defaultLang}]`]}
        </Descriptions.Item>
        <Descriptions.Item label={t('shop')} span={1.5}>
          {data.shop?.translation.title}
        </Descriptions.Item>
        <Descriptions.Item label={t('category')} span={1.5}>
          {data.category?.translation.title}
        </Descriptions.Item>
        <Descriptions.Item label={t('brand')} span={1.5}>
          {data.brand?.title}
        </Descriptions.Item>
        <Descriptions.Item label={t('unit')} span={1.5}>
          {data.unit?.translation.title}
        </Descriptions.Item>
        <Descriptions.Item label={t('images')} span={3}>
          <Row gutter={12}>
            {data?.galleries?.map((item, idx) => (
              <Col key={'image' + idx}>
                <img width={80} alt='product' src={IMG_URL + item.path} />
              </Col>
            ))}
          </Row>
        </Descriptions.Item>
        <Descriptions.Item label={t('tax')}>{data.tax}</Descriptions.Item>
        <Descriptions.Item label={t('min.quantity')}>
          {data.min_qty}
        </Descriptions.Item>
        <Descriptions.Item label={t('max.quantity')}>
          {data.max_qty}
        </Descriptions.Item>
      </Descriptions>
      {data.stocks?.map((item, idx) => {
        if (!item) {
          return '';
        }
        return (
          <Descriptions key={'desc' + idx} bordered className='mt-4'>
            <Descriptions.Item label={t('price')} span={2}>
              {item.price}
            </Descriptions.Item>
            <Descriptions.Item label={t('quantity')} span={2}>
              {item.quantity}
            </Descriptions.Item>
            {item.extras.map((extra, idx) => (
              <Descriptions.Item
                key={'extra' + idx}
                label={extra?.group?.translation?.title}
              >
                {extra?.value}
              </Descriptions.Item>
            ))}
          </Descriptions>
        );
      })}
      <div className='d-flex mt-4'>
        <Space>
          <Button onClick={prev}>{t('prev')}</Button>
          <Button type='primary' onClick={finish}>
            {t('finish')}
          </Button>
        </Space>
      </div>
    </>
  ) : (
    <div className='d-flex justify-content-center align-items-center'>
      <Spin size='large' className='py-5' />
    </div>
  );
};

export default ProductFinish;
