import React, { useState, useEffect } from 'react';
import { Button, Card, Col, Form, Input, Row, Select, Switch } from 'antd';
import { toast } from 'react-toastify';
import { useNavigate } from 'react-router-dom';
import LanguageList from '../../../components/language-list';
import TextArea from 'antd/es/input/TextArea';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { removeFromMenu, setMenuData } from '../../../redux/slices/menu';
import categoryService from '../../../services/seller/category';
import MediaUpload from '../../../components/upload';
import { fetchSellerCategory } from '../../../redux/slices/category';
import { useTranslation } from 'react-i18next';

const SellerCategoryAdd = () => {
  const { t } = useTranslation();
  const navigate = useNavigate();
  const dispatch = useDispatch();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);

  const [image, setImage] = useState(
    activeMenu.data?.image ? [activeMenu.data?.image] : []
  );
  const [form] = Form.useForm();
  const [loadingBtn, setLoadingBtn] = useState(false);
  const [error, setError] = useState(null);

  const { defaultLang, languages } = useSelector(
    (state) => state.formLang,
    shallowEqual
  );

  useEffect(() => {
    return () => {
      const data = form.getFieldsValue(true);
      dispatch(setMenuData({ activeMenu, data }));
    };
  }, []);

  const onFinish = (values) => {
    setLoadingBtn(true);
    const body = {
      ...values,
      type: 'main',
      active: values.active ? 1 : 0,
      keywords: values.keywords.join(','),
      parent_id: null,
      'images[0]': image[0]?.name,
    };
    const nextUrl = 'seller/categories';
    categoryService
      .create(body)
      .then(() => {
        toast.success(t('successfully.created'));
        dispatch(removeFromMenu({ ...activeMenu, nextUrl }));
        dispatch(fetchSellerCategory());
        navigate(`/${nextUrl}`);
      })
      .catch((err) => setError(err.response.data.params))
      .finally(() => setLoadingBtn(false));
  };

  // async function fetchUserCategoryList() {
  //   const params = { perPage: 100, type: 'main' };
  //   return categoryService.getAll(params).then((res) =>
  //     res.data.map((item) => ({
  //       title: item.translation?.title,
  //       value: item.id,
  //       key: item.id,
  //       children: item.children?.map((el) => ({
  //         title: el.translation?.title,
  //         value: el.id,
  //         key: el.id,
  //         children: el.children?.map((three) => ({
  //           title: three.translation?.title,
  //           value: three.id,
  //           key: three.id,
  //           disabled: true,
  //         })),
  //       })),
  //     }))
  //   );
  // }

  return (
    <Card title={t('add.category')} extra={<LanguageList />}>
      <Form
        name='basic'
        layout='vertical'
        onFinish={onFinish}
        initialValues={{
          parent_id: { title: '---', value: 0, key: 0 },
          active: true,
          ...activeMenu.data,
        }}
        form={form}
      >
        <Row gutter={12}>
          <Col span={12}>
            {languages.map((item, index) => (
              <Form.Item
                key={item.title + index}
                label={t('name')}
                name={`title[${item.locale}]`}
                help={
                  error
                    ? error[`title.${defaultLang}`]
                      ? error[`title.${defaultLang}`][0]
                      : null
                    : null
                }
                validateStatus={error ? 'error' : 'success'}
                rules={[
                  {
                    required: item.locale === defaultLang,
                    message: t('required'),
                  },
                ]}
                hidden={item.locale !== defaultLang}
              >
                <Input placeholder={t('name')} />
              </Form.Item>
            ))}
          </Col>

          <Col span={12}>
            {languages.map((item, index) => (
              <Form.Item
                key={item.locale + index}
                label={t('description')}
                name={`description[${item.locale}]`}
                rules={[
                  {
                    required: item.locale === defaultLang,
                    message: t('required'),
                  },
                ]}
                hidden={item.locale !== defaultLang}
              >
                <TextArea rows={4} />
              </Form.Item>
            ))}
          </Col>

          <Col span={12}>
            <Form.Item
              label={t('keywords')}
              name='keywords'
              rules={[{ required: true, message: t('required') }]}
            >
              <Select mode='tags' style={{ width: '100%' }}></Select>
            </Form.Item>
          </Col>

          <Col span={4}>
            <Form.Item label={t('image')}>
              <MediaUpload
                type='categories'
                imageList={image}
                setImageList={setImage}
                form={form}
                multiple={false}
              />
            </Form.Item>
          </Col>
          <Col span={2}>
            <Form.Item
              label={t('active')}
              name='active'
              valuePropName='checked'
            >
              <Switch />
            </Form.Item>
          </Col>
        </Row>
        <Button type='primary' htmlType='submit' loading={loadingBtn}>
          {t('submit')}
        </Button>
      </Form>
    </Card>
  );
};
export default SellerCategoryAdd;
