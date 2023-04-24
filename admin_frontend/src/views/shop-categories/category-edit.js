import React, { useState, useEffect } from 'react';
import {
  Button,
  Card,
  Col,
  Form,
  Input,
  Row,
  Select,
  Spin,
  Switch,
} from 'antd';
import { toast } from 'react-toastify';
import { useLocation, useNavigate, useParams } from 'react-router-dom';
import LanguageList from '../../components/language-list';
import TextArea from 'antd/es/input/TextArea';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import {
  disableRefetch,
  removeFromMenu,
  setMenuData,
} from '../../redux/slices/menu';
import categoryService from '../../services/category';
import { IMG_URL } from '../../configs/app-global';
import { fetchShopCategories } from '../../redux/slices/shopCategory';
import { useTranslation } from 'react-i18next';
import MediaUpload from '../../components/upload';

const CategoryEdit = () => {
  const { t } = useTranslation();
  const navigate = useNavigate();

  const dispatch = useDispatch();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const { params } = useSelector((state) => state.shopCategory, shallowEqual);
  const [loading, setLoading] = useState(false);
  const [image, setImage] = useState(
    activeMenu.data?.image ? [activeMenu.data?.image] : []
  );
  const [form] = Form.useForm();
  const [loadingBtn, setLoadingBtn] = useState(false);
  const [error, setError] = useState(null);
  const location = useLocation();
  const { uuid } = useParams();
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

  const createImage = (name) => {
    return {
      name,
      url: IMG_URL + name,
    };
  };

  function getLanguageFields(data) {
    if (!data) {
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
    }));
    return Object.assign({}, ...result);
  }

  const getCategory = (alias) => {
    setLoading(true);
    categoryService
      .getById(alias)
      .then((res) => {
        let category = res.data;

        const body = {
          ...category,
          ...getLanguageFields(category),
          image: [createImage(category.img)],
          keywords: category.keywords.split(','),
        };
        form.setFieldsValue(body);
        setImage([createImage(category.img)]);
      })
      .finally(() => {
        setLoading(false);
        dispatch(disableRefetch(activeMenu));
      });
  };

  const onFinish = (values) => {
    setLoadingBtn(true);
    const body = {
      ...values,
      type: 'shop',
      active: values.active ? 1 : 0,
      keywords: values.keywords.join(','),
      parent_id: null,
      'images[0]': image[0]?.name,
    };

    const nextUrl = 'catalog/shop/categories';
    if (location.state === 'edit') {
      categoryService
        .update(uuid, body)
        .then(() => {
          toast.success(t('successfully.updated'));
          dispatch(removeFromMenu({ ...activeMenu, nextUrl }));
          dispatch(fetchShopCategories(params));
          navigate(`/catalog/shop/categories`);
        })
        .catch((err) => setError(err.response.data.params))
        .finally(() => setLoadingBtn(false));
    } else {
      categoryService
        .create(body)
        .then(() => {
          toast.success(t('successfully.created'));
          dispatch(removeFromMenu({ ...activeMenu, nextUrl }));
          dispatch(fetchShopCategories());
          navigate(`/catalog/shop/categories`);
        })
        .catch((err) => setError(err.response.data.params))
        .finally(() => setLoadingBtn(false));
    }
  };

  useEffect(() => {
    if (activeMenu.refetch) {
      getCategory(uuid);
    }
  }, [activeMenu.refetch]);

  // async function fetchUserCategoryList() {
  //   const params = { perPage: 100, type: 'shop' };
  //   return categoryService.getAll(params).then((res) =>
  //     res.data.map((item) => ({
  //       title: item.translation?.title,
  //       value: item.id,
  //       key: item.id,
  //       // children: item.children?.map((el) => ({
  //       //   title: el.translation?.title,
  //       //   value: el.id,
  //       //   key: el.id,
  //       //   children: el.children?.map((three) => ({
  //       //     title: three.translation?.title,
  //       //     value: three.id,
  //       //     key: three.id,
  //       //     disabled: true,
  //       //   })),
  //       // })),
  //     }))
  //   );
  // }

  return (
    <Card title={t('edit.category')} extra={<LanguageList />}>
      {!loading ? (
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
                  <Input />
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
      ) : (
        <div className='d-flex justify-content-center align-items-center py-5'>
          <Spin size='large' className='mt-5 pt-5' />
        </div>
      )}
    </Card>
  );
};
export default CategoryEdit;
