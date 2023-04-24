import { initializeApp } from 'firebase/app';
import { getMessaging, getToken, onMessage } from 'firebase/messaging';
import {
  getFirestore,
  collection,
  onSnapshot,
  query,
  orderBy,
  addDoc,
  serverTimestamp,
  updateDoc,
  doc,
  deleteDoc,
} from 'firebase/firestore';
import {
  API_KEY,
  APP_ID,
  AUTH_DOMAIN,
  MEASUREMENT_ID,
  MESSAGING_SENDER_ID,
  PROJECT_ID,
  STORAGE_BUCKET,
  VAPID_KEY,
} from './configs/app-global';
import { store } from './redux/store';
import { setChats, setMessages } from './redux/slices/chat';
import { toast } from 'react-toastify';
import userService from './services/seller/user';
import { getStorage } from 'firebase/storage';

const config = localStorage.getItem('persist:settings')
  ? JSON.parse(JSON.parse(localStorage.getItem('persist:settings')).settings)
  : {};

const firebaseConfig = {
  apiKey: config.api_key || API_KEY,
  authDomain: config.auth_domain || AUTH_DOMAIN,
  projectId: config.project_id || PROJECT_ID,
  storageBucket: config.storage_bucket || STORAGE_BUCKET,
  messagingSenderId: config.messaging_sender_id || MESSAGING_SENDER_ID,
  appId: config.app_id || APP_ID,
  measurementId: config.measurement_id || MEASUREMENT_ID,
};

const app = initializeApp(firebaseConfig);

const messaging = getMessaging();
const db = getFirestore(app);
export const storage = getStorage(app);

onSnapshot(
  query(collection(db, 'messages'), orderBy('created_at', 'asc')),
  (querySnapshot) => {
    const messages = querySnapshot.docs.map((x) => ({
      id: x.id,
      ...x.data(),
      created_at: String(new Date(x.data().created_at?.seconds * 1000)),
    }));
    store.dispatch(setMessages(messages));
  }
);

onSnapshot(
  query(collection(db, 'chats'), orderBy('created_at', 'asc')),
  (querySnapshot) => {
    const chats = querySnapshot.docs.map((x) => ({
      id: x.id,
      ...x.data(),
      created_at: String(new Date(x.data().created_at?.seconds * 1000)),
    }));
    store.dispatch(setChats(chats));
  }
);

export async function sendMessage(payload) {
  try {
    await addDoc(collection(db, 'messages'), {
      ...payload,
      created_at: serverTimestamp(),
    });
  } catch (error) {
    toast.error(error);
  }
}
export async function updateMessage(payload) {
  try {
    await updateDoc(doc(db, 'messages', payload.id), {
      unread: false,
    });
  } catch (error) {
    toast.error(error);
  }
}
export async function deleteChat(payload) {
  try {
    await deleteDoc(doc(db, 'chats', payload.id));
  } catch (error) {
    toast.error(error);
  }
}
export async function deleteMessage(payload) {
  try {
    await deleteDoc(doc(db, 'messages', payload.id));
  } catch (error) {
    toast.error(error);
  }
}

export const requestForToken = () => {
  return getToken(messaging, { vapidKey: config.vapid_key || VAPID_KEY })
    .then((currentToken) => {
      if (currentToken) {
        console.log('current token for client: ', currentToken);
        const payload = { firebase_token: currentToken };
        userService
          .profileFirebaseToken(payload)
          .then((res) => console.log('firebase token sent => ', res));
      } else {
        // Show permission request UI
        console.log(
          'No registration token available. Request permission to generate one.'
        );
      }
    })
    .catch((err) => {
      console.log('An error occurred while retrieving token. ', err);
    });
};

export const onMessageListener = () =>
  new Promise((resolve) => {
    onMessage(messaging, (payload) => {
      resolve(payload);
    });
  });
