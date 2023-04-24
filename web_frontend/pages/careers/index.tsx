import React from "react";
import SEO from "components/seo";
import OrdersContainer from "containers/orders/orders";
import { useTranslation } from "react-i18next";
import CareerList from "containers/careerList/careerList";
import careers from "data/careers";

type Props = {};

export default function Careers({}: Props) {
  const { t } = useTranslation();

  return (
    <>
      <SEO title={t("careers")} />
      <div className="bg-white">
        <OrdersContainer title={t("careers")}>
          <CareerList data={careers} />
        </OrdersContainer>
      </div>
    </>
  );
}
