import React, { useState } from 'react';
import { Card, Col, Row } from 'antd';
import { colLg } from '../card-responsive';
import { useTranslation } from 'react-i18next';
import GalleryItem from './gallery-item';
import { BsFolder } from 'react-icons/bs';
import Meta from 'antd/lib/card/Meta';

const UploadMedia = ({
  setImageList,
  imageList,
  setIsModalOpen,
  form,
  name,
}) => {
  const { t } = useTranslation();
  const [currentType, setCurrentType] = useState(null);
  const folder = [
    'languages',
    'categories',
    'shops',
    'brands',
    'products',
    'extras',
    'users',
    'restaurant',
    'deliveryman',
    'shop-tags',
  ];
  return (
    <>
      {currentType ? (
        <GalleryItem
          type={currentType}
          setCurrentType={setCurrentType}
          setImageList={setImageList}
          imageList={imageList}
          setIsModalOpen={setIsModalOpen}
          form={form}
          name={name}
        />
      ) : (
        <Card className='media-upload-gallery-container'>
          <Row gutter={[24, 24]}>
            {folder.map((item) => {
              return (
                <Col {...colLg}>
                  <Card
                    cover={<BsFolder className='icon-folder' />}
                    className='folder'
                    onClick={() => setCurrentType(item)}
                  >
                    <Meta title={t(`${item}`)} />
                  </Card>
                </Col>
              );
            })}
          </Row>
        </Card>
      )}
    </>
  );
};

export default UploadMedia;
