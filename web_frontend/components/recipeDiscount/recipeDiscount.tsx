import React from "react";
import cls from "./recipeDiscount.module.scss";
import Badge from "components/badge/badge";
import { useTranslation } from "react-i18next";
import { CartType } from "interfaces";
import { useAppSelector } from "hooks/useRedux";
import { selectCurrency } from "redux/slices/currency";

type Props = {
  data?: CartType;
};

export default function RecipeDiscount({ data }: Props) {
  const { t } = useTranslation();
  const currency = useAppSelector(selectCurrency);
  const symbol = currency?.symbol || "";

  if (data?.receipt_discount) {
    return (
      <div className={cls.discount}>
        <Badge type="discount" variant="circle" />
        <div className={cls.text}>
          {t("recipe.discount.definition", {
            price: symbol + data?.receipt_discount,
          })}
        </div>
      </div>
    );
  }

  return <div />;
}
