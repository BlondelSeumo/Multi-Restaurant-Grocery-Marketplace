import React, { useState } from 'react';
import { Tabs, Modal, Image } from 'antd';
import UploadMedia from './upload-media';
import { DeleteOutlined, PlusOutlined } from '@ant-design/icons';
import getImage from '../../helpers/getImage';
import ImageGallery from '../image-gallery';

const MediaUpload = ({
  imageList,
  setImageList,
  form,
  type,
  multiple = true,
  name,
  disabled,
}) => {
  const [isModalOpen, setIsModalOpen] = useState(false);

  const showModal = () => {
    setIsModalOpen(true);
  };

  const handleCancel = () => {
    setIsModalOpen(false);
  };

  const removeImg = (path) => {
    const nextArray = imageList.filter((item) => item !== path);
    form.setFieldsValue({
      images: nextArray,
    });
    setImageList(nextArray);
  };

  return (
    <>
      <div className='media-upload-wrapper'>
        {imageList?.map((item) => (
          <div
            key={item}
            className='image-wrapper'
            onClick={() => (disabled ? undefined : removeImg(item))}
          >
            <Image
              preview={false}
              src={getImage(item?.name)}
              className='images'
              alt={'images'}
            />
            <DeleteOutlined color='white' hidden={disabled} />
          </div>
        ))}
        {(multiple || !imageList.length) && (
          <div className='media-upload' onClick={showModal}>
            <PlusOutlined /> <span>Upload</span>
          </div>
        )}
      </div>
      <Modal
        onCancel={handleCancel}
        maskClosable={true}
        visible={isModalOpen}
        footer={false}
        width={'80%'}
      >
        <Tabs>
          <Tabs.TabPane tab='Media files' key='item-1'>
            <UploadMedia
              form={form}
              setImageList={setImageList}
              imageList={imageList}
              setIsModalOpen={setIsModalOpen}
              name={name}
            />
          </Tabs.TabPane>
          <Tabs.TabPane
            tab='Upload media'
            key='item-2'
            className='upload-media'
          >
            <ImageGallery
              type={type}
              disabled={false}
              form={form}
              setFileList={setImageList}
              fileList={imageList}
              setIsModalOpen={setIsModalOpen}
            />
          </Tabs.TabPane>
        </Tabs>
      </Modal>
    </>
  );
};
export default MediaUpload;
