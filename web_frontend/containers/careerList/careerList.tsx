import React from "react";
import cls from "./careerList.module.scss";
import Link from "next/link";
import ArrowRightSLineIcon from "remixicon-react/ArrowRightSLineIcon";
import { useTranslation } from "react-i18next";
import { Career } from "interfaces";

type Props = {
  data: Career[];
};

export default function CareerList({ data }: Props) {
  const { t } = useTranslation();

  return (
    <div className={cls.root}>
      {data.map((item) => (
        <Link
          key={item.id}
          href={`/careers/${item.id}`}
          className={cls.wrapper}
        >
          <div className={cls.naming}>
            <h3 className={cls.title}>{item.title}</h3>
            <p className={cls.text}>{t("role")}</p>
          </div>
          <div className={cls.naming}>
            <h3 className={cls.title}>{item.category}</h3>
            <p className={cls.text}>{t("category")}</p>
          </div>
          <div className={cls.naming}>
            <h3 className={cls.title}>{item.location}</h3>
            <p className={cls.text}>{t("location")}</p>
          </div>
          <div className={cls.actions}>
            <div className={cls.arrowBtn}>
              <ArrowRightSLineIcon />
            </div>
          </div>
        </Link>
      ))}
    </div>
  );
}
