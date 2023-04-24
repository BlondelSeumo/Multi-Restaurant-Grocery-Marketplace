import {
  BRAND_LOGO,
  BRAND_LOGO_DARK,
  BRAND_LOGO_ROUNDED,
} from "constants/config";

/* eslint-disable @next/next/no-img-element */
export const BrandLogo = () => (
  <img src={BRAND_LOGO} width="129" height="28" alt="Brand logo" />
);

export const BrandLogoDark = () => (
  <img src={BRAND_LOGO_DARK} width="129" height="28" alt="Brand logo dark" />
);

export const DoubleCheckIcon = () => (
  <svg
    width="30"
    height="30"
    viewBox="0 0 30 30"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
  >
    <circle cx="15" cy="15" r="15" fill="#83EA00" />
    <g clipPath="url(#clip0_109_101)">
      <path
        d="M14.668 16.4666L15.8447 17.6433L22.8997 10.5883L24.078 11.7666L15.8447 19.9999L10.5413 14.6966L11.7197 13.5183L13.4905 15.2891L14.668 16.4658V16.4666ZM14.6697 14.1099L18.7963 9.98242L19.9713 11.1574L15.8447 15.2849L14.6697 14.1099ZM12.3138 18.8224L11.1363 19.9999L5.83301 14.6966L7.01134 13.5183L8.18884 14.6958L8.18801 14.6966L12.3138 18.8224Z"
        fill="black"
      />
    </g>
    <defs>
      <clipPath id="clip0_109_101">
        <rect width="20" height="20" fill="white" transform="translate(5 5)" />
      </clipPath>
    </defs>
  </svg>
);

export const BrandLogoRounded = () => (
  <img
    src={BRAND_LOGO_ROUNDED}
    width="60"
    height="60"
    alt="Brand logo rounded"
  />
);
