import React, { useEffect, useRef, useState } from "react";
import useModal from "hooks/useModal";
import usePopover from "hooks/usePopover";
import { useTranslation } from "react-i18next";
import MapPinRangeLineIcon from "remixicon-react/MapPinRangeLineIcon";
import cls from "./addressContainer.module.scss";
import AddressPopover from "components/addressPopover/addressPopover";
import dynamic from "next/dynamic";
import { useSettings } from "contexts/settings/settings.context";
import { getAddressFromLocation } from "utils/getAddressFromLocation";
import { useMediaQuery } from "@mui/material";
import MobileDrawer from "containers/drawer/mobileDrawer";
import { useRouter } from "next/router";

const PopoverContainer = dynamic(() => import("containers/popover/popover"));
const AddressModal = dynamic(
  () => import("components/addressModal/addressModal")
);

type Props = {};

export default function AddressContainer({}: Props) {
  const { t } = useTranslation();
  const isDesktop = useMediaQuery("(min-width:1140px)");
  const addressRef = useRef<any>();
  const [addressModal, handleOpenAddressModal, handleCloseAddressModal] =
    useModal();
  const [
    addressPopover,
    anchorEl,
    handleOpenAddressPopover,
    handleCloseAddressPopover,
  ] = usePopover();
  const { address, location, updateAddress, updateLocation } = useSettings();
  const [userAddress, setUserAddress] = useState<string | undefined>(undefined);
  const [userLocation, setUserLocation] = useState("");
  const { query } = useRouter();

  useEffect(() => {
    if (!address) {
      window.navigator.geolocation.getCurrentPosition(
        defineLocation,
        defineLocation
      );
    }
  }, []);

  async function defineLocation(position: any) {
    const { coords } = position;
    let latlng: string;
    if (coords) {
      latlng = `${coords.latitude},${coords.longitude}`;
    } else {
      latlng = location;
    }
    const addr = await getAddressFromLocation(latlng);
    setUserAddress(addr);
    setUserLocation(latlng);
    updateLocation(latlng);
    try {
      addressRef.current.click();
    } catch (err) {
      console.log("err => ", err);
    }
    if (query.g) {
      updateAddress(addr);
      handleCloseAddressPopover();
    }
  }

  const saveAndCloseAddressPopover = () => {
    updateAddress(userAddress);
    handleCloseAddressPopover();
  };

  const handleClickAddressRef = (event: any) => {
    event.stopPropagation();
    handleOpenAddressPopover(event);
  };

  return (
    <>
      <div className={cls.address} onClick={handleOpenAddressModal}>
        <div className={cls.icon}>
          <MapPinRangeLineIcon />
        </div>
        <div
          ref={addressRef}
          onClick={handleClickAddressRef}
          className={cls.addressTitle}
        >
          <label>{t("delivery.address")}</label>
          <p>{address}</p>
        </div>
      </div>
      {isDesktop ? (
        <PopoverContainer
          open={addressPopover}
          anchorEl={anchorEl}
          onClose={saveAndCloseAddressPopover}
          anchorOrigin={{ vertical: "bottom", horizontal: "center" }}
          transformOrigin={{ vertical: "top", horizontal: "center" }}
        >
          <AddressPopover
            handleOpenAddressModal={handleOpenAddressModal}
            handleCloseAddressPopover={saveAndCloseAddressPopover}
            defaultAddress={userAddress}
          />
        </PopoverContainer>
      ) : (
        <MobileDrawer
          open={addressPopover}
          onClose={saveAndCloseAddressPopover}
        >
          <AddressPopover
            handleOpenAddressModal={handleOpenAddressModal}
            handleCloseAddressPopover={saveAndCloseAddressPopover}
            defaultAddress={userAddress}
          />
        </MobileDrawer>
      )}
      {addressModal && (
        <AddressModal
          open={addressModal}
          onClose={handleCloseAddressModal}
          latlng={userLocation || location}
          address={userAddress || address}
          fullScreen={!isDesktop}
        />
      )}
    </>
  );
}
