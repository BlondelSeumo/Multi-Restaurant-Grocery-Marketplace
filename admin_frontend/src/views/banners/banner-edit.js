import React, { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { toast } from 'react-toastify';
import { Button, Card, Col, Form, Input, Row, Spin, Switch } from 'antd';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import {
  disableRefetch,
  removeFromMenu,
  setMenuData,
} from '../../redux/slices/menu';
import { fetchBanners } from '../../redux/slices/banner';
import { DebounceSelect } from '../../components/search';
import bannerService from '../../services/banner';
import { useTranslation } from 'react-i18next';
import LanguageList from '../../components/language-list';
import getTranslationFields from '../../helpers/getTranslationFields';
import shopService from '../../services/restaurant';
import MediaUpload from '../../components/upload';

const BannerEdit = () => {
  const { t } = useTranslation();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const dispatch = useDispatch();
  const [form] = Form.useForm();
  const navigate = useNavigate();
  const { id } = useParams();

  const [image, setImage] = useState(
    activeMenu.data?.img ? activeMenu.data?.img : []
  );
  const [loadingBtn, setLoadingBtn] = useState(false);
  const [loading, setLoading] = useState(false);

  const { languages, defaultLang } = useSelector(
    (state) => state.formLang,
    shallowEqual
  );

  useEffect(() => {
    return () => {
      const data = form.getFieldsValue(true);
      dispatch(setMenuData({ activeMenu, data }));
    };
  }, []);

  const createImages = (items) =>
    items.map((item) => ({
      uid: item.id,
      name: item.path,
      url: item.path,
    }));

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
      [`button_text[${item.locale}]`]: translations.find(
        (el) => el.locale === item.locale
      )?.button_text,
    }));
    return Object.assign({}, ...result);
  }

  const getBanner = (alias) => {
    setLoading(true);
    bannerService
      .getById(alias)
      .then((res) => {
        let banner = res.data;

        const data = {
          ...banner,
          img: createImages(banner.galleries),
          shops: banner?.shops?.map((item) => ({
            label: item.translation?.title,
            value: item.id,
          })),
          ...getLanguageFields(banner),
        };
        form.setFieldsValue(data);
        setImage(createImages(banner.galleries));
        dispatch(setMenuData({ activeMenu, data }));
      })
      .finally(() => {
        dispatch(disableRefetch(activeMenu));
        setLoading(false);
      });
  };

  const onFinish = (values) => {
    setLoadingBtn(true);
    const body = {
      url: values.url,
      shops: values?.shops?.map((i) => i.value),
      images: image?.map((image) => image.name),
      clickable: values.clickable,
      title: getTranslationFields(languages, values, 'title'),
      description: getTranslationFields(languages, values, 'description'),
      button_text: getTranslationFields(languages, values, 'button_text'),
    };
    bannerService
      .update(id, body)
      .then(() => {
        const nextUrl = 'banners';
        toast.success(t('successfully.updated'));
        dispatch(removeFromMenu({ ...activeMenu, nextUrl }));
        navigate(`/${nextUrl}`);
        dispatch(fetchBanners());
      })
      .finally(() => setLoadingBtn(false));
  };

  useEffect(() => {
    if (activeMenu.refetch) {
      getBanner(id);
    }
  }, [activeMenu.refetch]);

  function fetchShopOptions(search) {
    const params = {
      search,
      perPage: 10,
      status: 'approved',
    };
    return shopService.getAll(params).then((res) => formatShop(res.data));
  }

  function formatShop(data) {
    return data.map((item) => ({
      label: item.translation?.title,
      value: item.id,
    }));
  }

  return (
    <Card title={t('edit.banner')} className='h-100' extra={<LanguageList />}>
      {!loading ? (
        <Form
          name='banner-add'
          layout='vertical'
          onFinish={onFinish}
          form={form}
          initialValues={{ active: true, ...activeMenu.data }}
          className='d-flex flex-column h-100'
        >
          <Row gutter={12}>
            <Col span={12}>
              {languages.map((item) => (
                <Form.Item
                  key={'title' + item.locale}
                  label={t('title')}
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
                  key={'description' + item.locale}
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
                  <Input />
                </Form.Item>
              ))}
            </Col>
            <Col span={12}>
              {languages.map((item) => (
                <Form.Item
                  key={'button_text' + item.locale}
                  label={t('button_text')}
                  name={`button_text[${item.locale}]`}
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
              <Form.Item
                rules={[
                  {
                    required: true,
                    message: t('required'),
                  },
                ]}
                label={t('url')}
                name={'url'}
              >
                <Input />
              </Form.Item>
            </Col>

            <Col span={12}>
              <Form.Item
                label={t('shop')}
                name={'shops'}
                rules={[
                  {
                    required: true,
                    message: t('required'),
                  },
                ]}
              >
                <DebounceSelect
                  mode='multiple'
                  fetchOptions={fetchShopOptions}
                  debounceTimeout={200}
                />
              </Form.Item>
            </Col>

            <Col span={12}>
              <Form.Item
                label={t('clickable')}
                name='clickable'
                valuePropName='checked'
              >
                <Switch />
              </Form.Item>
            </Col>
            <Col span={24}>
              <Form.Item
                label={t('image')}
                name='img'
                rules={[
                  {
                    required: !image.length,
                    message: t('required'),
                  },
                ]}
              >
                <MediaUpload
                  type='products'
                  imageList={image}
                  setImageList={setImage}
                  form={form}
                  length='1'
                  multiple={false}
                />
              </Form.Item>
            </Col>
          </Row>
          <div className='flex-grow-1 d-flex flex-column justify-content-end'>
            <div className='pb-5'>
              <Button
                type='primary'
                htmlType='submit'
                loading={loadingBtn}
                disabled={loadingBtn}
              >
                {t('submit')}
              </Button>
            </div>
          </div>
        </Form>
      ) : (
        <div className='d-flex justify-content-center align-items-center'>
          <Spin size='large' className='py-5' />
        </div>
      )}
    </Card>
  );
};

export default BannerEdit;
