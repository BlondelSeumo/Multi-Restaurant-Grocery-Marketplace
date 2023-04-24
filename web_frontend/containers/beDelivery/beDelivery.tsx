/* eslint-disable @next/next/no-img-element */
import React from "react";
import cls from "./beDelivery.module.scss";
import Image from "next/image";
import { useTranslation } from "react-i18next";
import { useSettings } from "contexts/settings/settings.context";

type Props = {};

export default function BeDelivery({}: Props) {
  const { t } = useTranslation();
  const { settings } = useSettings();

  return (
    <div className={`container ${cls.container}`}>
      <div className={cls.wrapper}>
        <div className={cls.content}>
          <h1 className={cls.title}>Looking for delivery driver jobs?</h1>
          <p className={cls.text}>{t("become.delivery.text")}</p>
          <p className={`${cls.text} ${cls.bold}`}>
            Get invitation link from restaurant in order to be delivery.
          </p>
          <div className={cls.flex}>
            <a
              href={settings?.delivery_app_ios}
              className={cls.item}
              target="_blank"
              rel="noopener noreferrer"
            >
              <img src="/images/app-store.webp" alt="App store" />
            </a>
            <a
              href={settings?.delivery_app_android}
              className={cls.item}
              target="_blank"
              rel="noopener noreferrer"
            >
              <img src="/images/google-play.webp" alt="Google play" />
            </a>
          </div>
        </div>
        <div className={cls.imgWrapper}>
          <Image
            fill
            src={"/images/welcome.jpg"}
            alt={"Foodyman"}
            sizes="(max-width: 768px) 600px, 1072px"
          />
        </div>
      </div>
    </div>
  );
}
