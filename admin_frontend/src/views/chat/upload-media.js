import React from 'react';
import { getDownloadURL, ref, uploadBytesResumable } from 'firebase/storage';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { setNewMessage } from '../../redux/slices/chat';
import { toast } from 'react-toastify';
import { storage } from '../../firebase';
import { Button, Modal } from 'antd';
import TextArea from 'antd/lib/input/TextArea';

const UploadMedia = ({
  modal,
  url,
  setModal,
  setPercent = () => {},
  file,
  handleOnSubmit,
}) => {
  const dispatch = useDispatch();
  const toggle = () => setModal(!modal);
  const { newMessage } = useSelector((state) => state.chat, shallowEqual);
  const handleUpload = () => {
    if (!file) {
      toast.error('Please upload an image first!');
    }
    const storageRef = ref(storage, `/files/${file.name}`);
    const uploadTask = uploadBytesResumable(storageRef, file);
    uploadTask.on(
      'state_changed',
      (snapshot) => {
        const percent = Math.round(
          (snapshot.bytesTransferred / snapshot.totalBytes) * 100
        );
        setPercent(percent);
        if (percent === 100) {
        }
      },
      (err) => console.log(err),
      () => {
        getDownloadURL(uploadTask.snapshot.ref).then((url) => {
          handleOnSubmit(url);
        });
      }
    );
  };
  const handleChange = (text) => {
    dispatch(setNewMessage(text));
  };
  return (
    <Modal visible={modal} footer={false} onOk={toggle} onCancel={toggle}>
      <div className='upload-form'>
        <img src={url} />
        <TextArea
          value={newMessage}
          rows={4}
          placeholder='Message'
          label='Caption'
          onChange={(e) => {
            handleChange(e.target.value);
          }}
        />
        <div className='footer-btns'>
          <Button onClick={toggle}>Cancel</Button>
          <Button onClick={handleUpload}>Send</Button>
        </div>
      </div>
    </Modal>
  );
};

export default UploadMedia;
