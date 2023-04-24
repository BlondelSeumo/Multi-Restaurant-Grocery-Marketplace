import React, { useEffect, useState } from 'react';
import {
  Button,
  Col,
  Form,
  Input,
  message,
  Modal,
  Row,
  Space,
  Table,
} from 'antd';
import { DeleteOutlined, EditOutlined } from '@ant-design/icons';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import PropertyModal from './property-modal';
import { setMenuData } from '../../redux/slices/menu';
import productService from '../../services/product';
import { useTranslation } from 'react-i18next';
import { useParams } from 'react-router-dom';
import DeleteButton from '../../components/delete-button';

const ProductProperty = ({ next, prev }) => {
  const { t } = useTranslation();
  const dispatch = useDispatch();
  const { uuid } = useParams();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const { defaultLang, languages } = useSelector(
    (state) => state.formLang,
    shallowEqual
  );
  const [form] = Form.useForm();
  const [editData, setEditData] = useState(null);
  const [dataSource, setDataSource] = useState(
    activeMenu.data?.properties || []
  );
  const [loadingBtn, setLoadingBtn] = useState(false);

  const column = [
    {
      key: '2',
      title: t('key'),
      render: (text, row) => row[`key[${defaultLang}]`],
    },
    {
      key: '4',
      title: t('value'),
      render: (text, row) => row[`value[${defaultLang}]`],
    },
    {
      key: '5',
      title: t('options'),
      render: (record) => {
        return (
          <Space>
            <Button
              type='primary'
              icon={<EditOutlined />}
              onClick={() => setEditData(record)}
            />
            <DeleteButton
              type='primary'
              danger
              icon={<DeleteOutlined />}
              onClick={() => onDeleteProduct(record)}
            />
          </Space>
        );
      },
    },
  ];

  useEffect(() => {
    if (dataSource.length && uuid) {
      const properties = dataSource;
      dispatch(
        setMenuData({
          activeMenu,
          data: { ...activeMenu.data, properties },
        })
      );
    }
  }, [dataSource]);

  const editProperty = (data) => {
    setDataSource((pre) => {
      return pre.map((item) => {
        if (item.id === data.id) {
          return data;
        } else {
          return item;
        }
      });
    });
    handleCancel();
  };

  const onDeleteProduct = (record) => {
    Modal.confirm({
      title: t('delete.product'),
      okText: t('yes'),
      okType: 'danger',
      onOk: () => {
        setDataSource((pre) => pre.filter((item) => item.id !== record.id));
      },
    });
  };

  const handleCancel = () => setEditData(null);

  const onFinish = (values) => {
    const randomNumber = parseInt(Math.random() * 50);
    const newItem = { ...values, id: randomNumber };
    setDataSource((prev) => [...prev, newItem]);
    form.resetFields();
  };

  function createProperties() {
    setLoadingBtn(true);
    const data = formatProperty();
    productService
      .properties(uuid, data)
      .then(() => {
        message.success(t('product.properties.saved'));
        next();
      })
      .finally(() => setLoadingBtn(false));
  }

  function formatProperty() {
    let keys = [];
    let values = [];
    dataSource.forEach((item) => {
      let key = {};
      let value = {};
      languages.forEach((el) => {
        key[el.locale] = item[`key[${el.locale}]`];
        value[el.locale] = item[`value[${el.locale}]`];
      });
      keys.push(key);
      values.push(value);
    });
    return {
      key: keys,
      value: values,
    };
  }

  return (
    <>
      <Form layout='vertical' form={form} onFinish={onFinish}>
        <Row gutter={12} className='mb-3'>
          <Col span={8}>
            {languages.map((item) => (
              <Form.Item
                key={'key' + item.locale}
                name={`key[${item.locale}]`}
                rules={[
                  {
                    required: item.locale === defaultLang,
                    message: t('required'),
                  },
                ]}
                hidden={item.locale !== defaultLang}
              >
                <Input placeholder='Key' />
              </Form.Item>
            ))}
          </Col>
          <Col span={8}>
            {languages.map((item) => (
              <Form.Item
                key={'value' + item.locale}
                name={`value[${item.locale}]`}
                rules={[
                  {
                    required: item.locale === defaultLang,
                    message: t('required'),
                  },
                ]}
                hidden={item.locale !== defaultLang}
              >
                <Input placeholder={t('value')} />
              </Form.Item>
            ))}
          </Col>
          <Col span={4}>
            <Button type='primary' htmlType='submit'>
              {t('save')}
            </Button>
          </Col>
        </Row>
      </Form>

      <Table
        scroll={{ x: true }}
        columns={column}
        dataSource={dataSource}
        pagination={false}
        rowKey={(record) => record.id}
      />

      {editData && (
        <PropertyModal
          editData={editData}
          handleCancel={handleCancel}
          editProperty={editProperty}
        />
      )}

      <Space className='mt-4'>
        <Button onClick={prev}>{t('prev')}</Button>
        <Button type='primary' onClick={createProperties} loading={loadingBtn}>
          {t('next')}
        </Button>
      </Space>
    </>
  );
};

export default ProductProperty;
