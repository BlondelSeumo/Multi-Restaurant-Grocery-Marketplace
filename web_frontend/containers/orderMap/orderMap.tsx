import React from "react";
import Map from "components/map/map";
import { Order } from "interfaces";
import cls from "./orderMap.module.scss";
import { Skeleton } from "@mui/material";

type Props = {
  data?: Order;
  loading?: boolean;
};

export default function OrderMap({ data, loading = false }: Props) {
  const location = {
    lat: Number(data?.location?.latitude) || 0,
    lng: Number(data?.location?.longitude) || 0,
  };

  return (
    <div className={cls.wrapper}>
      {!loading ? (
        <Map
          location={location}
          readOnly
          shop={data?.delivery_type === "pickup" ? undefined : data?.shop}
        />
      ) : (
        <Skeleton variant="rectangular" className={cls.shimmer} />
      )}
    </div>
  );
}
