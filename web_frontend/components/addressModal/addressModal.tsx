import React, { useRef, useState } from "react";
import ModalContainer from "containers/modal/modal";
import { DialogProps, Grid } from "@mui/material";
import { useTranslation } from "react-i18next";
import cls from "./addressModal.module.scss";
import Search2LineIcon from "remixicon-react/Search2LineIcon";
import DarkButton from "components/button/darkButton";
import Map from "components/map/map";
import ArrowLeftLineIcon from "remixicon-react/ArrowLeftLineIcon";
import { useSettings } from "contexts/settings/settings.context";
import TextInput from "components/inputs/textInput";
import { useFormik } from "formik";
import CompassDiscoverLineIcon from "remixicon-react/CompassDiscoverLineIcon";
import { getAddressFromLocation } from "utils/getAddressFromLocation";
import shopService from "services/shop";
import { useQuery } from "react-query";

interface Props extends DialogProps {
  address?: string;
  latlng: string;
}
interface formValues {
  entrance?: string;
  floor?: string;
  apartment?: string;
  comment?: string;
}

export default function AddressModal({ address, latlng, ...rest }: Props) {
  const { t } = useTranslation();
  const { updateAddress, updateLocation } = useSettings();
  const [location, setLocation] = useState({
    lat: Number(latlng.split(",")[0]),
    lng: Number(latlng.split(",")[1]),
  });
  const inputRef = useRef<any>();

  const { isSuccess } = useQuery(["shopZones", location], () =>
    shopService.checkZone({
      address: { latitude: location.lat, longitude: location.lng },
    })
  );

  function submitAddress() {
    updateAddress(inputRef.current?.value);
    updateLocation(`${location.lat},${location.lng}`);
    if (rest.onClose) rest.onClose({}, "backdropClick");
  }

  const formik = useFormik({
    initialValues: {
      entrance: "",
      floor: "",
      apartment: "",
      comment: "",
    },
    onSubmit: (values: formValues, { setSubmitting }) => {
      console.log("values => ", values);
    },
    validate: (values: formValues) => {
      const errors: formValues = {};
      if (!values.entrance) {
        errors.entrance = t("required");
      }
      if (!values.floor) {
        errors.floor = t("required");
      }
      if (!values.apartment) {
        errors.apartment = t("required");
      }
      return errors;
    },
  });

  function defineAddress() {
    window.navigator.geolocation.getCurrentPosition(
      defineLocation,
      console.log
    );
  }

  async function defineLocation(position: any) {
    const { coords } = position;
    let latlng: string = `${coords.latitude},${coords.longitude}`;
    const addr = await getAddressFromLocation(latlng);
    if (inputRef.current) inputRef.current.value = addr;
    const locationObj = {
      lat: coords.latitude,
      lng: coords.longitude,
    };
    setLocation(locationObj);
  }

  return (
    <ModalContainer {...rest}>
      <div className={cls.wrapper}>
        <div className={cls.header}>
          <h1 className={cls.title}>{t("enter.delivery.address")}</h1>
          <div className={cls.flex}>
            <div className={cls.search}>
              <label htmlFor="search">
                <Search2LineIcon />
              </label>
              <input
                type="text"
                id="search"
                name="search"
                ref={inputRef}
                placeholder={t("search")}
                autoComplete="off"
                defaultValue={address}
              />
            </div>
            <div className={cls.btnWrapper}>
              <DarkButton onClick={defineAddress}>
                <CompassDiscoverLineIcon />
              </DarkButton>
            </div>
          </div>
        </div>
        <div className={cls.body}>
          <Map
            location={location}
            setLocation={setLocation}
            inputRef={inputRef}
          />
        </div>
        <div className={cls.form}>
          <Grid container spacing={2}>
            <Grid item xs={4}>
              <TextInput
                name="entrance"
                label={t("entrance")}
                placeholder={t("type.here")}
                value={formik.values.entrance}
                onChange={formik.handleChange}
              />
            </Grid>
            <Grid item xs={4}>
              <TextInput
                name="floor"
                label={t("floor")}
                placeholder={t("type.here")}
                value={formik.values.floor}
                onChange={formik.handleChange}
              />
            </Grid>
            <Grid item xs={4}>
              <TextInput
                name="apartment"
                label={t("apartment")}
                placeholder={t("type.here")}
                value={formik.values.apartment}
                onChange={formik.handleChange}
              />
            </Grid>
            <Grid item xs={12}>
              <TextInput
                name="comment"
                label={t("comment")}
                placeholder={t("type.here")}
                value={formik.values.comment}
                onChange={formik.handleChange}
              />
            </Grid>
            <Grid item xs={12}>
              <DarkButton
                type="button"
                onClick={submitAddress}
                disabled={!isSuccess}
              >
                {isSuccess ? t("submit") : t("delivery.zone.not.available")}
              </DarkButton>
            </Grid>
          </Grid>
        </div>
        <div className={cls.footer}>
          <button
            className={cls.circleBtn}
            onClick={(event) => {
              if (rest.onClose) rest.onClose(event, "backdropClick");
            }}
          >
            <ArrowLeftLineIcon />
          </button>
        </div>
      </div>
    </ModalContainer>
  );
}
