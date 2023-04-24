import React, { useState } from 'react';
import { Card, Steps } from 'antd';
import { shallowEqual, useSelector } from 'react-redux';
import LanguageList from '../../components/language-list';
import { useTranslation } from 'react-i18next';
import ShopMain from './main';
import ShopDelivery from './shopDelivery';
import Map from '../../components/shop/map';

const { Step } = Steps;

export const steps = [
  {
    title: 'restaurant',
    content: 'First-content',
  },
  {
    title: 'map',
    content: 'Second-content',
  },
  {
    title: 'delivery',
    content: 'Third-content',
  },
];

const ShopsAdd = () => {
  const { t } = useTranslation();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const [current, setCurrent] = useState(activeMenu.data?.step || 0);

  const next = () => {
    const step = current + 1;
    setCurrent(step);
  };
  const prev = () => {
    const step = current - 1;
    setCurrent(step);
  };

  return (
    <Card title={t('add.shop')} extra={<LanguageList />}>
      <Steps current={current}>
        {steps.map((item) => (
          <Step title={t(item.title)} key={item.title} />
        ))}
      </Steps>

      <div className='steps-content'>
        {steps[current].content === 'First-content' && (
          <ShopMain next={next} user={true} />
        )}

        {steps[current].content === 'Second-content' && (
          <Map next={next} prev={prev} />
        )}

        {steps[current].content === 'Third-content' && (
          <ShopDelivery next={next} prev={prev} />
        )}
      </div>
    </Card>
  );
};
export default ShopsAdd;
