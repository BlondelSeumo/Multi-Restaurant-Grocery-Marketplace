import React, { useState } from 'react';
import {
  CheckOutlined,
  CloseOutlined,
  EditOutlined,
  MinusOutlined,
  PlusOutlined,
} from '@ant-design/icons';
import { Button, Card, Col, Input, Row, Space, Spin } from 'antd';
import Meta from 'antd/lib/card/Meta';
import getImage from '../../helpers/getImage';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import {
  addOrderCoupon,
  clearOrderProducts,
  removeFromOrder,
  setOrderData,
  setOrderTotal,
  verifyOrderCoupon,
  changeOrderedProductQuantity,
  setOrderProducts,
} from '../../redux/slices/order';
import orderService from '../../services/order';
import ExtrasModal from './extrasModal';
import numberToPrice from '../../helpers/numberToPrice';
import useDidUpdate from '../../helpers/useDidUpdate';
import { useNavigate } from 'react-router-dom';
import { addMenu, setMenuData } from '../../redux/slices/menu';
import { fetchRestProducts } from '../../redux/slices/product';
import { useTranslation } from 'react-i18next';
import invokableService from '../../services/rest/invokable';
import QueryString from 'qs';
import { BsFillGiftFill } from 'react-icons/bs';
import shopService from '../../services/restaurant';

