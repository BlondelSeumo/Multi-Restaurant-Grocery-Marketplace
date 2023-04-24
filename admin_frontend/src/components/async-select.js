import React, { useState } from 'react';
import { Select, Spin } from 'antd';

export const AsyncSelect = ({ fetchOptions, refetch = false, ...props }) => {
  const [fetching, setFetching] = useState(false);
  const [options, setOptions] = useState([]);

  const fetchOnFocus = () => {
    if (!options.length || refetch) {
      setFetching(true);
      fetchOptions().then((newOptions) => {
        setOptions(newOptions);
        setFetching(false);
      });
    }
  };

  return (
    <Select
      labelInValue={true}
      filterOption={false}
      notFoundContent={fetching ? <Spin size='small' /> : 'no results'}
      {...props}
      options={fetching ? [] : options}
      onFocus={fetchOnFocus}
    />
  );
};
