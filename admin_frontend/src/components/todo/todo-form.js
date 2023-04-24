import React from 'react';
import { Form, Row, Col, Button, Input } from 'antd';
import { PlusCircleFilled } from '@ant-design/icons';
import { useTranslation } from 'react-i18next';

export const AddTodoForm = ({ onFormSubmit }) => {
  const [form] = Form.useForm();
  const { t } = useTranslation();

  const onFinish = (values) => {
    onFormSubmit(values);

    form.resetFields();
  };

  return (
    <Form
      form={form}
      onFinish={onFinish}
      layout='horizontal'
      className='todo-form'
    >
      <Row gutter={20}>
        <Col xs={24} sm={24} md={12} lg={14} xl={18}>
          <Form.Item
            name={'name'}
            rules={[{ required: true, message: 'This field is required' }]}
          >
            <Input placeholder={t('todo.placeholder')} />
          </Form.Item>
        </Col>
        <Col xs={24} sm={24} md={12} lg={10} xl={6}>
          <Button type='primary' htmlType='submit' block>
            <PlusCircleFilled />
            {t('todo.add')}
          </Button>
        </Col>
      </Row>
    </Form>
  );
};
