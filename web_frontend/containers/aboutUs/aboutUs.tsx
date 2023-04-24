/* eslint-disable @next/next/no-img-element */
import React from "react";
import cls from "./aboutUs.module.scss";
import Image from "next/image";
import { useTranslation } from "react-i18next";
import { META_TITLE } from "constants/config";

type Props = {};

export default function AboutUs({}: Props) {
  const { t } = useTranslation();

  return (
    <div className={`container ${cls.container}`}>
      <div className={cls.header}>
        <h1 className={cls.title}>
          {t("about")} {META_TITLE}
        </h1>
        <p className={cls.text}></p>
      </div>
      <div className={cls.hero}>
        <Image
          fill
          src={"/images/welcome.jpg"}
          alt={"Foodyman"}
          sizes="(max-width: 768px) 600px, 1072px"
        />
      </div>
      <main className={cls.content}>
        <p className={cls.text}>
          Tens of millions of people look for design inspiration and feedback on
          Dribbble. We help players like you share small screenshots (shots) to
          show off your current projects, boost your portfolio, and love what
          you do—no matter what kind of creative professional you are. Founded
          in 2009, we are a bootstrapped and profitable company helping design
          talent share, grow, and get hired by over 40,000 of today’s most
          innovative brands around the world.
        </p>
      </main>
    </div>
  );
}
