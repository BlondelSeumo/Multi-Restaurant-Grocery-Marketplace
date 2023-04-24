import React from "react";
import cls from "./checkoutDelivery.module.scss";
import TextInput from "components/inputs/textInput";
import ArrowRightSLineIcon from "remixicon-react/ArrowRightSLineIcon";
import CalendarCheckLineIcon from "remixicon-react/CalendarCheckLineIcon";
import MapPinRangeFillIcon from "remixicon-react/MapPinRangeFillIcon";
import PencilFillIcon from "remixicon-react/PencilFillIcon";
import { useTranslation } from "react-i18next";
import usePopover from "hooks/usePopover";
import dayjs from "dayjs";
import { FormikProps } from "formik";
import { OrderFormValues, IShop } from "interfaces";
import useModal from "hooks/useModal";
import { useMediaQuery } from "@mui/material";
import dynamic from "next/dynamic";
import checkIsDisabledDay from "utils/checkIsDisabledDay";

const DeliveryTimes = dynamic(
  () => import("components/deliveryTimes/deliveryTimes")
);
const MobileDrawer = dynamic(() => import("containers/drawer/mobileDrawer"));
const DeliveryTimePopover = dynamic(
  () => import("components/deliveryTimePopover/deliveryTimePopover")
);
const DeliveryAddressModal = dynamic(
  () => import("components/addressModal/deliveryAddressModal")
);
const ModalContainer = dynamic(() => import("containers/modal/modal"));

type Props = {
  data: IShop;
  formik: FormikProps<OrderFormValues>;
};

export default function CheckoutDeliveryForm({ formik, data }: Props) {
  const { t } = useTranslation();
  const isDesktop = useMediaQuery("(min-width:1140px)");
  const [timePopover, anchorEl, handleOpenTimePopover, handleCloseTimePopover] =
    usePopover();
  const [timeDrawer, handleOpenTimeDrawer, handleCloseTimeDrawer] = useModal();
  const [addressModal, handleOpenAddressModal, handleCloseAddressModal] =
    useModal();

  const { delivery_date, delivery_time, address, location } = formik.values;
  const isToday = dayjs(delivery_date).isSame(dayjs().format("YYYY-MM-DD"));
  const isTomorrow = dayjs(delivery_date).isSame(
    dayjs().add(1, "day").format("YYYY-MM-DD")
  );
  const day = dayjs(delivery_date).format("ddd");

  const openTimePicker = (event: any) => {
    if (checkIsDisabledDay(0, data)) {
      handleOpenTimeDrawer();
    } else {
      handleOpenTimePopover(event);
    }
  };

  const handleChangeDeliverySchedule = ({
    date,
    time,
  }: {
    date: string;
    time: string;
  }) => {
    formik.setFieldValue("delivery_time", time);
    formik.setFieldValue("delivery_date", date);
  };

  return (
    <>
      <div className={cls.row}>
        <button type="button" className={cls.rowBtn} onClick={openTimePicker}>
          <div className={cls.item}>
            <CalendarCheckLineIcon />
            <div className={cls.naming}>
              <div className={cls.label}>{t("delivery.time")}</div>
              <div className={cls.value}>
                {isToday ? t("today") : isTomorrow ? t("tomorrow") : day},{" "}
                {delivery_time}
              </div>
            </div>
          </div>
          <div className={cls.icon}>
            <PencilFillIcon />
          </div>
        </button>
        <button
          type="button"
          className={cls.rowBtn}
          onClick={handleOpenAddressModal}
        >
          <div className={cls.item}>
            <MapPinRangeFillIcon />
            <div className={cls.naming}>
              <div className={cls.label}>{t("delivery.address")}</div>
              <div className={cls.value}>{address?.address}</div>
            </div>
          </div>
          <div className={cls.icon}>
            <ArrowRightSLineIcon />
          </div>
        </button>
      </div>
      <div className={cls.form}>
        <div className={cls.flex}>
          <TextInput
            name="address.office"
            label={t("office")}
            value={formik.values.address?.office}
            onChange={formik.handleChange}
            placeholder={t("type.here")}
          />
          <TextInput
            name="address.house"
            label={t("house")}
            value={formik.values.address?.house}
            onChange={formik.handleChange}
            placeholder={t("type.here")}
          />
          <TextInput
            name="address.floor"
            label={t("floor")}
            value={formik.values.address?.floor}
            onChange={formik.handleChange}
            placeholder={t("type.here")}
          />
        </div>
        <TextInput
          name="note"
          label={t("comment")}
          value={formik.values.note}
          onChange={formik.handleChange}
          placeholder={t("type.here")}
        />
      </div>
      <DeliveryTimePopover
        open={timePopover}
        anchorEl={anchorEl}
        onClose={handleCloseTimePopover}
        weekDay={isToday ? t("today") : isTomorrow ? t("tomorrow") : day}
        time={data.delivery_time?.to || "0"}
        handleOpenDrawer={handleOpenTimeDrawer}
        formik={formik}
      />
      {isDesktop ? (
        <ModalContainer open={timeDrawer} onClose={handleCloseTimeDrawer}>
          <DeliveryTimes
            data={data}
            handleClose={handleCloseTimeDrawer}
            handleChangeDeliverySchedule={handleChangeDeliverySchedule}
          />
        </ModalContainer>
      ) : (
        <MobileDrawer open={timeDrawer} onClose={handleCloseTimeDrawer}>
          <DeliveryTimes
            data={data}
            handleClose={handleCloseTimeDrawer}
            handleChangeDeliverySchedule={handleChangeDeliverySchedule}
          />
        </MobileDrawer>
      )}
      <DeliveryAddressModal
        open={addressModal}
        onClose={handleCloseAddressModal}
        latlng={location}
        address={address?.address || ""}
        fullScreen={!isDesktop}
        formik={formik}
      />
    </>
  );
}
