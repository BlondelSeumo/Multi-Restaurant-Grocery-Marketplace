import React from "react";
import cls from "./checkoutDelivery.module.scss";
import { Box } from "@mui/material";
import { IShop, OrderFormValues } from "interfaces";
import { FormikProps } from "formik";
import CheckoutDeliveryTabs from "./checkoutDeliveryTabs";
import CheckoutDeliveryForm from "./checkoutDeliveryForm";
import CheckoutPickupForm from "./checkoutPickupForm";

type Props = {
  data: IShop;
  formik: FormikProps<OrderFormValues>;
};

export default function CheckoutDelivery({ data, formik }: Props) {
  const { delivery_type } = formik.values;

  return (
    <div className={cls.card}>
      <CheckoutDeliveryTabs data={data} formik={formik} />
      <Box display={delivery_type === "delivery" ? "block" : "none"}>
        <CheckoutDeliveryForm data={data} formik={formik} />
      </Box>
      <Box display={delivery_type === "delivery" ? "none" : "block"}>
        <CheckoutPickupForm data={data} formik={formik} />
      </Box>
    </div>
  );
}
