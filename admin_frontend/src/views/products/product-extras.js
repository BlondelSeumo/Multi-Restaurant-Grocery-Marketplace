import React, { useState, useEffect } from 'react';
import { Spin, Form, Row, Col, Checkbox, Button, Space, message } from 'antd';
import extraService from '../../services/extra';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import productService from '../../services/product';
import { setMenuData } from '../../redux/slices/menu';
import { useTranslation } from 'react-i18next';
import { useParams } from 'react-router-dom';

const ProductExtras = ({ next, prev }) => {
  const { t } = useTranslation();
  const dispatch = useDispatch();
  const { uuid } = useParams();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);

  const [extrasGroup, setExtrasGroup] = useState([]);
  const [loading, setLoading] = useState(false);
  const [loadingBtn, setLoadingBtn] = useState(false);

  const extraGroup = () => {
    setLoading(true);
    const params = { valid: true };
    extraService
      .getAllGroups(params)
      .then((res) => {
        const data = res.data.map((item) => ({
          id: item.id,
          label: item.translation?.title,
          value: item.id,
        }));
        setExtrasGroup(data);
      })
      .finally(() => setLoading(false));
  };

  useEffect(() => {
    extraGroup();
  }, []);

  const onFinish = (values) => {
    setLoadingBtn(true);
    const extras = values.extras || [];
    extras.sort((a, b) => a - b);
    productService
      .extras(uuid, { extras })
      .then(() => {
        dispatch(
          setMenuData({
            activeMenu,
            data: { ...activeMenu.data, extras },
          })
        );
        message.success(t('product.extras.saved'));
        next();
      })
      .finally(() => setLoadingBtn(false));
  };

  return (
    <Form
      layout='vertical'
      initialValues={{ ...activeMenu.data }}
      onFinish={onFinish}
    >
      {!loading ? (
        <>
          <Row gutter={12}>
            <Col span={12}>
              <Form.Item label={t('extras')} name='extras'>
                <Checkbox.Group options={extrasGroup} />
              </Form.Item>
            </Col>
          </Row>
          <Space>
            <Button onClick={prev}>{t('prev')}</Button>
            <Button type='primary' htmlType='submit' loading={loadingBtn}>
              {t('next')}
            </Button>
          </Space>
        </>
      ) : (
        <div className='d-flex justify-content-center align-items-center'>
          <Spin size='large' className='py-5' />
        </div>
      )}
    </Form>
  );
};
export default ProductExtras;
