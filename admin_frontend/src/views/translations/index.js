import React, { useContext, useEffect, useMemo, useRef, useState } from 'react';
import { Button, Card, Form, Input, Select, Space, Table } from 'antd';
import translationService from '../../services/translation';
import { toast } from 'react-toastify';
import { EditOutlined, PlusCircleOutlined } from '@ant-design/icons';
import TranslationCreateModal from './translationCreateModal';
import { shallowEqual, useSelector } from 'react-redux';
import { useTranslation } from 'react-i18next';
import SearchInput from '../../components/search-input';
const EditableContext = React.createContext(null);

const EditableRow = ({ index, ...props }) => {
  const [form] = Form.useForm();
  return (
    <Form form={form} component={false}>
      <EditableContext.Provider value={form}>
        <tr {...props} />
      </EditableContext.Provider>
    </Form>
  );
};

const EditableCell = ({
  title,
  editable,
  children,
  dataIndex,
  record,
  handleSave,
  ...restProps
}) => {
  const { t } = useTranslation();
  const [editing, setEditing] = useState(false);
  const inputRef = useRef(null);
  const form = useContext(EditableContext);
  useEffect(() => {
    if (editing) {
      inputRef.current.focus();
    }
  }, [editing]);

  const toggleEdit = () => {
    setEditing(!editing);
    form.setFieldsValue({
      [dataIndex]: record[dataIndex],
    });
  };

  const save = async () => {
    try {
      const values = await form.validateFields();
      toggleEdit();
      handleSave({ ...record, ...values, dataIndex });
    } catch (errInfo) {
      console.log('Save failed:', errInfo);
    }
  };

  let childNode = children;

  if (editable) {
    childNode = editing ? (
      <Form.Item
        style={{
          margin: 0,
        }}
        name={dataIndex}
        rules={[
          {
            required: true,
            message: t('required'),
          },
        ]}
      >
        <Input ref={inputRef} onPressEnter={save} onBlur={save} />
      </Form.Item>
    ) : (
      <div
        className='editable-cell-value-wrap cursor-pointer d-flex justify-content-between align-items-center'
        style={{
          paddingRight: 24,
        }}
        onClick={toggleEdit}
      >
        <div className='w-100'>{children}</div>
        <EditOutlined />
      </div>
    );
  }

  return <td {...restProps}>{childNode}</td>;
};

export default function Translations() {
  const { t } = useTranslation();
  const [list, setList] = useState([]);
  const [pageSize, setPageSize] = useState(10);
  const [page, setPage] = useState(1);
  const [total, setTotal] = useState(0);
  const [loading, setLoading] = useState(false);
  const [group, setGroup] = useState(null);
  const [sort, setSort] = useState(null);
  const [column, setColumn] = useState(null);
  const [visible, setVisible] = useState(false);
  const [skipPage, setSkipPage] = useState(0);
  const { languages } = useSelector((state) => state.formLang, shallowEqual);
  const [locale, setLocale] = useState('');
  const [search, setSearch] = useState('');

  const defaultColumns = useMemo(
    () => [
      {
        title: t('name'),
        dataIndex: 'key',
        sorter: (a, b, sortOrder) => sortTable(sortOrder, 'key'),
        width: 250,
        fixed: 'left',
      },
      {
        title: t('group'),
        dataIndex: 'group',
        sorter: (a, b, sortOrder) => sortTable(sortOrder, 'group'),
        width: 150,
        fixed: 'left',
      },
      ...languages
        .filter((item) => (locale ? item.locale === locale : true))
        .map((item) => ({
          title: item.title,
          dataIndex: `value[${item.locale}]`,
          editable: true,
          width: 300,
        })),
    ],
    [languages, locale]
  );

  function sortTable(type, column) {
    let sortType;
    switch (type) {
      case 'ascend':
        sortType = 'asc';
        break;
      case 'descend':
        sortType = 'desc';
        break;

      default:
        break;
    }
    setSort(sortType);
    setColumn(column);
  }

  function fetchTranslations() {
    setLoading(true);
    const params = {
      perPage: pageSize,
      skip: skipPage,
      group,
      sort,
      column,
      search,
    };
    translationService
      .getAll(params)
      .then(({ data }) => {
        const translations = Object.entries(data.translations).map((item) => ({
          key: item[0],
          group: item[1][0].group,
          ...Object.assign(
            {},
            ...languages.map((lang) => ({
              [`value[${lang.locale}]`]: item[1].find(
                (el) => el.locale === lang.locale
              )?.value,
            }))
          ),
        }));
        setList(translations);
        setTotal(data.total);
      })
      .finally(() => setLoading(false));
  }

  useEffect(() => {
    fetchTranslations();
  }, [pageSize, group, sort, column, skipPage, search]);

  const onChangePagination = (pageNumber) => {
    const { pageSize, current } = pageNumber;
    const skip = (current - 1) * pageSize;
    setPageSize(pageSize);
    setPage(current);
    setSkipPage(skip);
  };

  const handleSave = (row) => {
    const { dataIndex, key } = row;
    const newData = [...list];
    const index = newData.findIndex((item) => row.key === item.key);
    const item = newData[index];
    if (item[dataIndex] === row[dataIndex]) {
      return;
    }
    newData.splice(index, 1, { ...item, ...row });
    setList(newData);
    const savedItem = {
      ...row,
      value: undefined,
      dataIndex: undefined,
      key: undefined,
    };
    updateTranslation(key, savedItem);
  };

  function updateTranslation(key, data) {
    translationService
      .update(key, data)
      .then((res) => toast.success(res.message));
  }

  const components = {
    body: {
      row: EditableRow,
      cell: EditableCell,
    },
  };

  const columns = defaultColumns.map((col) => {
    if (!col.editable) {
      return col;
    }

    return {
      ...col,
      onCell: (record) => ({
        record,
        editable: col.editable,
        dataIndex: col.dataIndex,
        title: col.title,
        fixed: col.fixed,
        handleSave,
      }),
    };
  });

  return (
    <Card
      title={t('translations')}
      extra={
        <Space wrap>
          <SearchInput
            placeholder={t('search')}
            handleChange={(search) => setSearch(search)}
          />
          <Select
            style={{ minWidth: 150 }}
            value={locale}
            onChange={(value) => setLocale(value)}
            placeholder={t('select.language')}
          >
            <Select.Option value=''>{t('all')}</Select.Option>
            {languages.map((item) => (
              <Select.Option key={item.locale} value={item.locale}>
                {item.title}
              </Select.Option>
            ))}
          </Select>
          <Select
            style={{ minWidth: 150 }}
            value={group}
            onChange={(value) => setGroup(value)}
            placeholder={t('select.group')}
          >
            <Select.Option value=''>{t('all')}</Select.Option>
            <Select.Option value='web'>{t('web')}</Select.Option>
            <Select.Option value='mobile'>{t('mobile')}</Select.Option>
            <Select.Option value='errors'>{t('errors')}</Select.Option>
          </Select>
          <Button
            icon={<PlusCircleOutlined />}
            type='primary'
            onClick={() => setVisible(true)}
          >
            {t('add.translation')}
          </Button>
        </Space>
      }
    >
      <Table
        components={components}
        columns={columns}
        dataSource={list}
        pagination={{
          pageSize,
          page,
          total,
        }}
        rowKey={(record) => record.id}
        onChange={onChangePagination}
        loading={loading}
        scroll={{
          x: 1500,
        }}
      />
      {visible && (
        <TranslationCreateModal
          visible={visible}
          setVisible={setVisible}
          languages={languages}
          refetch={fetchTranslations}
        />
      )}
    </Card>
  );
}
