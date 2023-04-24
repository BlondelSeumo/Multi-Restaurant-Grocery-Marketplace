import { getMessaging, getToken, onMessage } from "firebase/messaging";
import app from "services/firebase";
import { VAPID_KEY } from "constants/config";
import profileService from "services/profile";
import { IPushNotification } from "interfaces";

type INotification = {
  notification?: IPushNotification;
};

export const getNotification = (
  setNotification: (data?: IPushNotification) => void
) => {
  const messaging = getMessaging(app);
  getToken(messaging, { vapidKey: VAPID_KEY })
    .then((currentToken) => {
      if (currentToken) {
        profileService
          .firebaseTokenUpdate({ firebase_token: currentToken })
          .then(() => {})
          .catch((error) => {
            console.log(error);
          });

        onMessage(messaging, (payload: INotification) => {
          setNotification(payload?.notification);
        });
      } else {
        console.log(
          "No registration token available. Request permission to generate one."
        );
      }
    })
    .catch((err) => {
      console.log("An error occurred while retrieving token. ", err);
    });
};
