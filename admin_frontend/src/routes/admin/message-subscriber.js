// ** React Imports
import { lazy } from 'react';

const MessageSubscriber = [
  {
    path: 'message/subscriber/add',
    component: lazy(() => import('views/message-subscribers/subciribed-add')),
  },
  {
    path: 'subscriptions/edit',
    component: lazy(() => import('views/message-subscribers/subciribed-edit')),
  },
  {
    path: 'message/subscriber',
    component: lazy(() => import('views/message-subscribers')),
  },
];

export default MessageSubscriber;
