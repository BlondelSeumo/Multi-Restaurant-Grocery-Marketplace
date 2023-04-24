import React from "react";
import SEO from "components/seo";
import { useTranslation } from "react-i18next";
import BeDelivery from "containers/beDelivery/beDelivery";

type Props = {};

export default function Deliver({}: Props) {
  const { t } = useTranslation();

  return (
    <>
      <SEO title={t("become.delivery")} />
      <BeDelivery />
    </>
  );
}
