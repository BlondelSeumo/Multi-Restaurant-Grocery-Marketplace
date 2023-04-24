import React, { useState } from 'react';
import { steps } from './steps';
import { Card, Steps } from 'antd';
import ProductProperty from './product-property';
import ProductFinish from './product-finish';
import ProductStock from './product-stock';
import ProductExtras from './product-extras';
import ProductsIndex from './products-index';
import LanguageList from '../../components/language-list';
import { shallowEqual, useSelector } from 'react-redux';
import { useTranslation } from 'react-i18next';

const { Step } = Steps;

const ProductsAdd = () => {
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
    <Card title={t('add.food')} extra={<LanguageList />}>
      <Steps current={current}>
        {steps.map((item) => (
          <Step title={t(item.title)} key={item.title} />
        ))}
      </Steps>
      <div className='steps-content'>
        {steps[current].content === 'First-content' && (
          <ProductsIndex next={next} />
        )}

        {steps[current].content === 'Second-content' && (
          <ProductExtras next={next} prev={prev} />
        )}

        {steps[current].content === 'Third-content' && (
          <ProductStock next={next} prev={prev} />
        )}

        {steps[current].content === 'Fourth-content' && (
          <ProductProperty next={next} prev={prev} />
        )}

        {steps[current].content === 'Finish-content' && (
          <ProductFinish prev={prev} />
        )}
      </div>
    </Card>
  );
};
export default ProductsAdd;
