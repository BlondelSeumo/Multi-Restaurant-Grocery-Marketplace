import React, { useEffect, useState } from 'react';
import {
  Button,
  Col,
  Form,
  Input,
  InputNumber,
  Row,
  Select,
  Switch,
} from 'antd';
import { DebounceSelect } from '../../components/search';
import shopService from '../../services/restaurant';
import brandService from '../../services/brand';
import categoryService from '../../services/category';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import productService from '../../services/product';
import { replaceMenu, setMenuData } from '../../redux/slices/menu';
import unitService from '../../services/unit';
import { useNavigate, useParams } from 'react-router-dom';
import { AsyncTreeSelect } from '../../components/async-tree-select';
import { useTranslation } from 'react-i18next';
import MediaUpload from '../../components/upload';
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
  const [fileList, setFileList] = useState(
    activeMenu.data?.images ? activeMenu.data?.images : []
  );
  const [loadingBtn, setLoadingBtn] = useState(false);
  const [units, setUnits] = useState([]);

  useEffect(() => {
    return () => {
      const data = form.getFieldsValue(true);
      dispatch(
        setMenuData({ activeMenu, data: { ...activeMenu.data, ...data } })
      );
    };
  }, []);

  async function fetchUserShopList(search) {
    const params = { search };
    return shopService.get(params).then((res) =>
      res.data.map((item) => ({
        label: item.translation ? item.translation.title : 'no name',
        value: item.id,
      }))
    );
  }

  async function fetchUserBrandList(username) {
    return brandService.search(username).then((res) =>
      res.data.map((item) => ({
        label: item.title,
        value: item.id,
      }))
    );
  }

  async function fetchUserCategoryList() {
    const params = { perPage: 100, type: 'main' };
    return categoryService.getAll(params).then((res) =>
      res.data.map((item) => ({
        title: item.translation?.title,
        value: item.id,
        key: item.id,
        // disabled: item.children.length ? true : false,
        // children: item.children?.map((el) => ({
        //   title: el.translation?.title,
        //   value: el.id,
        //   key: el.id,
        //   disabled: el.children.length ? true : false,
        //   children: el?.children?.map((three) => ({
        //     title: three.translation?.title,
        //     value: three.id,
        //     key: three.id,
        //     disabled: three.children.length ? true : false,
        //   })),
        // })),
      }))
    );
  }

  const onFinish = (values) => {
    setLoadingBtn(true);
    const params = {
      ...values,
      active: Number(values.active),
      brand_id: values.brand?.value,
      category_id: values.category?.value,
      shop_id: values.shop?.value,
      unit_id: values.unit?.value,
      images: undefined,
      brand: undefined,
      category: undefined,
      shop: undefined,
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
            url: `product/${data.uuid}`,
            name: t('add.product'),
            data: values,
            refetch: false,
          })
        );
        navigate(`/product/${data.uuid}/?step=1`);
      })
      .catch((err) => setError(err.response.data.params))
      .finally(() => setLoadingBtn(false));
  }

  function productUpdate(values, params) {
    productService
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

  function fetchUnits() {
    const params = {
      perPage: 100,
      page: 1,
      active: 1,
    };
    unitService.getAll(params).then(({ data }) => setUnits(formatUnits(data)));
  }

  useEffect(() => {
    fetchUnits();
  }, []);

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
        <Col span={12} className='mb-4'>
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
        <Col span={12}>
          <Form.Item
            label={t('shop/restaurant')}
            name='shop'
            rules={[{ required: true, message: t('required') }]}
          >
            <DebounceSelect
              fetchOptions={fetchUserShopList}
              disabled={action_type === 'edit'}
            />
          </Form.Item>
        </Col>
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
          <Form.Item
            label={t('tax')}
            name='tax'
            rules={[{ required: true, message: t('required') }]}
          >
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
            <Select labelInValue={true} filterOption={false} options={units} />
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
          <Form.Item label={t('images')} name='images'>
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
