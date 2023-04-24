import React, { useState } from 'react';
import { Card, Button, Result } from 'antd';
import { useTranslation } from 'react-i18next';
import axios from 'axios';
import { toast } from 'react-toastify';

export default function CashClear() {
  const [loadingBtn, setLoadingBtn] = useState(false);
  const { t } = useTranslation();
  const access_token = localStorage.getItem('token');

  const getBackup = () => {
    setLoadingBtn(true);
    axios
      .get(
        'https://api.foodyman.org/api/v1/dashboard/admin/settings/system/cache/clear',
        {
          headers: {
            Authorization: `Bearer ${access_token}`,
            'content-type': 'text/json',
          },
        }
      )
      .then(() => toast.success('successfully.cleared'))
      .finally(() => setLoadingBtn(false));
  };

  return (
    <Card title={t('clear.cash')}>
      <Result
        status='warning'
        title={t('do.you.really.want.to.clear.the.cash?')}
        extra={
          <Button type='primary' loading={loadingBtn} onClick={getBackup}>
            {t('clear.cash')}
          </Button>
        }
      />
    </Card>
  );
}
