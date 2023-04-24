import React, { useEffect, useState } from 'react';
import { Card, Col, Row } from 'antd';
import { DebounceSelect } from '../../../components/search';
import shopService from '../../../services/shop';
import brandService from '../../../services/brand';
import categoryService from '../../../services/category';
import useDidUpdate from '../../../helpers/useDidUpdate';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { fetchRestProducts } from '../../../redux/slices/product';
import SearchInput from '../../../components/search-input';
import { useTranslation } from 'react-i18next';
import { clearCart, setCartData } from '../../../redux/slices/cart';
import { fetchRestPayments } from '../../../redux/slices/payment';
import { disableRefetch } from '../../../redux/slices/menu';
import { getCartData } from '../../../redux/selectors/cartSelector';
import restPaymentService from '../../../services/rest/payment';

const Filter = () => {
  const { t } = useTranslation();
  const dispatch = useDispatch();
  const [brand, setBrand] = useState(null);
  const [category, setCategory] = useState(null);
  const [search, setSearch] = useState(null);
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const { allShops } = useSelector((state) => state.allShops, shallowEqual);
  const { currentBag } = useSelector((state) => state.cart, shallowEqual);

  const activeShop = getFirstShopFromList(allShops[0]);
  const cartData = useSelector((state) => getCartData(state.cart));

  async function fetchUserShop(search) {
    const params = { search, status: 'approved' };
    return shopService.search(params).then((res) =>
      res.data.map((item) => ({
        label: item.translation !== null ? item.translation.title : 'no name',
        value: item.id,
      }))
    );
  }

  async function fetchUserBrand(username) {
    return brandService.search(username).then((res) =>
      res.data.map((item) => ({
        label: item.title,
        value: item.id,
      }))
    );
  }

  async function fetchUserCategory(search) {
    const params = { search, type: 'main' };
    return categoryService.search(params).then((res) =>
      res.data.map((item) => ({
        label: item.translation !== null ? item.translation.title : 'no name',
        value: item.id,
      }))
    );
  }

  async function fetchSellerPayments() {
    const params = cartData?.shop.value;
    return restPaymentService.getById(params).then((res) => {
      dispatch(
        setCartData({
          bag_id: currentBag,
          payment_type: res.data.map((item) => item.payment),
        })
      );
    });
  }

  function getFirstShopFromList(shop) {
    return {
      label: shop?.translation?.title,
      value: shop?.id,
    };
  }

  useDidUpdate(() => {
    const params = {
      brand_id: brand?.value,
      category_id: category?.value,
      search,
      shop_id: cartData?.shop?.value,
      status: 'published',
    };
    dispatch(fetchRestProducts(params));
    fetchSellerPayments();
  }, [brand, category, search, cartData.shop]);

  const selectShop = () => dispatch(clearCart());

  useEffect(() => {
    const body = {
      shop_id: activeShop?.value,
    };
    if (activeMenu.refetch) {
      dispatch(fetchRestPayments(body));
      dispatch(setCartData({ bag_id: currentBag, shop: activeShop }));
      dispatch(disableRefetch(activeMenu));
    }
  }, [activeMenu.refetch]);

  return (
    <Card>
      <Row gutter={12}>
        <Col span={6}>
          <SearchInput
            className='w-100'
            placeholder={t('search')}
            handleChange={setSearch}
          />
        </Col>
        <Col span={6}>
          <DebounceSelect
            className='w-100'
            debounceTimeout={500}
            placeholder={t('select.shop')}
            fetchOptions={fetchUserShop}
            allowClear={false}
            onChange={(value) => {
              dispatch(setCartData({ bag_id: currentBag, shop: value }));
              selectShop();
            }}
            value={cartData?.shop}
          />
        </Col>
        <Col span={6}>
          <DebounceSelect
            className='w-100'
            placeholder={t('select.category')}
            fetchOptions={fetchUserCategory}
            onChange={(value) => setCategory(value)}
            value={category}
          />
        </Col>
        <Col span={6}>
          <DebounceSelect
            className='w-100'
            placeholder={t('select.brand')}
            fetchOptions={fetchUserBrand}
            onChange={(value) => setBrand(value)}
            value={brand}
          />
        </Col>
      </Row>
    </Card>
  );
};
export default Filter;
