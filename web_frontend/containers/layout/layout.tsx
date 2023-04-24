import React, { useContext, useEffect } from "react";
import { useRouter } from "next/router";
import Header from "./header/header";
import MobileHeader from "./mobileHeader/mobileHeader";
import ProfileHeader from "./profileHeader/profileHeader";
import { useMediaQuery } from "@mui/material";
import { useMutation, useQuery } from "react-query";
import cartService from "services/cart";
import { useAppDispatch, useAppSelector } from "hooks/useRedux";
import { updateUserCart } from "redux/slices/userCart";
import { clearCart, selectCart } from "redux/slices/cart";
import { useAuth } from "contexts/auth/auth.context";
import { selectCurrency, setCurrency } from "redux/slices/currency";
import currencyService from "services/currency";
import { Currency, Langauge } from "interfaces";
import languageService from "services/language";
import dynamic from "next/dynamic";
import informationService from "services/information";
import { useSettings } from "contexts/settings/settings.context";
import ErrorBoundary from "containers/errorBoundary/errorBoundary";
import { ThemeContext } from "contexts/theme/theme.context";
import Footer from "./footer/footer";
import { useTranslation } from "react-i18next";
import translationService from "services/translations";

const PushNotification = dynamic(
  () => import("containers/pushNotification/pushNotification")
);

type LayoutProps = {
  children: any;
};

const profileRoutes = [
  "checkout",
  "profile",
  "settings",
  "help",
  "orders/",
  "be-seller",
];

export default function Layout({ children }: LayoutProps) {
  const { pathname } = useRouter();
  const { i18n } = useTranslation();
  const locale = i18n.language;
  const isProfileRoute = profileRoutes.find((item) => pathname.includes(item));
  const isDesktop = useMediaQuery("(min-width:1140px)");
  const dispatch = useAppDispatch();
  const { isAuthenticated } = useAuth();
  const cart = useAppSelector(selectCart);
  const currency = useAppSelector(selectCurrency);
  const { updateSettings } = useSettings();
  const { isDarkMode, setDirection } = useContext(ThemeContext);

  useQuery(
    ["translation", locale],
    () => translationService.getAll({ lang: locale }),
    {
      onSuccess: (data) => {
        i18n.addResourceBundle(locale, "translation", data.data);
      },
    }
  );

  useQuery("currencies", () => currencyService.getAll(), {
    onSuccess: (data) => {
      const activeCurrency = data.data.find((item: Currency) => item.default);
      if (!currency) {
        dispatch(setCurrency(activeCurrency));
      }
    },
  });

  useQuery("languages", () => languageService.getAllActive(), {
    onSuccess: (data) => {
      const isRTL = !!data?.data.find((item: Langauge) => item.locale == locale)
        ?.backward;
      setDirection(isRTL ? "rtl" : "ltr");
    },
  });

  useQuery("settings", () => informationService.getSettings(), {
    onSuccess: (data) => {
      const obj = createSettings(data.data);
      updateSettings({
        payment_type: obj.payment_type,
        instagram_url: obj.instagram,
        facebook_url: obj.facebook,
        twitter_url: obj.twitter,
        referral_active: obj.referral_active,
        otp_expire_time: obj.otp_expire_time,
        customer_app_android: obj.customer_app_android,
        customer_app_ios: obj.customer_app_ios,
        delivery_app_android: obj.delivery_app_android,
        delivery_app_ios: obj.delivery_app_ios,
        vendor_app_android: obj.vendor_app_android,
        vendor_app_ios: obj.vendor_app_ios,
        group_order: obj.group_order,
        footer_text: obj.footer_text,
      });
    },
  });

  const { mutate: insertProducts } = useMutation({
    mutationFn: (data: any) => cartService.insert(data),
    onSuccess: (data) => {
      dispatch(clearCart());
      dispatch(updateUserCart(data.data));
    },
  });

  function createSettings(list: any[]) {
    const result = list.map((item) => ({
      [item.key]: item.value,
    }));
    return Object.assign({}, ...result);
  }

  useEffect(() => {
    if (isAuthenticated && !!cart.length) {
      let addons: any[] = [];
      let products: any[] = [];
      cart.forEach((item) => {
        products.push({
          stock_id: item.stock.id,
          quantity: item.quantity,
        });
        item.addons.forEach((el) => {
          addons.push({
            stock_id: el.stock.id,
            quantity: el.quantity,
            parent_id: item.stock.id,
          });
        });
      });
      const payload = {
        shop_id: cart.find((item) => !!item.shop_id)?.shop_id,
        currency_id: currency?.id,
        rate: currency?.rate,
        products: [...products, ...addons],
      };
      insertProducts(payload);
    }
  }, [cart, currency, isAuthenticated, insertProducts]);

  return (
    <ErrorBoundary isDarkMode={isDarkMode}>
      {isProfileRoute ? (
        <ProfileHeader />
      ) : isDesktop ? (
        <Header />
      ) : (
        <MobileHeader />
      )}
      {children}
      {isAuthenticated && <PushNotification />}
      <Footer />
    </ErrorBoundary>
  );
}
