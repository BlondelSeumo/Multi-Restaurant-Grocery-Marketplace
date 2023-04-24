/* eslint-disable @next/next/no-img-element */
import React, { MutableRefObject, useRef } from "react";
import GoogleMapReact, { Coords } from "google-map-react";
import cls from "./map.module.scss";
import { MAP_API_KEY } from "constants/constants";
import { getAddressFromLocation } from "utils/getAddressFromLocation";
import { IShop } from "interfaces";
import ShopLogoBackground from "components/shopLogoBackground/shopLogoBackground";

const Marker = (props: any) => (
  <img src="/images/marker.png" width={32} alt="Location" />
);
const ShopMarker = (props: any) => (
  <ShopLogoBackground data={props.shop} size="small" />
);

const options = {
  fields: ["address_components", "geometry"],
  types: ["address"],
};

type Props = {
  location: Coords;
  setLocation?: (data: any) => void;
  readOnly?: boolean;
  shop?: IShop;
  inputRef?: MutableRefObject<HTMLInputElement | null>;
};

export default function Map({
  location,
  setLocation = () => {},
  readOnly = false,
  shop,
  inputRef,
}: Props) {
  const autoCompleteRef = useRef<any>();

  async function onChangeMap(map: any) {
    if (readOnly) {
      return;
    }
    const location = {
      lat: map.center.lat(),
      lng: map.center.lng(),
    };
    setLocation(location);
    const address = await getAddressFromLocation(
      `${location.lat},${location.lng}`
    );
    if (inputRef?.current?.value) inputRef.current.value = address;
  }

  const handleApiLoaded = (map: any, maps: any) => {
    autoComplete(map, maps);
    if (shop) {
      const shopLocation = {
        lat: Number(shop.location?.latitude) || 0,
        lng: Number(shop.location?.longitude) || 0,
      };
      const markers = [location, shopLocation];
      let bounds = new maps.LatLngBounds();
      for (var i = 0; i < markers.length; i++) {
        bounds.extend(markers[i]);
      }
      map.fitBounds(bounds);
    }
  };

  function autoComplete(map: any, maps: any) {
    if (inputRef) {
      autoCompleteRef.current = new maps.places.Autocomplete(
        inputRef.current,
        options
      );
      autoCompleteRef.current.addListener("place_changed", async function () {
        const place = await autoCompleteRef.current.getPlace();
        console.log(place);
        const coords: Coords = {
          lat: place.geometry.location.lat(),
          lng: place.geometry.location.lng(),
        };
        setLocation(coords);
      });
    }
  }

  return (
    <div className={cls.root}>
      {!readOnly && (
        <div className={cls.marker}>
          <img src="/images/marker.png" width={32} alt="Location" />
        </div>
      )}
      <GoogleMapReact
        bootstrapURLKeys={{
          key: MAP_API_KEY || "",
          libraries: ["places"],
        }}
        defaultZoom={15}
        center={location}
        onDragEnd={onChangeMap}
        yesIWantToUseGoogleMapApiInternals
        onGoogleApiLoaded={({ map, maps }) => handleApiLoaded(map, maps)}
      >
        {readOnly && <Marker lat={location.lat} lng={location.lng} />}
        {!!shop && (
          <ShopMarker
            lat={shop.location?.latitude || 0}
            lng={shop.location?.longitude || 0}
            shop={shop}
          />
        )}
      </GoogleMapReact>
    </div>
  );
}
