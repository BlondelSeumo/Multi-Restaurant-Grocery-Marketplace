import React from 'react';
import { Button, Card } from 'antd';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { useTranslation } from 'react-i18next';
import Dragger from 'antd/lib/upload/Dragger';
import { InboxOutlined, InfoCircleOutlined } from '@ant-design/icons';
import { toast } from 'react-toastify';
import { setMenuData } from '../../../redux/slices/menu';
import { fetchSellerProducts } from '../../../redux/slices/product';
import { example } from '../../../configs/app-global';
import bannerService from '../../../services/banner';

export default function SellerAddonsImport() {
  const { t } = useTranslation();
  const dispatch = useDispatch();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);

  const createFile = (file) => {
    return {
      uid: file.name,
      name: file.name,
      status: 'done',
      url: file.name,
      created: true,
    };
  };

  const beforeUpload = (file) => {
    const isXls =
      file.type ===
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    if (!isXls) {
      toast.error(`${file.name} is not valid file`);
      return false;
    }
  };

  const handleUpload = ({ file, onSuccess }) => {
    const formData = new FormData();
    formData.append('file', file);
    bannerService.import(formData).then((data) => {
      toast.success(t('successfully.import'));
      dispatch(setMenuData({ activeMenu, data: createFile(file) }));
      onSuccess('ok');
      dispatch(fetchSellerProducts());
    });
  };

  const downloadFile = () => {
    const body = example + 'import-example/category_import.xls';
    window.location.href = body;
  };

  return (
    <Card title={t('import')}>
      <div className='alert' role='alert'>
        <div className='alert-header'>
          <InfoCircleOutlined className='alert-icon' />
          <p>{t('info')}</p>
        </div>
        <ol className='ml-4'>
          <li>{t('import.text_1')}</li>
          <li>{t('import.text_2')}</li>
          <li>{t('import.text_3')}</li>
          <li>{t('import.text_4')}</li>
        </ol>
      </div>
      <Button type='primary' className='mb-4' onClick={downloadFile}>
        {t('download.csv')}
      </Button>
      <Dragger
        name='file'
        multiple={false}
        maxCount={1}
        customRequest={handleUpload}
        defaultFileList={activeMenu?.data ? [activeMenu?.data] : null}
        beforeUpload={beforeUpload}
      >
        <p className='ant-upload-drag-icon'>
          <InboxOutlined />
        </p>
        <p className='ant-upload-text'>{t('upload-drag')}</p>
        <p className='ant-upload-hint'>{t('upload-text')}</p>
      </Dragger>
    </Card>
  );
}
