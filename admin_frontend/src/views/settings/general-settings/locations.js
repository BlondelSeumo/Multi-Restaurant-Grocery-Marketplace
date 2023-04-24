import { Button, Card, Col, Form, Input, Row } from 'antd';
import React, { useState } from 'react';
import { useTranslation } from 'react-i18next';
import { useDispatch } from 'react-redux';
import { shallowEqual, useSelector } from 'react-redux';
import { toast } from 'react-toastify';
import Map from '../../../components/map';
import settingService from '../../../services/settings';
import { fetchSettings as getSettings } from '../../../redux/slices/globalSettings';
import useDemo from '../../../helpers/useDemo';

const Locations = ({ location, setLocation }) => {
  const { t } = useTranslation();
  const [form] = Form.useForm();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const dispatch = useDispatch();
  const [loadingBtn, setLoadingBtn] = useState(false);
  const { isDemo } = useDemo();

  function updateSettings(data) {
    setLoadingBtn(true);
    settingService
      .update(data)
      .then(() => {
        toast.success(t('successfully.updated'));
        dispatch(getSettings());
      })
      .finally(() => setLoadingBtn(false));
  }

  const onFinish = (values) => {
    const body = {
      ...values,
      location: `${location.lat}, ${location.lng}`,
    };
    updateSettings(body);
  };

  return (
    <Form
      layout='vertical'
      form={form}
      name='global-settings'
      onFinish={onFinish}
      initialValues={{
        ...activeMenu.data,
      }}
    >
      <Card>
        <Row>
          <Col span={24}>
            <Form.Item
              label={t('google.map.key')}
              name='google_map_key'
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
          <Col span={24}>
            <Form.Item
              label={t('map')}
              name='location'
              style={{ borderRadius: '50px' }}
            >
              <Map location={location} setLocation={setLocation} />
            </Form.Item>
          </Col>
        </Row>
        <Button
          type='primary'
          onClick={() => form.submit()}
          loading={loadingBtn}
          disabled={isDemo}
        >
          {t('save')}
        </Button>
      </Card>
    </Form>
  );
};

export default Locations;
