import React from 'react';
import { Card } from 'antd';
import { useTranslation } from 'react-i18next';
import { shallowEqual, useSelector } from 'react-redux';

export default function TopBar() {
  const { t } = useTranslation();
  const { user } = useSelector((state) => state.auth, shallowEqual);

  return (
    <Card>
      <h2>
        {t('hello')}, {user.fullName} 👋
      </h2>
      <p>{t('hello.text')}</p>
    </Card>
  );
}
