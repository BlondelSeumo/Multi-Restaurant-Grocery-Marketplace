import { Button, Col, Form, Input, Row, Select } from 'antd';
import React, { useState, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import deliveryService from '../../services/delivery';
import Loading from '../../components/loading';
import { shallowEqual, useSelector } from 'react-redux';
import MediaUpload from '../../components/upload';
import Map from '../../components/map';
import getDefaultLocation from '../../helpers/getDefaultLocation';

const type_of_technique = [
  { label: 'Benzine', value: 'benzine' },
  { label: 'Diesel', value: 'diesel' },
  { label: 'Gas', value: 'gas' },
  { label: 'Motorbike', value: 'motorbike' },
  { label: 'Bike', value: 'bike' },
  { label: 'Foot', value: 'foot' },
  { label: 'Electric', value: 'electric' },
];

const DelivertSettingCreate = ({ id, data }) => {
  const { t } = useTranslation();
  const [form] = Form.useForm();
  const [loadingBtn, setLoadingBtn] = useState(false);
  const [loading, setLoading] = useState(false);
  const [image, setImage] = useState([]);
  const { settings } = useSelector(
    (state) => state.globalSettings,
    shallowEqual
  );
  const [location, setLocation] = useState(getDefaultLocation(settings));

  const createImages = (items) =>
    items.map((item) => ({
      uid: item.id,
      name: item.path,
      url: item.path,
    }));

  const fetchDeliverySettings = (id) => {
    setLoading(true);
    deliveryService
      .getById(id)
      .then((res) => {
        const responseData = {
          ...res.data,
          user_id: {
            label:
              res.data.deliveryMan.firstname +
              ' ' +
              res.data.deliveryMan.firstname,
            value: res.data.deliveryMan.id,
            images: createImages(res.data.galleries),
            location: {
              lat: res.data?.location?.latitude,
              lng: res.data?.location?.longitude,
            },
          },
        };
        setLocation({
          lat: res.data?.location?.latitude,
          lng: res.data?.location?.longitude,
        });
        setImage(createImages(res.data.galleries));
        form.setFieldsValue(responseData);
      })
      .finally(() => setLoading(false));
  };

  const onFinish = (values) => {
    setLoadingBtn(true);
    const params = {
      ...values,
      user_id: data.id,
      images: [image[0]?.name],
      location: {
        latitude: location.lat,
        longitude: location.lng,
      },
    };

    deliveryService.update(id, params).finally(() => setLoadingBtn(false));
  };

  useEffect(() => {
    if (id) {
      fetchDeliverySettings(id);
    }
  }, []);

  return (
    <Form name='basic' layout='vertical' onFinish={onFinish} form={form}>
      {loading ? (
        <Loading />
      ) : (
        <Row gutter={12}>
          <Col span={12}>
            <Form.Item
              label={t('brand')}
              name='brand'
              rules={[
                {
                  required: true,
                  message: t('required'),
                },
              ]}
            >
              <Input />
            </Form.Item>
          </Col>
          <Col span={12}>
            <Form.Item
              label={t('model')}
              name='model'
              rules={[
                {
                  required: true,
                  message: t('required'),
                },
              ]}
            >
              <Input />
            </Form.Item>
          </Col>
          <Col span={12}>
            <Form.Item
              label={t('type.of.technique')}
              name='type_of_technique'
              rules={[
                {
                  required: true,
                  message: t('required'),
                },
              ]}
            >
              <Select options={type_of_technique} />
            </Form.Item>
          </Col>
          <Col span={12}>
            <Form.Item
              label={t('car.number')}
              name='number'
              rules={[
                {
                  required: true,
                  message: t('required'),
                },
              ]}
            >
              <Input />
            </Form.Item>
          </Col>
          <Col span={12}>
            <Form.Item
              label={t('car.color')}
              name='color'
              rules={[
                {
                  required: true,
                  message: t('required'),
                },
              ]}
            >
              <Input />
            </Form.Item>
          </Col>
          <Col span={12}>
            <Form.Item label={t('image')} name='images'>
              <MediaUpload
                type='deliveryman/settings'
                imageList={image}
                setImageList={setImage}
                form={form}
                length='1'
                multiple={true}
              />
            </Form.Item>
          </Col>
          <Col span={24}>
            <Form.Item label={t('map')} name='location'>
              <Map location={location} setLocation={setLocation} />
            </Form.Item>
          </Col>
          <Col span={24}>
            <Button type='primary' htmlType='submit' loading={loadingBtn}>
              {t('save')}
            </Button>
          </Col>
        </Row>
      )}
    </Form>
  );
};

export default DelivertSettingCreate;
