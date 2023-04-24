import { useState } from 'react';
import { Modal, Spin, Upload } from 'antd';
import { PlusOutlined } from '@ant-design/icons';
import galleryService from '../services/gallery';
import { IMG_URL } from '../configs/app-global';
import { useTranslation } from 'react-i18next';

const getBase64 = (file) =>
  new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);

    reader.onload = () => resolve(reader.result);

    reader.onerror = (error) => reject(error);
  });

const ImageUploadSingle = ({ image, setImage, type, form, name = 'image' }) => {
  const { t } = useTranslation();
  const [previewVisible, setPreviewVisible] = useState(false);
  const [previewImage, setPreviewImage] = useState('');
  const [previewTitle, setPreviewTitle] = useState('');
  const [loading, setLoading] = useState(false);

  const handleCancel = () => setPreviewVisible(false);

  const handlePreview = async (file) => {
    if (!file.url && !file.preview) {
      file.preview = await getBase64(file.originFileObj);
    }

    setPreviewImage(file.url || file.preview);
    setPreviewVisible(true);
    setPreviewTitle(
      file.name || file.url.substring(file.url.lastIndexOf('/') + 1)
    );
  };

  const handleChange = ({ fileList: newFileList }) => {
    // setFileList(newFileList)
  };

  const createImage = (file) => {
    return {
      uid: file.title,
      name: file.title,
      status: 'done', // done, uploading, error
      url: file.title,
      created: true,
    };
  };

  const uploadButton = loading ? (
    <div>
      <Spin />
    </div>
  ) : (
    <div>
      <PlusOutlined />
      <div
        style={{
          marginTop: 8,
        }}
      >
        {t('upload')}
      </div>
    </div>
  );

  const handleUpload = ({ file, onSuccess }) => {
    setLoading(true);
    const formData = new FormData();
    formData.append('image', file);
    formData.append('type', type);
    galleryService
      .upload(formData)
      .then(({ data }) => {
        setImage(createImage(data));
        form.setFieldsValue({
          [name]: createImage(data),
        });
        onSuccess('ok');
      })
      .finally(() => setLoading(false));
  };

  return (
    <>
      <Upload
        listType='picture-card'
        showUploadList={false}
        onPreview={handlePreview}
        onChange={handleChange}
        customRequest={handleUpload}
        className='picture-card'
      >
        {image && !loading ? (
          <img
            src={image.url}
            alt='avatar'
            style={{
              width: '100%',
              height: '100%',
              objectFit: 'contain',
            }}
          />
        ) : (
          uploadButton
        )}
      </Upload>
      <Modal
        visible={previewVisible}
        title={previewTitle}
        footer={null}
        onCancel={handleCancel}
      >
        <img
          alt='example'
          style={{
            width: '100%',
          }}
          src={previewImage}
        />
      </Modal>
    </>
  );
};

export default ImageUploadSingle;
