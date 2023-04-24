import React from "react";
import Link from "next/link";
import cls from "./ordersRefundButton.module.scss";
import { useTranslation } from "react-i18next";
import Refund2LineIcon from "remixicon-react/Refund2LineIcon";
import ArrowRightSLineIcon from "remixicon-react/ArrowRightSLineIcon";

type Props = {};

export default function OrdersRefundButton({}: Props) {
  const { t } = useTranslation();

  return (
    <div className={cls.root}>
      <Link href="/order-refunds" className={cls.textBtn}>
        <Refund2LineIcon />
        <span className={cls.text}>{t("refunds")}</span>
        <ArrowRightSLineIcon />
      </Link>
    </div>
  );
}
