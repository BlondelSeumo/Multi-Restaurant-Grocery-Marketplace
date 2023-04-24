import React, { useState } from 'react';
import { data } from '../../configs/menu-config';
import { LockOutlined, MailOutlined } from '@ant-design/icons';
import {
  Button,
  Card,
  Col,
  Descriptions,
  Form,
  Input,
  notification,
  Row,
  Typography,
} from 'antd';
import authService from '../../services/auth';
import { useDispatch, useSelector } from 'react-redux';
import { setUserData } from '../../redux/slices/auth';
import {
  fetchRestSettings,
  fetchSettings,
} from '../../redux/slices/globalSettings';
import { useTranslation } from 'react-i18next';
import { PROJECT_NAME } from '../../configs/app-global';
import { requestForToken } from '../../firebase';
const { Title } = Typography;

const credentials = [
  {
    login: 'admin@githubit.com',
    password: 'githubit',
  },
  {
    login: 'manager@githubit.com',
    password: 'manager',
  },
  {
    login: 'sellers@githubit.com',
    password: 'seller',
  },
  {
    login: 'moderator@githubit.com',
    password: 'moderator',
  },
  {
    login: 'delivery@githubit.com',
    password: 'delivery',
  },
];

const Login = () => {
  const { t } = useTranslation();
  const dispatch = useDispatch();
  const [form] = Form.useForm();
  const [loading, setLoading] = useState(false);
  const { settings } = useSelector((state) => state.globalSettings);

  const handleLogin = (values) => {
    const body = {
      email: values.email,
      password: values.password,
    };
    setLoading(true);
    authService
      .login(body)
      .then((res) => {
        const user = {
          fullName: res.data.user.firstname + ' ' + res.data.user.lastname,
          role: res.data.user.role,
          urls: data[res.data.user.role],
          img: res.data.user.img,
          token: res.data.access_token,
          email: res.data.user.email,
          id: res.data.user.id,
        };
        if (user.role === 'user') {
          notification.error({
            message: t('ERROR_101'),
          });
          return;
        }
        localStorage.setItem('token', res.data.access_token);
        dispatch(setUserData(user));
        if (user.role === 'admin') {
          dispatch(fetchSettings());
        } else {
          dispatch(fetchRestSettings());
        }
      })
      .finally(() => setLoading(false));
  };

  const copyCredentials = (event, item) => {
    event.preventDefault();
    form.setFieldsValue({ email: item.login, password: item.password });
  };

  return (
    <div className='login-container'>
      <div className='container d-flex flex-column justify-content-center h-100'>
        <Row justify='center' className='w-100'>
          <Col>
            <Card className='card'>
              <div className='my-4 pl-4 pr-4 w-100'>
                <div className='app-brand text-center'>
                  <Title className='brand-logo'>
                    {settings.title || PROJECT_NAME}
                  </Title>
                </div>
                <Row justify='center'>
                  <Col>
                    <Form
                      name='login-form'
                      layout='vertical'
                      form={form}
                      onFinish={handleLogin}
                      style={{ width: '420px' }}
                    >
                      <Form.Item
                        name='email'
                        label='Email'
                        rules={[
                          {
                            required: true,
                            message: 'Please input your Email!',
                          },
                        ]}
                      >
                        <Input
                          prefix={
                            <MailOutlined className='site-form-item-icon' />
                          }
                          placeholder='Email'
                        />
                      </Form.Item>

                      <Form.Item
                        name='password'
                        label='Password'
                        rules={[
                          {
                            required: true,
                            message: 'Please input your password!',
                          },
                        ]}
                      >
                        <Input.Password
                          prefix={
                            <LockOutlined className='site-form-item-icon' />
                          }
                          placeholder='Password'
                        />
                      </Form.Item>

                      <Form.Item className='login-input'>
                        <Button
                          type='primary'
                          htmlType='submit'
                          className='login-form-button'
                          loading={loading}
                        >
                          {t('Login')}
                        </Button>
                      </Form.Item>
                      {
                        <Descriptions bordered size='small'>
                          {credentials.map((item, idx) => (
                            <React.Fragment key={idx}>
                              <Descriptions.Item span={2} label={item.login}>
                                {item.password}
                              </Descriptions.Item>
                              <Descriptions.Item span={1}>
                                <a
                                  href='/'
                                  className='copy-link'
                                  onClick={(event) =>
                                    copyCredentials(event, item)
                                  }
                                >
                                  {t('Copy')}
                                </a>
                              </Descriptions.Item>
                            </React.Fragment>
                          ))}
                        </Descriptions>
                      }
                    </Form>
                  </Col>
                </Row>
              </div>
            </Card>
          </Col>
        </Row>
      </div>
    </div>
  );
};
export default Login;
