import { createSlice } from '@reduxjs/toolkit';

const initialState = {
  chats: [],
  messages: [],
  currentChat: null,
  newMessage: '',
};

const chatSlice = createSlice({
  name: 'chat',
  initialState,
  reducers: {
    setChats(state, action) {
      state.chats = action.payload;
    },
    setMessages(state, action) {
      state.messages = action.payload;
    },
    setCurrentChat(state, action) {
      state.currentChat = action.payload;
    },
    addMessage(state, action) {
      state.messages.push(action.payload);
    },
    setNewMessage(state, action) {
      state.newMessage = action.payload;
    },
    removeCurrentChat(state) {
      state.currentChat = null;
    },
  },
});

export const {
  setChats,
  setMessages,
  setCurrentChat,
  addMessage,
  setNewMessage,
  removeCurrentChat,
} = chatSlice.actions;
export default chatSlice.reducer;
