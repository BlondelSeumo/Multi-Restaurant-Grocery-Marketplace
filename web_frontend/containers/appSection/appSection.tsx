/* eslint-disable @next/next/no-img-element */
import React from "react";
import cls from "./appSection.module.scss";
import Image from "next/image";
import { useTranslation } from "react-i18next";
import { useSettings } from "contexts/settings/settings.context";

type Props = {};

export default function AppSection({}: Props) {
  const { t } = useTranslation();
  const { settings } = useSettings();

  return (
    <>
      <div className={`container ${cls.container} ${cls.bgGrey}`}>
        <div className={cls.wrapper}>
          <div className={cls.imgWrapper}>
            <Image
              fill
              src={"/images/customer_app.png"}
              alt={"Foodyman"}
              sizes="(max-width: 768px) 600px, 1072px"
            />
          </div>
          <div className={cls.content}>
            <h1 className={cls.title}>Restaurant and grocery customer app</h1>
            <p className={cls.text}>{t("become.delivery.text")}</p>
            <div className={cls.flex}>
              <a
                href={settings?.customer_app_ios}
                className={cls.item}
                target="_blank"
                rel="noopener noreferrer"
              >
                <img src="/images/app-store.webp" alt="App store" />
              </a>
              <a
                href={settings?.customer_app_android}
                className={cls.item}
                target="_blank"
                rel="noopener noreferrer"
              >
                <img src="/images/google-play.webp" alt="Google play" />
              </a>
            </div>
          </div>
        </div>
      </div>
      <div className={`container ${cls.container}`}>
        <div className={`${cls.wrapper} ${cls.reverse}`}>
          <div className={cls.imgWrapper}>
            <Image
              fill
              src={"/images/vendor_app.png"}
              alt={"Foodyman"}
              sizes="(max-width: 768px) 600px, 1072px"
            />
          </div>
          <div className={cls.content}>
            <h1 className={cls.title}>Restaurant and grocery vendor app</h1>
            <p className={cls.text}>{t("become.delivery.text")}</p>
            <div className={cls.flex}>
              <a
                href={settings?.vendor_app_ios}
                className={cls.item}
                target="_blank"
                rel="noopener noreferrer"
              >
                <img src="/images/app-store.webp" alt="App store" />
              </a>
              <a
                href={settings?.vendor_app_android}
                className={cls.item}
                target="_blank"
                rel="noopener noreferrer"
              >
                <img src="/images/google-play.webp" alt="Google play" />
              </a>
            </div>
          </div>
        </div>
      </div>
      <div className={`container ${cls.container} ${cls.bgGrey}`}>
        <div className={cls.wrapper}>
          <div className={cls.imgWrapper}>
            <Image
              fill
              src={"/images/delivery_app.png"}
              alt={"Foodyman"}
              sizes="(max-width: 768px) 600px, 1072px"
            />
          </div>
          <div className={cls.content}>
            <h1 className={cls.title}>
              Restaurant and grocery deliveryman app
            </h1>
            <p className={cls.text}>{t("become.delivery.text")}</p>
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
        </div>
      </div>
    </>
  );
}
