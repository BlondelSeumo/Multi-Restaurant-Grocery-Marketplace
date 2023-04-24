import React from 'react';
import moment from 'moment';
import { Message } from '@chatscope/chat-ui-kit-react';
import { RiCheckDoubleFill } from 'react-icons/ri';

const AdminMessage = ({ text, time, chat_img, status = '' }) => {
  return (
    <div className='admin-message-wrapper'>
      <div className={`admin-message ${chat_img && 'chat-image'}`}>
        {chat_img && (
          <Message
            type='image'
            model={{
              direction: 'incoming',
              payload: {
                src: chat_img,
                alt: 'Joe avatar',
                width: '100%',
                height: '100%',
              },
            }}
          />
        )}
        {text && <div className='text'>{text}</div>}
        <div className='time'>{moment(new Date(time)).format('HH:mm')}</div>
        <span className='double-check'>
          {status === 'pending' ? '' : <RiCheckDoubleFill size={16} />}
        </span>
      </div>
    </div>
  );
};

export default AdminMessage;
