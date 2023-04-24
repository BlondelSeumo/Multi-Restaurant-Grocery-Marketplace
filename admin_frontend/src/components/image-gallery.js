import { useState } from 'react';
import { Modal, Spin, Upload } from 'antd';
import { PlusOutlined } from '@ant-design/icons';
import galleryService from '../services/gallery';
import { useTranslation } from 'react-i18next';

const getBase64 = (file) =>
  new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);

    reader.onload = () => resolve(reader.result);

    reader.onerror = (error) => reject(error);
  });

const ImageGallery = ({
  fileList,
  setFileList,
  type,
  form,
  disabled = false,
  setIsModalOpen,
}) => {
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
        setFileList((prev) => [...prev, createImage(data)]);
        form.setFieldsValue({
          images: [...fileList, createImage(data)],
        });
        onSuccess('ok');
      })
      .finally(() => {
        setIsModalOpen(false);
        setLoading(false);
      });
  };

  const handleRemove = (file) => {
    setFileList((prev) => prev.filter((item) => item.uid !== file.uid));
  };

  return (
    <>
      <Upload
        listType='picture-card'
        fileList={fileList}
        onPreview={handlePreview}
        customRequest={handleUpload}
        className={disabled ? 'antdImgUpload' : 'antdImgUpload'}
        onRemove={handleRemove}
        showUploadList={false}
      >
        {fileList.length >= 24 || disabled ? null : uploadButton}
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

export default ImageGallery;
