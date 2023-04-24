import React, { useState } from 'react';
import { Spin, TreeSelect } from 'antd';

const data = {
  title: '---',
  value: 0,
  key: 0,
};

export const AsyncTreeSelect = ({
  fetchOptions,
  refetch = false,
  ...props
}) => {
  const [fetching, setFetching] = useState(false);
  const [treeData, setTreeData] = useState([]);
  const fetchOnFocus = () => {
    if (!treeData.length || refetch) {
      setFetching(true);
      fetchOptions().then((newOptions) => {
        setTreeData(newOptions);
        setFetching(false);
      });
    }
  };

  return (
    <TreeSelect
      treeLine={true}
      treeData={fetching ? [] : [data, ...treeData]}
      treeDefaultExpandAll
      labelInValue
      onFocus={fetchOnFocus}
      notFoundContent={fetching ? <Spin size='small' /> : 'no results'}
      {...props}
    />
  );
};
