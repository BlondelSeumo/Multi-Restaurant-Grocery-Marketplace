import React from "react";
import { IShop } from "interfaces";
import cls from "./storeCard.module.scss";
import Link from "next/link";
import { useTranslation } from "react-i18next";
import getImage from "utils/getImage";
import BonusCaption from "components/bonusCaption/bonusCaption";
import TaxiFillIcon from "remixicon-react/TaxiFillIcon";
import FallbackImage from "components/fallbackImage/fallbackImage";

type Props = {
  data: IShop;
};

export default function StoreCard({ data }: Props) {
  const { t } = useTranslation();

  return (
    <Link href={`/shop/${data.id}`} className={cls.wrapper}>
      <div className={cls.header}>
        <FallbackImage
          fill
          src={getImage(data.background_img)}
          alt={data.translation?.title}
          sizes="286px"
        />
        <div className={cls.badge}>
          <TaxiFillIcon />
          <span className={cls.text}>
            {t("delivery.range", {
              times: `${data.delivery_time?.from}-${data.delivery_time?.to}`,
            })}
          </span>
        </div>
      </div>
      <div className={cls.body}>
        <h3 className={cls.title}>{data.translation?.title}</h3>
        <p className={cls.text}>
          {data.bonus ? (
            <BonusCaption data={data.bonus} />
          ) : (
            data.translation?.description
          )}
        </p>
      </div>
    </Link>
  );
}
