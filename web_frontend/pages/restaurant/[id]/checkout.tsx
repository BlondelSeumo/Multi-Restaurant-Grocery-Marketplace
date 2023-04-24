//@ts-nocheck
import React from "react";
import SEO from "components/seo";
import CheckoutContainer from "containers/checkout/checkout";
import CheckoutDelivery from "containers/checkoutDelivery/checkoutDelivery";
import CheckoutProducts from "containers/checkoutProducts/checkoutProducts";
import { GetServerSideProps } from "next";
import { dehydrate, QueryClient, useQuery } from "react-query";
import shopService from "services/shop";
import { useRouter } from "next/router";
import cartService from "services/cart";
import { useAppDispatch, useAppSelector } from "hooks/useRedux";
import { updateUserCart } from "redux/slices/userCart";
import { useTranslation } from "react-i18next";
import getLanguage from "utils/getLanguage";
import { selectCurrency } from "redux/slices/currency";

type Props = {};

export default function Checkout({}: Props) {
  const { i18n } = useTranslation();
  const locale = i18n.language;
  const { query } = useRouter();
  const shopId = Number(query.id);
  const dispatch = useAppDispatch();
  const currency = useAppSelector(selectCurrency);

  const { data } = useQuery(["shop", shopId, locale], () =>
    shopService.getById(shopId)
  );

  const { isLoading } = useQuery(
    ["cart", currency?.id],
    () => cartService.get({ currency_id: currency?.id }),
    {
      onSuccess: (data) => dispatch(updateUserCart(data.data)),
      staleTime: 0,
      refetchOnWindowFocus: true,
    }
  );

  return (
    <>
      <SEO />
      <CheckoutContainer data={data?.data}>
        <CheckoutDelivery />
        <CheckoutProducts loading={isLoading} />
      </CheckoutContainer>
    </>
  );
}

export const getServerSideProps: GetServerSideProps = async ({
  query,
  req,
}) => {
  const queryClient = new QueryClient();
  const shopId = Number(query.id);
  const locale = getLanguage(req.cookies?.locale);

  await queryClient.prefetchQuery(["shop", shopId, locale], () =>
    shopService.getById(shopId)
  );

  return {
    props: {
      dehydratedState: JSON.parse(JSON.stringify(dehydrate(queryClient))),
    },
  };
};
