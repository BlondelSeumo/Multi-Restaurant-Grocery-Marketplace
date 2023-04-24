//@ts-nocheck
import React from "react";
import SEO from "components/seo";
import BeSellerContainer from "containers/beSeller/beSellerContainer";
import ShopForm from "components/shopForm/shopForm";
import ShopGeneralForm from "components/shopForm/shopGeneralForm";
import { useTranslation } from "react-i18next";
import ShopDeliveryForm from "components/shopForm/shopDeliveryForm";
import ShopAddressForm from "components/shopForm/shopAddressForm";
import { useQuery } from "react-query";
import categoryService from "services/category";
import shopService from "services/shop";
import { Category } from "interfaces";

type Props = {};

export default function BeSeller({}: Props) {
  const { t, i18n } = useTranslation();
  const locale = i18n.language;

  const { data: shopCategories } = useQuery(["shopCategories", locale], () =>
    categoryService.getAllShopCategories({ perPage: 100 })
  );
  const { data: tags } = useQuery("tags", () => shopService.getAllTags());

  const formatCategories = (list: Category[] = []) => {
    if (!list.length) {
      return [];
    }
    return list.map((item) => ({
      label: item.translation?.title,
      value: item.id,
    }));
  };

  return (
    <>
      <SEO />
      <BeSellerContainer>
        <ShopForm title={t("general")} xs={12} md={8}>
          <ShopGeneralForm
            shopCategories={formatCategories(shopCategories?.data)}
            tagList={formatCategories(tags?.data)}
          />
        </ShopForm>
        <ShopForm title={t("delivery.info")} xs={12} md={4}>
          <ShopDeliveryForm />
        </ShopForm>
        <ShopForm title={t("address")} xs={12}>
          <ShopAddressForm />
        </ShopForm>
      </BeSellerContainer>
    </>
  );
}
