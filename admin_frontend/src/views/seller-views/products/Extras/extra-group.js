import React, { useEffect, useState, useContext } from 'react';
import { Button, Card, Space, Table } from 'antd';
import {
  DeleteOutlined,
  EditOutlined,
  EyeOutlined,
  PlusCircleOutlined,
  PlusOutlined,
} from '@ant-design/icons';
import { toast } from 'react-toastify';
import { useTranslation } from 'react-i18next';
import extraService from '../../../../services/seller/extras';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { fetchSelletExtraGroups } from '../../../../redux/slices/extraGroup';
import { disableRefetch, setMenuData } from '../../../../redux/slices/menu';
import ExtraGroupModal from './extra-group-modal';
import DeleteButton from '../../../../components/delete-button';
import ExtraGroupShowModal from './extra-group-show-modal';
import FilterColumns from '../../../../components/filter-column';
import CustomModal from '../../../../components/modal';
import { Context } from '../../../../context/context';

import SearchInput from '../../../../components/search-input';

export default function SellerExtraGroup() {
  const { t } = useTranslation();
  const dispatch = useDispatch();
  const { setIsModalVisible } = useContext(Context);
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const { extraGroups, loading } = useSelector(
    (state) => state.extraGroup,
    shallowEqual
  );

  const [id, setId] = useState(null);
  const [show, setShow] = useState(null);
  const [modal, setModal] = useState(null);
  const [loadingBtn, setLoadingBtn] = useState(false);
  const [text, setText] = useState(null);

  const [columns, setColumns] = useState([
    {
      title: t('id'),
      dataIndex: 'id',
      key: 'id',
      is_show: true,
    },
    {
      title: t('title'),
      dataIndex: 'translation',
      key: 'translation',
      is_show: true,
      render: (translation) => translation?.title,
    },
    {
      title: t('type'),
      dataIndex: 'type',
      key: 'type',
      is_show: true,
    },
    {
      title: t('options'),
      is_show: true,
      render: (record) => (
        <Space>
          <Button
            type='primary'
            icon={<EyeOutlined />}
            onClick={() => setShow(record.id)}
          />
          <Button
            type='primary'
            icon={<EditOutlined />}
            onClick={() => setModal(record)}
          />
          <DeleteButton
            type='primary'
            danger
            icon={<DeleteOutlined />}
            onClick={() => {
              setIsModalVisible(true);
              setId([record.id]);
              setText(true);
            }}
          />
        </Space>
      ),
    },
  ]);

  const handleCancel = () => {
    setShow(null);
    setModal(null);
  };

  const onDeleteExtra = () => {
    setLoadingBtn(true);
    const params = {
      ...Object.assign(
        {},
        ...id.map((item, index) => ({
          [`ids[${index}]`]: item,
        }))
      ),
    };
    extraService
      .deleteGroup(params)
      .then(() => {
        setIsModalVisible(false);
        toast.success(t('successfully.deleted'));
        setId(null);
        dispatch(fetchSelletExtraGroups());
      })
      .finally(() => setLoadingBtn(false));
  };

  useEffect(() => {
    if (activeMenu.refetch) {
      dispatch(fetchSelletExtraGroups());
      dispatch(disableRefetch(activeMenu));
    }
  }, [activeMenu.refetch]);

  const rowSelection = {
    selectedRowKeys: id,
    onChange: (key) => {
      setId(key);
    },
  };

  const allDelete = () => {
    if (id === null || id.length === 0) {
      toast.warning(t('select.the.product'));
    } else {
      setIsModalVisible(true);
      setText(false);
    }
  };

  const handleFilter = (item, name) => {
    dispatch(
      setMenuData({
        activeMenu,
        data: { ...activeMenu.data, [name]: item },
      })
    );
  };

  return (
    <Card
      title={t('extra.group')}
      extra={
        <Space wrap>
          <Button
            type='primary'
            icon={<PlusCircleOutlined />}
            onClick={() => setModal({})}
          >
            {t('add.extra')}
          </Button>
          <DeleteButton size='' onClick={allDelete}>
            {t('delete.selected')}
          </DeleteButton>

          <FilterColumns columns={columns} setColumns={setColumns} />
        </Space>
      }
    >
      <Table
        scroll={{ x: true }}
        rowSelection={rowSelection}
        loading={loading}
        columns={columns?.filter((item) => item.is_show)}
        dataSource={extraGroups}
        rowKey={(record) => record.id}
        pagination={false}
      />
      {modal && <ExtraGroupModal modal={modal} handleCancel={handleCancel} />}
      <CustomModal
        click={onDeleteExtra}
        text={text ? t('delete') : t('all.delete')}
        loading={loadingBtn}
        setText={setId}
      />
      {show && <ExtraGroupShowModal open={show} handleClose={handleCancel} />}
    </Card>
  );
}
