import axios from 'axios';

export default async function getAddressFromLocation(
  location,
  key = 'AIzaSyDI0dNtJchqNdUbml5PyVGrUqJDSuJLMdg'
) {
  let params = {
    latlng: `${location?.lat},${location?.lng}`,
    key,
  };
  return axios
    .get(`https://maps.googleapis.com/maps/api/geocode/json`, { params })
    .then(({ data }) => data.results[0]?.formatted_address)
    .catch((error) => {
      return 'not found';
    });
}
