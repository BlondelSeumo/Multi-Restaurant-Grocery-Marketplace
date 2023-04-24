import React from 'react';
import { BsFolder } from 'react-icons/bs';
import { Card, Col, Row } from 'antd';
import { useNavigate } from 'react-router-dom';
import { colLg } from '../../components/card-responsive';
import { useTranslation } from 'react-i18next';
import Meta from 'antd/lib/card/Meta';

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

const Gallery = () => {
  const navigate = useNavigate();
  const { t } = useTranslation();

  return (
    <Card title={t('gallery')} className='gallery-container'>
      <Row gutter={[24, 24]}>
        {folder.map((item, index) => (
          <Col {...colLg} key={index}>
            <Card
              cover={<BsFolder className='icon-folder' />}
              onClick={() => navigate(`/gallery/${item}`)}
              className='folder'
            >
              <Meta title={t(`${item}`)} />
            </Card>
          </Col>
        ))}
      </Row>
    </Card>
  );
};

export default Gallery;
