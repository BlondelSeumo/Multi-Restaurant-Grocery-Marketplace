import React from "react";
import { PopoverProps } from "@mui/material";
import PopoverContainer from "containers/popover/popover";
import cls from "./deliveryTimePopover.module.scss";
import Link from "next/link";
import { useTranslation } from "react-i18next";
import { FormikProps } from "formik";
import { OrderFormValues } from "interfaces";
import dayjs from "dayjs";
import roundedDeliveryTime from "utils/roundedDeliveryTime";

interface Props extends PopoverProps {
  weekDay: string;
  time: string;
  handleOpenDrawer: () => void;
  formik?: FormikProps<OrderFormValues>;
}

export default function DeliveryTimePopover({
  weekDay,
  time,
  handleOpenDrawer,
  formik,
  ...rest
}: Props) {
  const { t } = useTranslation();

  const handleClose = (event: any) => {
    event.preventDefault();
    if (rest.onClose) rest.onClose({}, "backdropClick");
  };

  const handleSelectStandartTime = (event: any) => {
    const estimatedDeliveryDuration = Number(time);
    const standardTime = roundedDeliveryTime(
      dayjs().add(estimatedDeliveryDuration, "minute"),
      estimatedDeliveryDuration
    );
    const standardDate = dayjs().format("YYYY-MM-DD");
    formik?.setFieldValue("delivery_date", standardDate);
    formik?.setFieldValue("delivery_time", standardTime);
    handleClose(event);
  };

  return (
    <PopoverContainer
      {...rest}
      anchorOrigin={{ vertical: "bottom", horizontal: "right" }}
      transformOrigin={{ vertical: "top", horizontal: "right" }}
    >
      <div className={cls.wrapper}>
        <Link href="/" className={cls.link} onClick={handleSelectStandartTime}>
          <span className={cls.text}>
            {t("today")} — {time} {t("min")}
          </span>
        </Link>
        <Link
          href="/"
          className={cls.link}
          onClick={(event) => {
            handleClose(event);
            handleOpenDrawer();
          }}
        >
          <span className={cls.text}>{t("schedule")}</span>
        </Link>
      </div>
    </PopoverContainer>
  );
}
