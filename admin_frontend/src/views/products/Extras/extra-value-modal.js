import { Button, Form, Input, Modal, Select } from 'antd';
import React, { useEffect, useState } from 'react';
import { SketchPicker } from 'react-color';
import { useTranslation } from 'react-i18next';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { toast } from 'react-toastify';
import ImageUploadSingle from '../../../components/image-upload-single';
import createImage from '../../../helpers/createImage';
import { fetchExtraValues } from '../../../redux/slices/extraValue';
import extraService from '../../../services/extra';

export default function ExtraValueModal({ modal, handleCancel }) {
  const [form] = Form.useForm();
  const { t } = useTranslation();
  const dispatch = useDispatch();
  const [loadingBtn, setLoadingBtn] = useState(false);
  const [type, setType] = useState('text');
  const [image, setImage] = useState(null);
  const [color, setColor] = useState('');

  const { extraGroups } = useSelector(
    (state) => state.extraGroup,
    shallowEqual
  );

  useEffect(() => {
    if (modal.id) {
      setType(modal.group.type);
      switch (modal.group.type) {
        case 'color':
          setColor(modal.value);
          break;

        case 'image':
          setImage(createImage(modal.value));
          break;

        default:
          break;
      }
      form.setFieldsValue(modal);
    }
  }, [modal]);

  const updateExtra = (id, body) => {
    setLoadingBtn(true);
    extraService
      .updateValue(id, body)
      .then(() => {
        toast.success(t('successfully.updated'));
        handleCancel();
        dispatch(fetchExtraValues());
      })
      .finally(() => setLoadingBtn(false));
  };

  const createExtra = (body) => {
    setLoadingBtn(true);
    extraService
      .createValue(body)
      .then(() => {
        toast.success(t('successfully.created'));
        handleCancel();
        dispatch(fetchExtraValues());
      })
      .finally(() => setLoadingBtn(false));
  };

  const onFinish = (values) => {
    const body = {
      extra_group_id: values.extra_group_id,
      value: getValue(type, values.value),
    };

    if (modal.id) {
      updateExtra(modal.id, body);
    } else {
      createExtra(body);
    }
  };

  function getValue(type, value) {
    switch (type) {
      case 'color':
        return value.hex;
      case 'text':
        return value;
      case 'image':
        return value.name;
      default:
        return '';
    }
  }

  const renderExtraValue = (type) => {
    switch (type) {
      case 'color':
        return (
          <SketchPicker
            onChangeComplete={(color) => setColor(color.hex)}
            color={color}
            disableAlpha={true}
          />
        );
      case 'text':
        return <Input placeholder={t('enter.extra.value')} />;

      case 'image':
        return (
          <ImageUploadSingle
            type='extras'
            image={image}
            setImage={setImage}
            form={form}
            name='value'
          />
        );

      default:
        return '';
    }
  };

  const handleSelect = (id) => {
    const extraGroup = extraGroups.find((item) => item.id === id);
    form.setFieldsValue({ value: '' });
    setType(extraGroup.type);
  };

  return (
    <Modal
      title={modal.id ? t('edit.extra') : t('add.extra')}
      visible={!!modal}
      onCancel={handleCancel}
      footer={[
        <Button
          type='primary'
          onClick={() => form.submit()}
          loading={loadingBtn}
        >
          {t('save')}
        </Button>,
        <Button type='default' onClick={handleCancel}>
          {t('cancel')}
        </Button>,
      ]}
    >
      <Form name='extra-form' layout='vertical' form={form} onFinish={onFinish}>
        <Form.Item
          name='extra_group_id'
          label={t('extra.group')}
          rules={[{ required: true, message: '' }]}
        >
          <Select
            style={{ width: '100%' }}
            placeholder={t('select.extra.group')}
            onChange={handleSelect}
          >
            {extraGroups.map((item) => (
              <Select.Option value={item.id} key={item.id}>
                {item.translation?.title}
              </Select.Option>
            ))}
          </Select>
        </Form.Item>
        <Form.Item
          name='value'
          label={t('value')}
          rules={[{ required: true, message: '' }]}
        >
          {renderExtraValue(type)}
        </Form.Item>
      </Form>
    </Modal>
  );
}
