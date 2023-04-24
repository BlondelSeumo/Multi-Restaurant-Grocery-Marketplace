import React, { useMemo, useState } from "react";
import PrimaryButton from "components/button/primaryButton";
import BankCardLineIcon from "remixicon-react/BankCardLineIcon";
import Coupon3LineIcon from "remixicon-react/Coupon3LineIcon";
import cls from "./checkoutPayment.module.scss";
import { useTranslation } from "react-i18next";
import { useMediaQuery } from "@mui/material";
import dynamic from "next/dynamic";
import useModal from "hooks/useModal";
import { useAppSelector } from "hooks/useRedux";
import { selectUserCart } from "redux/slices/userCart";
import { useQuery } from "react-query";
import orderService from "services/order";
import Price from "components/price/price";
import Loading from "components/loader/loading";
import { OrderFormValues, Payment } from "interfaces";
import { FormikProps } from "formik";
import { DoubleCheckIcon } from "components/icons";
import Coupon from "components/coupon/coupon";
import PaymentMethod from "components/paymentMethod/paymentMethod";
import { useAuth } from "contexts/auth/auth.context";
import { warning } from "components/alert/toast";
import { selectCurrency } from "redux/slices/currency";

const DrawerContainer = dynamic(() => import("containers/drawer/drawer"));
const MobileDrawer = dynamic(() => import("containers/drawer/mobileDrawer"));

type Props = {
  formik: FormikProps<OrderFormValues>;
  loading?: boolean;
  payments: Payment[];
};

type OrderType = {
  bonus_shop?: any;
  coupon_price?: number;
  delivery_fee?: number;
  price?: number;
  total_discount?: number;
  total_price?: number;
  total_shop_tax?: number;
  total_tax?: number;
};

export default function CheckoutPayment({
  formik,
  loading = false,
  payments = [],
}: Props) {
  const { t } = useTranslation();
  const isDesktop = useMediaQuery("(min-width:1140px)");
  const { user } = useAuth();
  const [
    paymentMethodDrawer,
    handleOpenPaymentMethod,
    handleClosePaymentMethod,
  ] = useModal();
  const [promoDrawer, handleOpenPromo, handleClosePromo] = useModal();
  const cart = useAppSelector(selectUserCart);
  const currency = useAppSelector(selectCurrency);
  const [order, setOrder] = useState<OrderType>({});
  const { coupon, location, delivery_type, payment_type } = formik.values;

  const payload = useMemo(
    () => ({
      address: location,
      type: delivery_type,
      coupon,
      currency_id: currency?.id,
    }),
    [location, delivery_type, coupon, currency]
  );

  const { isLoading } = useQuery(
    ["calculate", payload, cart],
    () => orderService.calculate(cart.id, payload),
    {
      onSuccess: (data) => setOrder(data.data),
      staleTime: 0,
    }
  );

  function handleOrderCreate() {
    if (payment_type?.tag === "wallet") {
      if (Number(order.total_price) > Number(user.wallet?.price)) {
        warning(t("insufficient.wallet.balance"));
        return;
      }
    }
    formik.handleSubmit();
  }

  return (
    <div className={cls.card}>
      <div className={cls.cardHeader}>
        <h3 className={cls.title}>{t("payment")}</h3>
        <div className={cls.flex}>
          <div className={cls.flexItem}>
            <BankCardLineIcon />
            <span className={cls.text}>
              {payment_type ? (
                <span style={{ textTransform: "capitalize" }}>
                  {t(payment_type?.tag)}
                </span>
              ) : (
                t("payment.method")
              )}
            </span>
          </div>
          <button className={cls.action} onClick={handleOpenPaymentMethod}>
            {t("edit")}
          </button>
        </div>
        <div className={cls.flex}>
          <div className={cls.flexItem}>
            <Coupon3LineIcon />
            <span className={cls.text}>
              {coupon ? (
                <span className={cls.coupon}>
                  {coupon} <DoubleCheckIcon />
                </span>
              ) : (
                t("promo.code")
              )}
            </span>
          </div>
          <button className={cls.action} onClick={handleOpenPromo}>
            {t("enter")}
          </button>
        </div>
      </div>
      <div className={cls.cardBody}>
        <div className={cls.block}>
          <div className={cls.row}>
            <div className={cls.item}>{t("subtotal")}</div>
            <div className={cls.item}>
              <Price number={order.price} />
            </div>
          </div>
          <div className={cls.row}>
            <div className={cls.item}>{t("delivery.price")}</div>
            <div className={cls.item}>
              <Price number={order.delivery_fee} />
            </div>
          </div>
          <div className={cls.row}>
            <div className={cls.item}>{t("shop.tax")}</div>
            <div className={cls.item}>
              <Price number={order.total_shop_tax} />
            </div>
          </div>
          <div className={cls.row}>
            <div className={cls.item}>{t("discount")}</div>
            <div className={cls.item}>
              <Price number={order.total_discount} minus />
            </div>
          </div>
          {coupon ? (
            <div className={cls.row}>
              <div className={cls.item}>{t("promo.code")}</div>
              <div className={cls.item}>
                <Price number={order.coupon_price} minus />
              </div>
            </div>
          ) : (
            ""
          )}
        </div>
        <div className={cls.cardFooter}>
          <div className={cls.btnWrapper}>
            <PrimaryButton
              type="submit"
              onClick={handleOrderCreate}
              loading={loading}
            >
              {t("continue.payment")}
            </PrimaryButton>
          </div>
          <div className={cls.priceBlock}>
            <p className={cls.text}>{t("total")}</p>
            <div className={cls.price}>
              <Price number={order.total_price} />
            </div>
          </div>
        </div>
      </div>

      {isLoading && <Loading />}

      {isDesktop ? (
        <DrawerContainer
          open={paymentMethodDrawer}
          onClose={handleClosePaymentMethod}
          title={t("payment.method")}
        >
          <PaymentMethod
            formik={formik}
            list={payments}
            handleClose={handleClosePaymentMethod}
          />
        </DrawerContainer>
      ) : (
        <MobileDrawer
          open={paymentMethodDrawer}
          onClose={handleClosePaymentMethod}
          title={t("payment.method")}
        >
          <PaymentMethod
            formik={formik}
            list={payments}
            handleClose={handleClosePaymentMethod}
          />
        </MobileDrawer>
      )}
      {isDesktop ? (
        <DrawerContainer
          open={promoDrawer}
          onClose={handleClosePromo}
          title={t("add.promocode")}
        >
          <Coupon formik={formik} handleClose={handleClosePromo} />
        </DrawerContainer>
      ) : (
        <MobileDrawer
          open={promoDrawer}
          onClose={handleClosePromo}
          title={t("add.promocode")}
        >
          <Coupon formik={formik} handleClose={handleClosePromo} />
        </MobileDrawer>
      )}
    </div>
  );
}
