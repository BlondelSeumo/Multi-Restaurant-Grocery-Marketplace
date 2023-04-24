import React, { useState, useEffect } from 'react';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import LanguageList from '../../components/language-list';
import { useTranslation } from 'react-i18next';
import { steps } from './steps';
import { Card, Steps } from 'antd';
import ShopMain from './main';
import UserEdit from './user';
import { useQueryParams } from '../../helpers/useQueryParams';
import { disableRefetch, setMenuData } from '../../redux/slices/menu';
import shopService from '../../services/shop';
import { useParams } from 'react-router-dom';
import ShopDelivery from './shopDelivery';
import Loading from '../../components/loading';
import Map from '../../components/shop/map';
const { Step } = Steps;

const ShopsEdit = () => {
  const { t } = useTranslation();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const queryParams = useQueryParams();
  const current = Number(queryParams.values?.step || 0);
  const [loading, setLoading] = useState(activeMenu.refetch);
  const dispatch = useDispatch();
  const { uuid } = useParams();

  const { languages } = useSelector((state) => state.formLang, shallowEqual);

  const next = () => {
    const step = current + 1;
    queryParams.set('step', step);
  };
  const prev = () => {
    const step = current - 1;
    queryParams.set('step', step);
  };

  const onChange = (step) => {
    dispatch(setMenuData({ activeMenu, data: { ...activeMenu.data, step } }));
    queryParams.set('step', step);
  };

  const fetchShop = (uuid) => {
    setLoading(true);
    shopService
      .getById(uuid)
      .then((res) => {
        const data = {
          ...res.data,
          ...getLanguageFields(res.data),
          logo_img: createImages(res.data.logo_img),
          background_img: createImages(res.data.background_img),
          user: {
            label: res.data.seller.firstname + ' ' + res.data.seller.lastname,
            value: res.data.seller.id,
          },
          delivery_time_from: res.data?.delivery_time.from,
          delivery_time_to: res.data?.delivery_time.to,
          delivery_time_type: res.data?.delivery_time.type,
          recommended: res.data.mark === 'recommended',
          categories: res.data?.categories?.map((item) => ({
            label: item?.translation?.title,
            value: item.id,
            key: item.id,
          })),
          tags: res.data?.tags?.map((item) => ({
            label: item?.translation?.title,
            value: item.id,
            key: item.id,
          })),
        };
        dispatch(setMenuData({ activeMenu, data }));
      })
      .finally(() => {
        setLoading(false);
        dispatch(disableRefetch(activeMenu));
      });
  };

  const createImages = (items) => {
    return {
      items,
      uid: items,
      url: items,
      name: items,
    };
  };

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
      [`address[${item.locale}]`]: translations.find(
        (el) => el.locale === item.locale
      )?.address,
    }));
    return Object.assign({}, ...result);
  }

  useEffect(() => {
    if (activeMenu.refetch && uuid) {
      fetchShop(uuid);
    }
  }, [activeMenu.refetch, current]);

  return (
    <Card title={t('shop.edit')} extra={<LanguageList />}>
      <Steps current={current} onChange={onChange}>
        {steps.map((item) => (
          <Step title={t(item.title)} key={item.title} />
        ))}
      </Steps>
      {!loading ? (
        <div className='steps-content'>
          {steps[current].content === 'First-content' && (
            <ShopMain
              next={next}
              loading={loading}
              action_type={'edit'}
              user={false}
            />
          )}

          {steps[current].content === 'Second-content' && (
            <Map next={next} prev={prev} />
          )}

          {steps[current].content === 'Third-content' && (
            <ShopDelivery next={next} prev={prev} />
          )}

          {steps[current].content === 'Four-content' && (
            <UserEdit next={next} prev={prev} />
          )}
        </div>
      ) : (
        <Loading />
      )}
    </Card>
  );
};
export default ShopsEdit;
