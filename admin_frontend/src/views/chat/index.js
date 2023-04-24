import React, { useContext, useEffect, useRef } from 'react';
import '@chatscope/chat-ui-kit-styles/dist/default/styles.min.css';
import {
  Sidebar,
  MainContainer,
  ChatContainer,
  MessageList,
  MessageInput,
  Avatar,
  ConversationList,
  Conversation,
  ConversationHeader,
} from '@chatscope/chat-ui-kit-react';
import Channel from './channel';
import {
  deleteChat,
  deleteMessage,
  sendMessage,
  updateMessage,
} from '../../firebase';
import { batch, shallowEqual, useDispatch, useSelector } from 'react-redux';
import {
  addMessage,
  removeCurrentChat,
  setCurrentChat,
  setNewMessage,
} from '../../redux/slices/chat';
import {
  getChatDetails,
  getMessages,
} from '../../redux/selectors/chatSelector';
import { scrollTo } from '../../helpers/scrollTo';
import { useTranslation } from 'react-i18next';
import { DeleteOutlined, MoreOutlined } from '@ant-design/icons';
import { Dropdown, Menu } from 'antd';
import CustomModal from '../../components/modal';
import { Context } from '../../context/context';
import getAvatar from '../../helpers/getAvatar';
import { useState } from 'react';
import UploadMedia from './upload-media';
import { toast } from 'react-toastify';
import { SUPPORTED_FORMATS } from '../../configs/app-global';

export default function Chat() {
  const { t } = useTranslation();
  const inputRef = useRef();
  const nextRef = useRef();
  const dispatch = useDispatch();
  const messageEndRef = useRef();
  const { setIsModalVisible } = useContext(Context);
  const [file, setFile] = useState('');
  const [url, setUrl] = useState('');
  const [modal, setModal] = useState(false);
  const { user } = useSelector((state) => state.auth, shallowEqual);
  const { myShop } = useSelector((state) => state.myShop, shallowEqual);
  const { chats, currentChat, newMessage, messages } = useSelector(
    (state) => state.chat,
    shallowEqual
  );
  const groupMessages = useSelector(
    (state) => getMessages(state.chat),
    shallowEqual
  );

  useEffect(() => {
    if (inputRef.current) {
      inputRef.current.focus();
    }
  }, [inputRef, currentChat]);

  const handleOnChange = (value) => {
    dispatch(setNewMessage(value));
  };

  const handleOnSubmit = (url) => {
    const isFile = url?.includes('https');
    const trimmedMessage = newMessage
      .replace(/\&nbsp;/g, '')
      .replace(/<[^>]+>/g, '')
      .trim();
    const payload = {
      chat_content: trimmedMessage,
      chat_id: currentChat?.id,
      sender: 0,
      unread: true,
    };
    if (isFile) payload.chat_img = url;
    if (trimmedMessage || isFile) {
      sendMessage(payload);
      batch(() => {
        dispatch(setNewMessage(''));
        dispatch(addMessage({ ...payload, status: 'pending' }));
      });
      const topPosition = messageEndRef.current.offsetTop;
      const container = document.querySelector(
        '.message-list .scrollbar-container'
      );
      scrollTo(container, topPosition - 30, 600);
      setUrl('');
      setModal(false);
    }
  };

  const handleChatClick = (chat, unreadMessages) => {
    dispatch(setCurrentChat(chat));
    if (unreadMessages.length) {
      unreadMessages.forEach((item) => {
        updateMessage(item);
      });
    }
  };

  const deleteCurrentChat = () => {
    deleteChat(currentChat);
    dispatch(removeCurrentChat());
    groupMessages.forEach((group) => {
      group.messages.forEach((item) => deleteMessage(item));
    });
    setIsModalVisible(false);
  };
  function handleFile(event) {
    if (!SUPPORTED_FORMATS.includes(event.target.files[0].type)) {
      toast.error('Supported only image formats!');
    } else {
      setFile(event.target.files[0]);
      const reader = new FileReader();
      reader.onload = () => {
        if (reader.readyState === 2) {
          setUrl(reader.result);
          setModal(true);
        }
      };
      reader?.readAsDataURL(event.target.files[0]);
    }
  }
  const onAttachClick = () => {
    nextRef.current.click();
  };

  return (
    <div style={{ height: '80vh' }}>
      <input
        type='file'
        ref={nextRef}
        onChange={handleFile}
        accept='image/jpg, image/jpeg, image/png, image/svg+xml, image/svg'
        className='d-none'
      />
      <MainContainer responsive className='chat-container rounded'>
        <Sidebar position='left' scrollable={false} className='chat-sidebar'>
          <ConversationList>
            {chats
              .filter((item) =>
                user.role == 'admin'
                  ? item.roleId == 'admin'
                  : user.role == 'seller'
                  ? item.roleId == myShop.id
                  : item.roleId == user.id
              )
              .map((chat) => {
                const { lastMessage, unreadMessages } = getChatDetails(
                  chat,
                  messages
                );
                return (
                  <Conversation
                    key={chat.id}
                    name={`${chat.user?.firstname} ${chat.user?.lastname}`}
                    lastSenderName={
                      Boolean(lastMessage?.sender)
                        ? chat.user?.firstname
                        : t('you')
                    }
                    info={lastMessage?.chat_content}
                    onClick={() => handleChatClick(chat, unreadMessages)}
                    unreadCnt={unreadMessages.length}
                  >
                    <Avatar
                      src={getAvatar(chat.user?.img)}
                      // status='invisible'
                      name={chat.user?.firstname}
                    />
                    <Conversation.Content userName={chat.user?.firstname} />
                  </Conversation>
                );
              })}
          </ConversationList>
        </Sidebar>

        <ChatContainer className='chat-container'>
          {currentChat ? (
            <ConversationHeader className='chat-header'>
              <ConversationHeader.Back />
              <Avatar
                src={getAvatar(currentChat?.user?.img)}
                name={currentChat?.user?.firstname}
              />
              <ConversationHeader.Content
                userName={`${currentChat?.user?.firstname} ${currentChat?.user?.lastname}`}
              />
              <ConversationHeader.Actions>
                <Dropdown
                  overlay={
                    <Menu
                      items={[
                        {
                          key: '1',
                          label: <div>{t('delete.chat')}</div>,
                          icon: <DeleteOutlined />,
                          onClick: () => setIsModalVisible(true),
                        },
                      ]}
                    />
                  }
                >
                  <span className='more-btn'>
                    <MoreOutlined style={{ fontSize: 22 }} />
                  </span>
                </Dropdown>
              </ConversationHeader.Actions>
            </ConversationHeader>
          ) : (
            ''
          )}
          <MessageList className='message-list'>
            <Channel
              groupMessages={groupMessages}
              messageEndRef={messageEndRef}
            />
          </MessageList>
          {groupMessages.length ? (
            <MessageInput
              ref={inputRef}
              value={newMessage}
              onChange={handleOnChange}
              onSend={handleOnSubmit}
              placeholder='Message'
              className='chat-input'
              onAttachClick={onAttachClick}
            />
          ) : (
            ''
          )}
        </ChatContainer>
      </MainContainer>
      <UploadMedia
        modal={modal}
        url={url}
        setModal={setModal}
        file={file}
        handleOnSubmit={handleOnSubmit}
      />
      <CustomModal click={deleteCurrentChat} text={t('delete.chat')} />
    </div>
  );
}
