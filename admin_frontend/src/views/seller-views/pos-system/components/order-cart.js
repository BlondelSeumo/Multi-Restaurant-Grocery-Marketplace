import React, { useState } from 'react';
import {
  CheckOutlined,
  DeleteOutlined,
  MinusOutlined,
  PlusOutlined,
} from '@ant-design/icons';
import { Button, Card, Col, Form, Image, Input, Row, Space, Spin } from 'antd';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import {
  addToCart,
  clearCart,
  removeFromCart,
  reduceCart,
  setCartShops,
  clearCartShops,
  setCartTotal,
  addCoupon,
  verifyCoupon,
  removeBag,
} from '../../../../redux/slices/cart';
import getImage from '../../../../helpers/getImage';
import useDidUpdate from '../../../../helpers/useDidUpdate';
import orderService from '../../../../services/seller/order';
import invokableService from '../../../../services/rest/invokable';
import { useTranslation } from 'react-i18next';
import numberToPrice from '../../../../helpers/numberToPrice';
import {
  getCartData,
  getCartItems,
} from '../../../../redux/selectors/cartSelector';
import PreviewInfo from './preview-info';
import { toast } from 'react-toastify';
import { fetchSellerProducts } from '../../../../redux/slices/product';
import transactionService from '../../../../services/transaction';

import moment from 'moment';
import QueryString from 'qs';

