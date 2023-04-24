import React, { useState } from 'react';
import { Card, Col, Pagination, Row, Spin } from 'antd';
import Meta from 'antd/es/card/Meta';
import { PlusOutlined } from '@ant-design/icons';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import getImage from '../../../helpers/getImage';
import { fetchRestProducts } from '../../../redux/slices/product';
import { toast } from 'react-toastify';
import ProductModal from './product-modal';
import { useTranslation } from 'react-i18next';
import { BsFillGiftFill } from 'react-icons/bs';
import { getCartData } from '../../../redux/selectors/cartSelector';
import RiveResult from '../../../components/rive-result';

export default function ProductCard() {
  const colLg = {
    lg: 8,
    xl: 6,
    xxl: 6,
  };
  const { t } = useTranslation();
  const [extrasModal, setExtrasModal] = useState(null);
  const dispatch = useDispatch();
  const { products, loading, meta, params } = useSelector(
    (state) => state.product,
    shallowEqual
  );
  const { data } = useSelector((state) => state.cart, shallowEqual);
  const currentData = useSelector((state) => getCartData(state.cart));
  const { currency } = useSelector((state) => state.cart, shallowEqual);
  function onChangePagination(page) {
    dispatch(
      fetchRestProducts({
        perPage: 12,
        page,
        shop_id: data[0].shop.value,
        status: 'published',
      })
    );
  }

  const addProductToCart = (item) => {
    if (!currency) {
      toast.warning(t('please.select.currency'));
      return;
    }
    if (!currentData.address) {
      toast.warning(t('please.select.address'));
      return;
    }
    if (!currentData.deliveries) {
      toast.warning(t('please.select.delivery'));
      return;
    }
    setExtrasModal(item);
  };

  return (
    <div className='px-2'>
      {loading ? (
        <Spin className='d-flex justify-content-center my-5' />
      ) : (
        <Row gutter={12} className='mt-4 product-card'>
          {products.length === 0 ? (
            <Col span={24}>
              <RiveResult id='nosell' />
            </Col>
          ) : (
            products.map((item, index) => (
              <Col {...colLg} key={index}>
                <Card
                  className='products-col'
                  key={item.id}
                  cover={<img alt={item.name} src={getImage(item.img)} />}
                  onClick={() => addProductToCart(item)}
                >
                  <Meta title={item.translation.title} />
                  <div className='preview'>
                    <PlusOutlined />
                  </div>
                  {item.stocks.map((it) => (
                    <span className={it.bonus ? 'show-bonus' : 'd-none'}>
                      <BsFillGiftFill /> {it.bonus?.value}
                      {'+'}
                      {it.bonus?.bonus_quantity}
                    </span>
                  ))}
                </Card>
              </Col>
            ))
          )}
        </Row>
      )}
      {extrasModal && (
        <ProductModal
          extrasModal={extrasModal}
          setExtrasModal={setExtrasModal}
        />
      )}
      <div className='d-flex justify-content-end my-5'>
        <Pagination
          total={meta.total}
          current={params.page}
          pageSize={12}
          showSizeChanger={false}
          onChange={onChangePagination}
        />
      </div>
    </div>
  );
}