export default function OrderItems() {
  const { t } = useTranslation();
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const { orderItems, data, total, coupon, orderProducts } = useSelector(
    (state) => state.order,
    shallowEqual
  );
  const [loading, setLoading] = useState(false);
  const [extrasModal, setExtrasModal] = useState(null);
  const [loadingCoupon, setLoadingCoupon] = useState(false);
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);

  function formatProducts(list) {
    const addons = list?.map((item) => ({
      quantity: item.quantity,
      stock_id: item.stockID ? item.stockID?.id : item.stock?.id,
    }));

    const products = list?.flatMap((item) =>
      item.addons?.map((addon, index) => ({
        quantity: addon.quantity,
        stock_id: addon.stock_id,
        parent_id: item.stockID ? item.stockID?.id : item.stock?.id,
      }))
    );

    const combine = addons.concat(products);

    const result = {
      products: combine,
      currency_id: data?.currency?.id,
      coupon: data?.coupon?.name,
      shop_id: data?.shop?.value,
      type: data?.deliveries?.label?.toLowerCase(),
      address: {
        latitude: data?.address?.lat,
        longitude: data?.address?.lng,
      },
    };
    return QueryString.stringify(result, { addQueryPrefix: true });
  }

  useDidUpdate(() => {
    dispatch(
      fetchRestProducts({
        perPage: 12,
        currency_id: data.currency.id,
        shop_id: data.shop?.value,
      })
    );
    if (data?.shop?.value) {
      fetchShop(data?.shop?.value);
    }
  }, [data.currency, data.shop]);

  useDidUpdate(() => {
    if (orderItems.length) {
      productCalculate();
    } else {
      dispatch(clearOrderProducts());
    }
  }, [orderItems, data.currency, data.address]);

  function productCalculate() {
    if (!!data?.deliveries?.label) {
      const products = formatProducts(orderItems);

      setLoading(true);
      orderService
        .calculate(products)
        .then(({ data }) => {
          const product = data.data;
          const orderData = {
            product_tax: product.total_tax,
            shop_tax: product.total_shop_tax,
            order_total: product.total_price,
            delivery_fee: product.delivery_fee,
          };
          dispatch(setOrderTotal(orderData));
        })
        .catch(() => dispatch(setOrderProducts(orderProducts)))
        .finally(() => setLoading(false));
    }
  }

  const goToProduct = (item) => {
    dispatch(
      addMenu({
        id: `product-${item.uuid}`,
        url: `product/${item.uuid}`,
        name: t('edit.product'),
      })
    );
    navigate(`/product/${item.uuid}`);
  };

  function handleCheckCoupon() {
    if (!coupon) {
      return;
    }
    setLoadingCoupon(true);
    invokableService
      .checkCoupon(coupon)
      .then((res) => {
        const coupon = res.data;
        dispatch(setOrderData({ coupon }));
        dispatch(
          verifyOrderCoupon({
            price: res.data.price,
            verified: true,
          })
        );
      })
      .catch(() =>
        dispatch(
          verifyOrderCoupon({
            price: 0,
            verified: false,
          })
        )
      )
      .finally(() => setLoadingCoupon(false));
  }

  const handleChangeProductQuantity = (quantity, id) => {
    dispatch(changeOrderedProductQuantity({ quantity, id }));
  };

  const fetchShop = (uuid) => {
    shopService.getById(uuid).then((res) => {
      dispatch(
        setMenuData({
          activeMenu,
          data: { ...activeMenu.data, shop: res.data },
        })
      );
    });
  };

  return (
    <div className='order-items'>
      {loading && (
        <div className='loader'>
          <Spin />
        </div>
      )}
      <Row gutter={24} className='mt-4'>
        <Col span={24}>
          <Card className='shop-card'>
            {orderItems?.map((item, index) =>
              item.bonus === undefined || item.bonus === false ? (
                <div>
                  <Card className='position-relative'>
                    <CloseOutlined
                      className='close-order'
                      onClick={() => dispatch(removeFromOrder(item))}
                    />
                    <Space className='mr-3'>
                      <div className='order-item-img'>
                        <img
                          src={getImage(item?.img)}
                          alt={item.translation?.title}
                        />
                      </div>
                      <Meta
                        title={
                          <div>
                            <Space>
                              <div
                                className='cursor-pointer white-space-wrap'
                                onClick={() => goToProduct(item)}
                              >
                                {item.translation?.title}
                              </div>
                              <Button
                                icon={<EditOutlined />}
                                type='text'
                                size='small'
                                onClick={() => setExtrasModal(item)}
                              />
                            </Space>
                            <div className='product-price'>
                              {numberToPrice(item.price, data.currency.symbol)}
                            </div>
                          </div>
                        }
                        description={
                          <>
                            <Space>
                              <Button
                                disabled={item.quantity < item.min_qty + 1}
                                onClick={() =>
                                  handleChangeProductQuantity(
                                    item.quantity - 1,
                                    item.id
                                  )
                                }
                                type='primary'
                                icon={<MinusOutlined />}
                              />{' '}
                              {item.quantity}{' '}
                              <Button
                                onClick={() =>
                                  handleChangeProductQuantity(
                                    item.quantity + 1,
                                    item.id
                                  )
                                }
                                type='primary'
                                icon={<PlusOutlined />}
                              />
                            </Space>
                            <div className='mt-2'>
                              <Space wrap>
                                {item?.addons?.map((addon) => (
                                  <span
                                    key={addon.id}
                                    className='extras-text rounded'
                                  >
                                    {addon?.stock?.product?.translation
                                      ?.title ||
                                      addon?.product.translation?.title}{' '}
                                    x {addon?.quantity}
                                  </span>
                                ))}
                              </Space>
                            </div>
                          </>
                        }
                      />
                    </Space>
                  </Card>
                </div>
              ) : (
                <div key={index}>
                  <div>
                    <Card className='position-relative'>
                      <Space className='mr-3 w-100 justify-content-between align-items-start'>
                        <Space>
                          <div className='order-item-img'>
                            <img
                              src={getImage(item?.img)}
                              alt={item.translation?.title}
                            />
                          </div>
                          <Meta
                            title={
                              <div>
                                <div
                                  className='cursor-pointer white-space-wrap'
                                  onClick={() => goToProduct(item)}
                                >
                                  {item.translation?.title}
                                </div>
                                <div className='product-price'>
                                  {numberToPrice(
                                    item.price,
                                    data.currency?.symbol
                                  )}
                                </div>
                              </div>
                            }
                          />
                        </Space>
                        <div className='bonus'>
                          <BsFillGiftFill /> Bonus
                        </div>
                      </Space>
                    </Card>
                  </div>
                </div>
              )
            )}

            <div className='d-flex align-items-center justify-content-between'>
              <Space>
                <img
                  src={getImage(activeMenu.data?.shop?.logo_img)}
                  alt='logo'
                  width={40}
                  height={40}
                  className='rounded-circle'
                />
                <div>{activeMenu.data?.shop?.translation?.title}</div>
              </Space>
              <Space>
                <Input
                  placeholder={t('coupon')}
                  addonAfter={
                    coupon.verified ? (
                      <CheckOutlined style={{ color: '#18a695' }} />
                    ) : null
                  }
                  defaultValue={coupon.coupon}
                  onBlur={(event) =>
                    dispatch(
                      addOrderCoupon({
                        coupon: event.target.value,
                        user_id: data.user?.value,
                        shop_id: data.shop.value,
                        verified: false,
                      })
                    )
                  }
                />
                <Button
                  onClick={() => handleCheckCoupon(data.shop.id)}
                  loading={loadingCoupon}
                >
                  {t('check.coupon')}
                </Button>
              </Space>

              <div className='mt-2 text-right shop-total'>
                <Space>
                  <p className='font-weight-bold'>{t('product.tax')}:</p>
                  <p>
                    {numberToPrice(total.product_tax, data.currency?.symbol)}
                  </p>
                </Space>
                <div />
                <Space>
                  <p className='font-weight-bold'>{t('shop.tax')}:</p>
                  <p>{numberToPrice(total.shop_tax, data.currency?.symbol)}</p>
                </Space>
                <div />
                <Space>
                  <p className='font-weight-bold'>{t('delivery.fee')}:</p>
                  <p>
                    {numberToPrice(total.delivery_fee, data.currency?.symbol)}
                  </p>
                </Space>
                <div />
                <Space>
                  <p className='font-weight-bold'>{t('total')}:</p>
                  <p>
                    {numberToPrice(total.order_total, data.currency?.symbol)}
                  </p>
                </Space>
              </div>
            </div>
          </Card>
        </Col>
      </Row>
      {extrasModal && (
        <ExtrasModal
          extrasModal={extrasModal}
          setExtrasModal={setExtrasModal}
        />
      )}
    </div>
  );
}
