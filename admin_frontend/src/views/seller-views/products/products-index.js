import React, { useEffect, useState } from 'react';
import { Button, Col, Form, Input, InputNumber, Row, Switch } from 'antd';
import { DebounceSelect } from '../../../components/search';
import brandService from '../../../services/rest/brand';
import categoryService from '../../../services/rest/category';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import productService from '../../../services/seller/product';
import { replaceMenu, setMenuData } from '../../../redux/slices/menu';
import unitService from '../../../services/seller/unit';
import { useNavigate, useParams } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { AsyncSelect } from '../../../components/async-select';
import { AsyncTreeSelect } from '../../../components/async-tree-select';
import MediaUpload from '../../../components/upload';
import TextArea from 'antd/lib/input/TextArea';

const ProductsIndex = ({ next, action_type = '' }) => {
  const { t } = useTranslation();
  const [form] = Form.useForm();
  const dispatch = useDispatch();
  const { uuid } = useParams();
  const navigate = useNavigate();
  const [error, setError] = useState(null);
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const { defaultLang, languages } = useSelector(
    (state) => state.formLang,
    shallowEqual
  );
  const { myShop } = useSelector((state) => state.myShop, shallowEqual);
  const [fileList, setFileList] = useState(
    activeMenu.data?.images ? activeMenu.data?.images : []
  );
  const [loadingBtn, setLoadingBtn] = useState(false);

  useEffect(() => {
    return () => {
      const data = form.getFieldsValue(true);
      dispatch(
        setMenuData({ activeMenu, data: { ...activeMenu.data, ...data } })
      );
    };
  }, []);

  function fetchUserBrandList(username) {
    const params = {
      search: username,
    };
    return brandService.getAll(params).then((res) =>
      res.data.map((item) => ({
        label: item.title,
        value: item.id,
      }))
    );
  }

  function fetchUserCategoryList(username) {
    const params = {
      search: username ? username : null,
      type: 'main',
    };
    return categoryService.getAll(params).then((res) =>
      res.data.map((item) => ({
        label: item.translation?.title,
        value: item.id,
        key: item.id,
        // disabled: item?.children.length ? true : false,
        // children: item.children?.map((el) => ({
        //   label: el.translation?.title,
        //   value: el.id,
        //   key: el.id,
        //   disabled: el.children.length ? true : false,
        //   children: el?.children?.map((three) => ({
        //     label: three.translation?.title,
        //     value: three.id,
        //     key: three.id,
        //     disabled: three.children.length ? true : false,
        //   })),
        // })),
      }))
    );
  }

  function fetchUnits(search) {
    const params = {
      search,
    };
    return unitService.getAll(params).then(({ data }) => formatUnits(data));
  }

  const onFinish = (values) => {
    setLoadingBtn(true);
    const params = {
      ...values,
      active: Number(values.active),
      brand_id: values.brand?.value,
      category_id: values.category?.value,
      unit_id: values.unit?.value,
      images: undefined,
      brand: undefined,
      category: undefined,
      unit: undefined,
      ...Object.assign(
        {},
        ...fileList.map((item, index) => ({
          [`images[${index}]`]: item.name,
        }))
      ),
    };
    if (action_type === 'edit') {
      productUpdate(values, params);
    } else {
      productCreate(values, params);
    }
  };

  function productCreate(values, params) {
    productService
      .create(params)
      .then(({ data }) => {
        dispatch(
          replaceMenu({
            id: `product-${data.uuid}`,
            url: `seller/product/${data.uuid}`,
            name: t('add.product'),
            data: values,
            refetch: false,
          })
        );
        navigate(`/seller/product/${data.uuid}?step=1`);
      })
      .catch((err) => setError(err.response.data.params))
      .finally(() => setLoadingBtn(false));
  }

  function productUpdate(values, params) {
    productService
      .update(uuid, params)
      .then(({ data }) => {
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

  function formatUnits(data) {
    return data.map((item) => ({
      label: item.translation?.title,
      value: item.id,
    }));
  }

  return (
    <Form
      layout='vertical'
      form={form}
      initialValues={{ active: true, ...activeMenu.data }}
      onFinish={onFinish}
    >
      <Row gutter={12}>
        <Col span={12}>
          {languages.map((item) => (
            <Form.Item
              key={'name' + item.id}
              label={t('name')}
              name={`title[${item.locale}]`}
              rules={[
                {
                  required: item.locale === defaultLang,
                  message: t('required'),
                },
              ]}
              hidden={item.locale !== defaultLang}
            >
              <Input />
            </Form.Item>
          ))}
        </Col>
        <Col span={12}>
          {languages.map((item) => (
            <Form.Item
              key={'description' + item.id}
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
              <TextArea rows={3} />
            </Form.Item>
          ))}
        </Col>
        {myShop.type === 'shop' && (
          <Col span={12}>
            <Form.Item
              label={t('brand')}
              name='brand'
              rules={[
                {
                  required: false,
                  message: t('required'),
                },
              ]}
            >
              <DebounceSelect fetchOptions={fetchUserBrandList} />
            </Form.Item>
          </Col>
        )}

        <Col span={12}>
          <Form.Item
            label={t('category')}
            name='category'
            rules={[{ required: true, message: t('required') }]}
          >
            <AsyncTreeSelect fetchOptions={fetchUserCategoryList} />
          </Form.Item>
        </Col>

        <Col span={6}>
          <Form.Item label={t('tax')} name='tax'>
            <InputNumber min={0} className='w-100' addonAfter='%' />
          </Form.Item>
        </Col>
        <Col span={6}>
          <Form.Item
            label={t('qr.code')}
            name='bar_code'
            rules={[{ required: true, message: t('required') }]}
            help={error?.bar_code ? error.bar_code[0] : null}
            validateStatus={error?.bar_code ? 'error' : 'success'}
          >
            <Input className='w-100' />
          </Form.Item>
        </Col>
        <Col span={6}>
          <Form.Item
            label={t('unit')}
            name='unit'
            rules={[{ required: true, message: t('required') }]}
          >
            <AsyncSelect fetchOptions={fetchUnits} />
          </Form.Item>
        </Col>

        <Col span={6}>
          <Form.Item
            label={t('min.qty')}
            name='min_qty'
            rules={[{ required: true, message: t('required') }]}
          >
            <InputNumber min={0} className='w-100' />
          </Form.Item>
        </Col>
        <Col span={6}>
          <Form.Item
            label={t('max.qty')}
            name='max_qty'
            rules={[{ required: true, message: t('required') }]}
          >
            <InputNumber min={0} className='w-100' />
          </Form.Item>
        </Col>
        <Col span={6}>
          <Form.Item label={t('active')} name='active' valuePropName='checked'>
            <Switch />
          </Form.Item>
        </Col>
        <Col span={24}>
          <Form.Item
            rules={[{ required: true, message: t('required') }]}
            label={t('image')}
            name='images'
          >
            <MediaUpload
              type='products'
              imageList={fileList}
              setImageList={setFileList}
              form={form}
              multiple={true}
            />
          </Form.Item>
        </Col>
      </Row>

      <Button type='primary' htmlType='submit' loading={loadingBtn}>
        {t('next')}
      </Button>
    </Form>
  );
};

export default ProductsIndex;
