import React, { useEffect, useState } from 'react';
import { Button, Card, Col, Modal, Row } from 'antd';
import { useTranslation } from 'react-i18next';
import subscriptionService from '../../../services/seller/subscriptions';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { fetchRestPayments } from '../../../redux/slices/payment';
import Loading from '../../../components/loading';
import { toast } from 'react-toastify';
import { fetchMyShop } from '../../../redux/slices/myShop';
import Paystack from '../../../assets/images/paystack.svg';
import { FaPaypal } from 'react-icons/fa';
import { SiStripe, SiRazorpay } from 'react-icons/si';
import { AiOutlineWallet } from 'react-icons/ai';
import restPaymentService from '../../../services/rest/payment';
const acceptedPayments = ['wallet'];

export default function SellerSubscriptionModal({ modal, handleCancel }) {
  const { t } = useTranslation();
  const dispatch = useDispatch();
  const { payments, loading } = useSelector(
    (state) => state.payment,
    shallowEqual
  );
  const { myShop: shop } = useSelector((state) => state.myShop, shallowEqual);
  const { seller } = useSelector((state) => state.myShop.myShop, shallowEqual);
  const [loadingBtn, setLoadingBtn] = useState(false);
  const [paymentType, setPaymentType] = useState({});
  const [paymentData, setPaymentData] = useState(null);
  const [paymentData2, setPaymentData2] = useState(null);

  const { payment_type } = useSelector(
    (state) => state.globalSettings.settings,
    shallowEqual
  );

  async function fetchSellerPaymentList() {
    return await restPaymentService.getById(shop.id).then(({ data }) =>
      setPaymentData(
        data.map((item) => ({
          label: item.payment.tag || 'no name',
          value: item.payment.id,
          key: item.payment.id,
        }))
      )
    );
  }
  async function fetchPaymentList() {
    return restPaymentService.getAll().then(({ data }) =>
      setPaymentData2(
        data.map((item) => ({
          label: item.tag || 'no name',
          value: item.id,
          key: item.id,
        }))
      )
    );
  }

  useEffect(() => {
    if (!payments.length) {
      dispatch(fetchRestPayments());
    }
    fetchSellerPaymentList();
    fetchPaymentList();
  }, []);

  const handleSubmit = () => {
    if (!paymentType.value) {
      toast.warning(t('please.select.payment.type'));
      return;
    }
    if (paymentType.label === 'wallet' && seller?.wallet?.price < modal.price) {
      toast.warning(t('insufficient.balance'));
      return;
    }
    setLoadingBtn(true);
    subscriptionService
      .attach(modal.id)
      .then(({ data }) => transactionCreate(data.id))
      .error(() => setLoadingBtn(false));
  };

  function transactionCreate(id) {
    const payload = {
      payment_sys_id: paymentType.value,
    };
    subscriptionService
      .transactionCreate(id, payload)
      .then(() => {
        handleCancel();
        toast.success(t('successfully.purchased'));
        dispatch(fetchMyShop());
      })
      .finally(() => setLoadingBtn(false));
  }

  const selectPayment = (type) => {
    if (!acceptedPayments.includes(type.label)) {
      toast.warning(t('cannot.work.demo'));
      return;
    }
    setPaymentType(type);
  };

  const handleAddIcon = (data) => {
    switch (data) {
      case 'wallet':
        return <AiOutlineWallet size={80} />;
      case 'paypal':
        return <FaPaypal size={80} />;
      case 'stripe':
        return <SiStripe size={80} />;
      case 'razorpay':
        return <SiRazorpay size={80} />;
      case 'paystack':
        return <img src={Paystack} alt='img' width='80' height='80' />;
    }
  };

  return (
    <Modal
      visible={!!modal}
      title={t('purchase.subscription')}
      onCancel={handleCancel}
      footer={[
        <Button
          type='primary'
          onClick={handleSubmit}
          loading={loadingBtn}
          key='save-btn'
        >
          {t('save')}
        </Button>,
        <Button type='default' onClick={handleCancel} key='cancel-btn'>
          {t('cancel')}
        </Button>,
      ]}
    >
      {!loading ? (
        <Row gutter={12}>
          {(payment_type === 'admin' ? paymentData2 : paymentData)
            ?.filter((item) => item?.label !== 'cash')
            ?.map((item, index) => (
              <Col span={8} key={index}>
                <Card
                  className={`payment-card ${
                    paymentType?.label === item.label ? 'active' : ''
                  }`}
                  onClick={() => selectPayment(item)}
                >
                  <div className='payment-icon'>
                    {handleAddIcon(item?.label)}
                  </div>
                  <div className='font-weight-bold mt-2'>{t(item?.label)}</div>
                </Card>
              </Col>
            ))}
        </Row>
      ) : (
        <Loading />
      )}
    </Modal>
  );
}
