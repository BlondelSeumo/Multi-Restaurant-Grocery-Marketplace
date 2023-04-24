import React, { useEffect, useState } from 'react';
import { GoogleApiWrapper, Map, Marker } from 'google-maps-react';
import { MAP_API_KEY } from '../configs/app-global';
import pinIcon from '../assets/images/pin.png';
import getAddressFromLocation from '../helpers/getAddressFromLocation';
import { BiCurrentLocation } from 'react-icons/bi';

function GoogleMap(props) {
  const [loc, setLoc] = useState();

  const onClickMap = async (t, map, coord) => {
    const { latLng } = coord;
    const location = {
      lat: latLng.lat(),
      lng: latLng.lng(),
    };
    props.setLocation(location);
    const address = await getAddressFromLocation(location, MAP_API_KEY);
    props.setAddress(address);
  };

  const handleSubmit = async (event) => {
    const location = {
      lat: event?.lat,
      lng: event?.lng,
    };
    props.setLocation(location);
    const address = await getAddressFromLocation(location, MAP_API_KEY);
    props.setAddress(address);
  };

  const currentLocation = async () => {
    await navigator.geolocation.getCurrentPosition(function (position) {
      const location = {
        lat: position.coords.latitude,
        lng: position.coords.longitude,
      };
      setLoc(location);
    });
  };

  useEffect(() => {
    currentLocation();
  }, []);

  const markers = [
    {
      lat: Number(props.location.lat) || 0,
      lng: Number(props.location.lng) || 0,
    },
  ];

  var bounds = new props.google.maps.LatLngBounds();
  for (var i = 0; i < markers.length; i++) {
    bounds.extend(markers[i]);
  }

  return (
    <div className='map-container' style={{ height: 400, width: '100%' }}>
      <button
        className='map-button'
        type='button'
        onClick={() => {
          currentLocation();
          handleSubmit(loc);
        }}
      >
        <BiCurrentLocation />
      </button>
      <Map
        cursor={'pointer'}
        onClick={onClickMap}
        google={props.google}
        defaultZoom={12}
        zoom={10}
        className='clickable'
        initialCenter={props.location}
        center={props.location}
        // bounds={bounds}
      >
        <Marker
          position={props.location}
          icon={{
            url: pinIcon,
            scaledSize: new props.google.maps.Size(32, 32),
          }}
          className='marker'
        />
      </Map>
    </div>
  );
}

export default GoogleApiWrapper({
  apiKey: MAP_API_KEY,
})(GoogleMap);
