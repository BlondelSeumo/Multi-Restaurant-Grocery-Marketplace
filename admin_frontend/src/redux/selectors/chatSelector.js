import moment from 'moment';

export function getMessages(chat) {
  const { messages, currentChat } = chat;
  if (!currentChat) return [];
  const groups = messages
    .filter((item) => item.chat_id === currentChat.id)
    .reduce((groups, item) => {
      const date = moment(new Date(item.created_at)).format('DD-MM-YYYY');
      if (!groups[date]) {
        groups[date] = [];
      }
      groups[date].push(item);
      return groups;
    }, {});
  const groupArrays = Object.keys(groups).map((date) => {
    return {
      date,
      messages: groups[date],
    };
  });
  return groupArrays;
}

export function getAllUnreadMessages(messages) {
  return messages.filter((item) => item.unread && Boolean(item.sender));
}

export function getChatDetails(chat, messages) {
  const chatMessages = messages.filter((item) => item.chat_id === chat.id);
  const lastMessage = chatMessages[chatMessages.length - 1];
  const unreadMessages = chatMessages.filter(
    (item) => item.unread && Boolean(item.sender)
  );

  return { lastMessage, unreadMessages };
}
