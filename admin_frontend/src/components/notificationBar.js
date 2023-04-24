import React, { useEffect, useState } from 'react';
import { BellOutlined } from '@ant-design/icons';
import NotificationDrawer from './notification-drawer';
import { delMany, keys } from 'idb-keyval';
import { Badge } from 'antd';
import { toast } from 'react-toastify';
import PushNotification from './push-notification';

export default function NotificationBar() {
  const [notificationDrawer, setNotificationDrawer] = useState(false);
  const [notifications, setNotifications] = useState([]);

  useEffect(() => {
    const handleFocus = () => {
      getNotifications();
    };
    window.addEventListener('focus', handleFocus);
    return () => {
      window.removeEventListener('focus', handleFocus);
    };
  }, []);

  function getNotifications() {
    keys().then((val) => setNotifications(val));
  }

  function clearNotifications() {
    delMany(notifications)
      .then(() => {
        setNotifications([]);
        setNotificationDrawer(false);
      })
      .catch(() => toast.error('Error occured'));
  }

  return (
    <>
      <span className='icon-button' onClick={() => setNotificationDrawer(true)}>
        <Badge count={notifications.length}>
          <BellOutlined style={{ fontSize: 20 }} />
        </Badge>
      </span>

      <NotificationDrawer
        visible={notificationDrawer}
        handleClose={() => setNotificationDrawer(false)}
        list={notifications}
        clear={clearNotifications}
        refetch={getNotifications}
      />
      <PushNotification refetch={getNotifications} />
    </>
  );
}
