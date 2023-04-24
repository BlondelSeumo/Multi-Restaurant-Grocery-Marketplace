import React from "react";
import { IShop } from "interfaces";
import cls from "./shopCard.module.scss";
import Link from "next/link";
import ShopLogo from "components/shopLogo/shopLogo";
import RunFillIcon from "remixicon-react/RunFillIcon";
import StarSmileFillIcon from "remixicon-react/StarSmileFillIcon";
import { useTranslation } from "react-i18next";
import getImage from "utils/getImage";
import BonusCaption from "components/bonusCaption/bonusCaption";
import ShopBadges from "containers/shopBadges/shopBadges";
import FallbackImage from "components/fallbackImage/fallbackImage";

type Props = {
  data: IShop;
};

export default function ShopCard({ data }: Props) {
  const { t } = useTranslation();

  return (
    <Link
      href={`/${data.type}/${data.id}`}
      className={`${cls.wrapper} ${data.open ? "" : cls.closed}`}
    >
      <div className={cls.header}>
        {!data.open && <div className={cls.closedText}>{t("closed")}</div>}
        <FallbackImage
          fill
          src={getImage(data.background_img)}
          alt={data.translation?.title}
          sizes="400px"
        />
      </div>
      <div className={cls.body}>
        <div className={cls.shopLogo}>
          <ShopLogo data={data} />
        </div>
        <ShopBadges data={data} />
        <h3 className={cls.title}>{data.translation?.title}</h3>
        <p className={cls.text}>
          {data.bonus ? (
            <BonusCaption data={data.bonus} />
          ) : (
            data.translation?.description
          )}
        </p>
      </div>
      <div className={cls.footer}>
        <div className={cls.flex}>
          <span className={cls.greenDot} />
          <RunFillIcon />
          <span className={cls.text}>
            {t("delivery.range", {
              times: `${data.delivery_time?.from}-${data.delivery_time?.to}`,
            })}
          </span>
        </div>
        <span className={cls.dot} />
        <div className={cls.flex}>
          <StarSmileFillIcon className={cls.ratingIcon} />
          <span className={cls.text}>{data.rating_avg?.toFixed(1) || 0}</span>
        </div>
      </div>
    </Link>
  );
}