export default function OrderCart() {
  const { t } = useTranslation();
  const dispatch = useDispatch();
  const { cartItems, cartShops, currentBag, total, coupons, currency } =
    useSelector((state) => state.cart, shallowEqual);
  const filteredCartItems = useSelector((state) => getCartItems(state.cart));
  const data = useSelector((state) => getCartData(state.cart), shallowEqual);
  const [loading, setLoading] = useState(false);
  const [orderId, setOrderId] = useState(null);
  const [loadingCoupon, setLoadingCoupon] = useState(null);
  const [form] = Form.useForm();
  const { myShop: shop } = useSelector((state) => state.myShop, shallowEqual);

  const deleteCard = (e) => dispatch(removeFromCart(e));
  const clearAll = () => {
    dispatch(clearCart());
    if (currentBag !== 0) {
      dispatch(removeBag(currentBag));
    }
  };

  const increment = (item) => {
    if (item.quantity === item?.stock?.quantity) {
      return;
    }
    if (item.quantity === item.max_qty) {
      return;
    }
    dispatch(addToCart({ ...item, quantity: 1 }));
  };

  const decrement = (item) => {
    if (item.quantity === 1) {
      return;
    }
    if (item.quantity <= item.min_qty) {
      return;
    }
    dispatch(reduceCart({ ...item, quantity: 1 }));
  };

  function formatProducts(list) {
    const addons = list.map((item) => ({
      quantity: item.quantity,
      stock_id: item.stockID ? item.stockID?.id : item.stock?.id,
    }));

    const products = list.flatMap((item) =>
      item.addons.map((addon) => ({
        quantity: addon.quantity,
        stock_id: addon.stockID,
        parent_id: item.stockID ? item.stockID?.id : item.stock?.id,
      }))
    );

    const combine = addons.concat(products);

    const result = {
      products: combine,
      currency_id: currency?.id,
      coupon: data?.coupon?.name,
      shop_id: shop.id,
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
      fetchSellerProducts({
        perPage: 12,
        currency_id: currency?.id,
        status: 'published',
      })
    );
    if (filteredCartItems.length) {
      productCalculate();
    }
  }, [currency]);

  useDidUpdate(() => {
    if (filteredCartItems.length) {
      productCalculate();
    } else {
      dispatch(clearCartShops());
    }
  }, [cartItems, currentBag, data?.address, currency]);

  function productCalculate() {
    const products = formatProducts(filteredCartItems);

    setLoading(true);
    orderService
      .calculate(products)
      .then(({ data }) => {
        const product = data.data;
        const items = product.stocks.map((item) => ({
          ...filteredCartItems.find((el) => el.id === item.id),
          ...item,
          ...item.stock.countable,
          stock: item.stock.stock_extras,
          stocks: item.stock.stock_extras,
          stockID: item.stock,
        }));
        dispatch(setCartShops(items));
        const orderData = {
          product_total: product.stocks?.reduce(
            (acc, curr) => acc + (curr.total_price || curr.price),
            0
          ),
          product_tax: product.total_tax,
          shop_tax: product.total_shop_tax,
          order_total: product.total_price,
          delivery_fee: product.delivery_fee,
        };
        dispatch(setCartTotal(orderData));
      })
      .finally(() => setLoading(false));
  }

  const handleSave = (id) => {
    setOrderId(id);
    dispatch(
      fetchSellerProducts({
        perPage: 12,
        currency_id: currency?.id,
        status: 'published',
      })
    );
  };

  const handleCloseInvoice = () => {
    setOrderId(null);
    clearAll();
    toast.success(t('successfully.closed'));
  };

  function handleCheckCoupon(shopId) {
    let coupon = coupons.find((item) => item.shop_id === shopId);
    if (!coupon) {
      return;
    }
    setLoadingCoupon(shopId);
    invokableService
      .checkCoupon(coupon)
      .then((res) =>
        dispatch(
          verifyCoupon({
            shop_id: shopId,
            price: res.data.price,
            verified: true,
          })
        )
      )
      .catch(() =>
        dispatch(
          verifyCoupon({
            shop_id: shopId,
            price: 0,
            verified: false,
          })
        )
      )
      .finally(() => setLoadingCoupon(null));
  }

  console.log(cartShops)

  const handleClick = () => {
    if (!currency) {
      toast.warning(t('please.select.currency'));
      return;
    }
    if (!data.address) {
      toast.warning(t('please.select.address'));
      return;
    }
    if (!data.delivery_time) {
      toast.warning(t('shop.closed'));
      return;
    }
    if (!data.delivery_date) {
      toast.warning(t('please.select.deliveryDate'));
      return;
    }
    setLoading(true);
    const products = cartShops?.map((cart) => ({
      stock_id: cart.stockID.id,
      quantity: cart.countable_quantity,
      bonus: cart.bonus,
    }));
    const addons = cartShops?.flatMap((product) =>
      product.addons?.map((addon) => ({
        stock_id: addon.id,
        quantity: addon.quantity,
        parent_id: product.stockID.id,
      }))
    );
    const body = {
      user_id: data.user?.value,
      currency_id: currency?.id,
      rate: currency?.rate,
      shop_id: shop.id,
      delivery_id: data.deliveries.label,
      delivery_fee: data.delivery_fee,
      coupon: coupons[0]?.coupon,
      tax: total.order_tax,
      payment_type: data.paymentType?.label,
      delivery_date: data.delivery_date,
      delivery_address_id: data.address?.address,
      address: {
        address: data.address?.address,
        office: null,
        house: null,
        floor: null,
      },
      location: {
        latitude: data.address?.lat,
        longitude: data.address?.lng,
      },
      delivery_time: moment(data.delivery_time, 'HH:mm').format('HH:mm'),
      delivery_type: data.deliveries.label.toLowerCase(),
      delivery_type_id: data.deliveries.value,
      products: products.concat(...addons),
    };

    const payment = {
      payment_sys_id: data.paymentType.value,
    };

    orderService
      .create(body)
      .then((response) => {
        createTransaction(response.data.id, payment);
      })
      .catch((err) => console.error(err))
      .finally(() => setLoading(false));
  };

  function createTransaction(id, data) {
    transactionService
      .create(id, data)
      .then((res) => {
        handleSave(res.data.id);
        form.resetFields();
      })
      .finally(() => setLoading(false));
  }

  return (
    <Card>
      {loading && (
        <div className='loader'>
          <Spin />
        </div>
      )}
      <div className='card-save'>
        {cartShops.map((item) => (
          <div>
            {item.bonus !== true ? (
              <div className='custom-cart-container' key={item.id}>
                <Row className='product-row'>
                  <Image
                    width={70}
                    height='auto'
                    src={getImage(item.img)}
                    preview
                    placeholder
                    className='rounded'
                  />
                  <Col span={18} className='product-col'>
                    <div>
                      <span className='product-name'>
                        {item?.translation?.title}
                      </span>
                      <br />
                      <Space wrap className='mt-2'>
                        {item?.stock?.map((el) => {
                          return (
                            <span className='extras-text rounded pr-2 pl-2'>
                              {el.value}
                            </span>
                          );
                        })}
                      </Space>
                      <br />
                      <Space wrap className='mt-2'>
                        {item.addons?.map((addon) => {
                          return (
                            <span className='extras-text rounded pr-2 pl-2'>
                              {addon?.countable?.translation?.title} x{' '}
                              {addon.quantity}
                            </span>
                          );
                        })}
                      </Space>
                      <div className='product-counter'>
                        <span>
                          <span>
                            {numberToPrice(
                              item?.total_price || item?.price,
                              currency?.symbol
                            )}
                          </span>
                        </span>

                        <div className='count'>
                          <Button
                            className='button-counter'
                            shape='circle'
                            icon={<MinusOutlined size={14} />}
                            onClick={() => decrement(item)}
                          />
                          <span>{item.countable_quantity}</span>
                          <Button
                            className='button-counter'
                            shape='circle'
                            icon={<PlusOutlined size={14} />}
                            onClick={() => increment(item)}
                          />
                          <Button
                            className='button-counter'
                            shape='circle'
                            onClick={() => deleteCard(item)}
                            icon={<DeleteOutlined size={14} />}
                          />
                        </div>
                      </div>
                    </div>
                  </Col>
                </Row>
              </div>
            ) : (
              <>
                <h4 className='mt-2'>{t('bomus.product')}</h4>
                <div className='custom-cart-container' key={item.id}>
                  <Row className='product-row'>
                    <Image
                      width={70}
                      height='auto'
                      src={getImage(item.img)}
                      preview
                      placeholder
                      className='rounded'
                    />
                    <Col span={18} className='product-col'>
                      <div>
                        <span className='product-name'>
                          {item?.translation?.title}
                        </span>
                        <br />
                        <Space wrap className='mt-2'>
                          {item?.stock?.map((el) => {
                            return (
                              <span className='extras-text rounded pr-2 pl-2'>
                                {el.value}
                              </span>
                            );
                          })}
                        </Space>
                        <br />
                        <Space wrap className='mt-2'>
                          {item.addons?.map((addon) => {
                            return (
                              <span className='extras-text rounded pr-2 pl-2'>
                                {addon?.countable?.translation?.title} x{' '}
                                {addon.quantity}
                              </span>
                            );
                          })}
                        </Space>
                        <div className='product-counter'>
                          <span>
                            {numberToPrice(
                              item?.total_price || item?.price,
                              currency?.symbol
                            )}
                          </span>

                          <div className='count'>
                            <Button
                              className='button-counter'
                              shape='circle'
                              icon={<MinusOutlined size={14} />}
                              onClick={() => decrement(item)}
                              disabled
                            />
                            <span>{item.countable_quantity}</span>
                            <Button
                              className='button-counter'
                              shape='circle'
                              icon={<PlusOutlined size={14} />}
                              onClick={() => increment(item)}
                              disabled
                            />
                          </div>
                        </div>
                      </div>
                    </Col>
                  </Row>
                </div>
              </>
            )}
          </div>
        ))}
        {cartShops.length ? (
          <div className='d-flex my-3'>
            <Input
              placeholder={t('coupon')}
              className='w-100 mr-2'
              addonAfter={
                coupons.find((el) => el.shop_id === cartShops[0].shop_id)
                  ?.verified ? (
                  <CheckOutlined style={{ color: '#18a695' }} />
                ) : null
              }
              defaultValue={
                coupons.find((el) => el.shop_id === cartShops[0].shop_id)
                  ?.coupon
              }
              onBlur={(event) =>
                dispatch(
                  addCoupon({
                    coupon: event.target.value,
                    user_id: data.user?.value,
                    shop_id: cartShops[0].shop_id,
                    verified: false,
                  })
                )
              }
            />
            <Button
              onClick={() => handleCheckCoupon(cartShops[0].shop_id)}
              loading={loadingCoupon === cartShops[0].shop_id}
            >
              {t('check.coupon')}
            </Button>
          </div>
        ) : (
          ''
        )}

        <Row className='all-price-row'>
          <Col span={24} className='col'>
            <div className='all-price-container'>
              <span>{t('sub.total')}</span>
              <span>
                {numberToPrice(total.product_total, currency?.symbol)}
              </span>
            </div>
            <div className='all-price-container'>
              <span>{t('product.tax')}</span>
              <span>{numberToPrice(total.product_tax, currency?.symbol)}</span>
            </div>
            <div className='all-price-container'>
              <span>{t('shop.tax')}</span>
              <span>{numberToPrice(total.shop_tax, currency?.symbol)}</span>
            </div>
            <div className='all-price-container'>
              <span>{t('delivery.fee')}</span>
              <span>{numberToPrice(total.delivery_fee, currency?.symbol)}</span>
            </div>
            {/* <div className='all-price-container'>
              <span>{t('cashback')}</span>
              <span>{numberToPrice(total?.cashback, currency?.symbol)}</span>
            </div> */}
          </Col>
        </Row>

        <Row className='submit-row'>
          <Col span={14} className='col'>
            <span>{t('total.amount')}</span>
            <span>{numberToPrice(total.order_total, currency?.symbol)}</span>
          </Col>
          <Col className='col2'>
            <Button
              type='primary'
              onClick={() => handleClick()}
              disabled={!cartShops.length}
            >
              {t('place.order')}
            </Button>
          </Col>
        </Row>
      </div>
      {orderId ? (
        <PreviewInfo orderId={orderId} handleClose={handleCloseInvoice} />
      ) : (
        ''
      )}
    </Card>
  );
}
