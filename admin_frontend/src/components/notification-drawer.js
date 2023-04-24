import React from 'react';
import { Button, Drawer, List } from 'antd';
import { useTranslation } from 'react-i18next';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { addMenu } from '../redux/slices/menu';
import { useNavigate } from 'react-router-dom';
import { del } from 'idb-keyval';

export default function NotificationDrawer({
  handleClose,
  visible,
  list,
  clear,
  refetch,
}) {
  const { user } = useSelector((state) => state.auth, shallowEqual);
  const { direction } = useSelector((state) => state.theme.theme, shallowEqual);
  const { t } = useTranslation();
  const dispatch = useDispatch();
  const navigate = useNavigate();

  const deleteItem = (id) => {
    del(id).then(() => refetch());
    handleClose();
  };

  const goToShow = (id) => {
    deleteItem(id);
    let url = '';
    switch (user.role) {
      case 'admin':
        url = `order/details/${id}`;
        break;
      case 'manager':
        url = `order/details/${id}`;
        break;
      case 'seller':
        url = `seller/order/details/${id}`;
        break;
      case 'moderator':
        url = `seller/order/details/${id}`;
        break;
      case 'deliveryman':
        url = `deliveryman/order/details/${id}`;
        break;

      default:
        break;
    }
    dispatch(
      addMenu({
        url,
        id: 'order_details',
        name: t('order.details'),
      })
    );
    navigate(`/${url}`);
  };

  return (
    <Drawer
      title={t('notifications')}
      placement={direction === 'rtl' ? 'right' : 'left'}
      closable={false}
      onClose={handleClose}
      visible={visible}
      key={'left'}
      extra={<Button onClick={() => clear()}>{t('clear')}</Button>}
    >
      <List
        size='small'
        itemLayout='horizontal'
        dataSource={list}
        renderItem={(item) => (
          <List.Item className='list-clickable' onClick={() => goToShow(item)}>
            <div className='py-1'>
              <span className='font-weight-bold'>
                {t('new.order')} #{item}
              </span>
            </div>
          </List.Item>
        )}
      />
    </Drawer>
  );
}
