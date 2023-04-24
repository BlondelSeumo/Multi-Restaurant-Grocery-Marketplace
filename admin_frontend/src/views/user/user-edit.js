import React, { useEffect, useState } from 'react';
import { Card, Form, Tabs } from 'antd';
import { useParams } from 'react-router-dom';
import moment from 'moment';
import userService from '../../services/user';
import Loading from '../../components/loading';
import UserEditForm from './userEditForm';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { disableRefetch, setMenuData } from '../../redux/slices/menu';
import { useTranslation } from 'react-i18next';
import UserOrders from './userOrders';
import WalletHistory from './walletHistory';
import createImage from '../../helpers/createImage';
import UserPassword from './userPassword';
import DelivertSettingCreate from './add-deliveryman-settings';
import useDemo from '../../helpers/useDemo';
import hideEmail from '../../components/hideEmail';
const { TabPane } = Tabs;

const UserEdit = () => {
  const { t } = useTranslation();
  const [form] = Form.useForm();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const dispatch = useDispatch();
  const { uuid } = useParams();
  const [loading, setLoading] = useState(false);
  const [tab, setTab] = useState('edit');
  const [image, setImage] = useState([]);
  const [id, setId] = useState(null);
  const role = activeMenu?.data?.role;
  const { isDemo } = useDemo();

  const showUserData = (uuid) => {
    setLoading(true);
    userService
      .getById(uuid)
      .then((res) => {
        const data = res.data;
        const payload = {
          ...data,
          image: createImage(data.img),
        };
        dispatch(setMenuData({ activeMenu, data: payload }));
        form.setFieldsValue({
          firstname: data.firstname,
          lastname: data.lastname,
          email: isDemo ? hideEmail(data.email) : data.email,
          phone: data.phone,
          birthday: moment(data.birthday),
          gender: data.gender,
          password_confirmation: data.password_confirmation,
          password: data.password,
          image: [createImage(data.img)],
          shop_id:
            data.invitations.length !== 0
              ? data.invitations.map((i) => ({
                  label: i.shop?.translation?.title,
                  value: i.shop?.id,
                }))
              : undefined,
        });
        setImage([createImage(data.img)]);
        setId(res.data?.delivery_man_setting?.id);
      })
      .finally(() => {
        setLoading(false);
        dispatch(disableRefetch(activeMenu));
      });
  };

  useEffect(() => {
    if (activeMenu?.refetch) {
      showUserData(uuid);
    }
  }, [activeMenu?.refetch]);

  const onChange = (key) => setTab(key);

  return (
    <Card title={t('user.settings')}>
      {!loading ? (
        <Tabs
          activeKey={tab}
          onChange={onChange}
          tabPosition='left'
          size='small'
        >
          <TabPane key='edit' tab={t('edit.user')}>
            <UserEditForm
              data={activeMenu?.data}
              form={form}
              image={image}
              setImage={setImage}
              action_type={'edit'}
            />
          </TabPane>
          <TabPane key='order' tab={t('orders')}>
            <UserOrders data={activeMenu?.data} />
          </TabPane>
          {role === 'deliveryman' && (
            <TabPane key='delivery' tab={t('deliveryman')}>
              <DelivertSettingCreate id={id} data={activeMenu.data} />
            </TabPane>
          )}
          <TabPane key='wallet' tab={t('wallet')}>
            <WalletHistory data={activeMenu?.data} />
          </TabPane>
          <TabPane key='password' tab={t('password')}>
            <UserPassword data={activeMenu?.data} />
          </TabPane>
        </Tabs>
      ) : (
        <Loading />
      )}
    </Card>
  );
};

export default UserEdit;
