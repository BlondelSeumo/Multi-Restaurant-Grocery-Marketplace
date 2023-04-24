import React from "react";
import Link from "next/link";
import cls from "./productCard.module.scss";
import { Product } from "interfaces";
import getImage from "utils/getImage";
import Price from "components/price/price";
import Badge from "components/badge/badge";
import useShopType from "hooks/useShopType";
import FallbackImage from "components/fallbackImage/fallbackImage";

type Props = {
  data: Product;
  handleOpen: (event: any, data: Product) => void;
};

export default function ProductCard({ data, handleOpen }: Props) {
  const type = useShopType();

  return (
    <Link
      href={`/${type}/${data.shop_id}?product=${data.uuid}`}
      shallow={true}
      replace={true}
      className={`${cls.wrapper} ${data.id === 0 ? cls.active : ""}`}
    >
      <div className={cls.header}>
        <FallbackImage
          fill
          src={getImage(data.img)}
          alt={data.translation?.title}
          sizes="320px"
          quality={90}
        />
      </div>
      <div className={cls.body}>
        <h3 className={cls.title}>{data.translation?.title}</h3>
        <p className={cls.text}>{data.translation?.description}</p>
        <span className={cls.price}>
          <Price number={data.stock?.total_price} />
        </span>{" "}
        {!!data.stock?.discount && (
          <span className={cls.oldPrice}>
            <Price number={data.stock?.price} old />
          </span>
        )}
        <span className={cls.bonus}>
          {data.stock?.bonus && <Badge type="bonus" variant="circle" />}
        </span>
      </div>
    </Link>
  );
}
