import React, { useEffect, useState } from 'react';
import { Button, Form, Space } from 'antd';
import { useNavigate, useParams } from 'react-router-dom';
import ShopAddData from './shop-add-data';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { replaceMenu, setMenuData } from '../../redux/slices/menu';
import shopService from '../../services/shop';
import { useTranslation } from 'react-i18next';
import getDefaultLocation from '../../helpers/getDefaultLocation';

const ShopMain = ({ next, action_type = '', user }) => {
  const { t } = useTranslation();
  const [form] = Form.useForm();
  const navigate = useNavigate();
  const dispatch = useDispatch();
  const { uuid } = useParams();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const { settings } = useSelector(
    (state) => state.globalSettings,
    shallowEqual
  );

  const [location, setLocation] = useState(
    activeMenu?.data?.location
      ? {
          lat: parseFloat(activeMenu?.data?.location?.latitude),
          lng: parseFloat(activeMenu?.data?.location?.longitude),
        }
      : getDefaultLocation(settings)
  );

  const [loadingBtn, setLoadingBtn] = useState(false);
  const [logoImage, setLogoImage] = useState(
    activeMenu.data?.logo_img ? [activeMenu.data?.logo_img] : []
  );
  const [backImage, setBackImage] = useState(
    activeMenu.data?.background_img ? [activeMenu.data?.background_img] : []
  );
  const [error, setError] = useState(null);

  useEffect(() => {
    return () => {
      const data = form.getFieldsValue(true);
      data.open_time = JSON.stringify(data?.open_time);
      data.close_time = JSON.stringify(data?.close_time);
      dispatch(
        setMenuData({ activeMenu, data: { ...activeMenu.data, ...data } })
      );
    };
  }, []);

  const onFinish = (values) => {
    setLoadingBtn(true);
    const body = {
      ...values,
      'images[0]': logoImage[0]?.name,
      'images[1]': backImage[0]?.name,
      delivery_time_type: values.delivery_time_type,
      delivery_time_to: values.delivery_time_to,
      delivery_time_from: values.delivery_time_from,
      categories: values.categories.map((e) => e.value),
      tags: values.tags?.map((e) => e.value),
      user_id: values.user.value,
      open: undefined,
      'location[latitude]': location.lat,
      'location[longitude]': location.lng,
      user: undefined,
      delivery_time: 0,
      type: 'shop',
    };
    if (action_type === 'edit') {
      shopUpdate(values, body);
    } else {
      shopCreate(values, body);
    }
  };

  function shopCreate(values, params) {
    shopService
      .create(params)
      .then(({ data }) => {
        dispatch(
          replaceMenu({
            id: `shop-${data.uuid}`,
            url: `shop/${data.uuid}`,
            name: t('add.shop'),
            data: data,
            refetch: false,
          })
        );
        navigate(`/shop/${data.uuid}?step=1`);
      })
      .catch((err) => setError(err.response.data.params))
      .finally(() => setLoadingBtn(false));
  }

  function shopUpdate(values, params) {
    shopService
      .update(uuid, params)
      .then(() => {
        dispatch(
          setMenuData({
            activeMenu,
            data: values,
          })
        );
        next();
      })
      .catch((err) => setError(err.response.data.params))
      .finally(() => setLoadingBtn(false));
  }

  return (
    <>
      <Form
        form={form}
        name='basic'
        layout='vertical'
        onFinish={onFinish}
        initialValues={{
          open: false,
          status: 'new',
          ...activeMenu.data,
        }}
      >
        <ShopAddData
          logoImage={logoImage}
          setLogoImage={setLogoImage}
          backImage={backImage}
          setBackImage={setBackImage}
          form={form}
          location={location}
          setLocation={setLocation}
          error={error}
          user={user}
        />
        <Space>
          <Button type='primary' htmlType='submit' loading={loadingBtn}>
            {t('next')}
          </Button>
        </Space>
      </Form>
    </>
  );
};
export default ShopMain;
