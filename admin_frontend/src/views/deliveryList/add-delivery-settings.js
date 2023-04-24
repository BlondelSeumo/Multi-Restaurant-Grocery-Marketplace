import { Button, Col, Form, Input, Modal, Row, Select, Space } from 'antd';
import React, { useState, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import deliveryService from '../../services/delivery';
import Loading from '../../components/loading';
import { fetchDelivery } from '../../redux/slices/deliveries';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
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

const DelivertSettingCreate = ({ data, handleCancel }) => {
  const { t } = useTranslation();
  const [form] = Form.useForm();
  const dispatch = useDispatch();
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
      images: image.map((img) => img.name),
      location: {
        latitude: location.lat,
        longitude: location.lng,
      },
    };
    if (data.settingsId) {
      deliveryService
        .update(data.settingsId, params)
        .then(() => {
          handleCancel();
          dispatch(fetchDelivery());
        })
        .finally(() => setLoadingBtn(true));
    } else {
      deliveryService
        .create(params)
        .then(() => {
          handleCancel();
          dispatch(fetchDelivery());
        })
        .finally(() => setLoadingBtn(true));
    }
  };

  useEffect(() => {
    if (data.settingsId) {
      fetchDeliverySettings(data.settingsId);
    }
  }, []);

  return (
    <>
      <Modal
        visible={!!data}
        title={data.id ? t('edit.delivery.setting') : t('add.delivery.setting')}
        closable={false}
        style={{ minWidth: '80vw' }}
        footer={[
          <Space>
            <Button
              type='primary'
              htmlType='submit'
              key={'submit'}
              onClick={() => form.submit()}
              loading={loadingBtn}
            >
              {t('submit')}
            </Button>
            <Button type='default' key={'cancelBtn'} onClick={handleCancel}>
              {t('cancel')}
            </Button>
          </Space>,
        ]}
      >
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
            </Row>
          )}
        </Form>
      </Modal>
    </>
  );
};

export default DelivertSettingCreate;
