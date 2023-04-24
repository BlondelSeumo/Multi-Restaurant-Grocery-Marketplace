import React from "react";
import cls from "./storeContainer.module.scss";
import dynamic from "next/dynamic";
import { Category, IShop } from "interfaces";
import { Member, ShopProvider } from "contexts/shop/shop.provider";
import CartContainer from "containers/cart/cartContainer";
import VerticalNavbar from "containers/verticalNavbar/verticalNavbar";
import { useMediaQuery } from "@mui/material";

const MobileCart = dynamic(() => import("containers/mobileCart/mobileCart"));

type Props = {
  data?: IShop;
  children: any;
  memberState: Member;
  categories: Category[];
  categoryLoading?: boolean;
};

export default function StoreContainer({
  data,
  children,
  memberState,
  categories,
  categoryLoading,
}: Props) {
  const isDesktop = useMediaQuery("(min-width:1140px)");

  return (
    <ShopProvider memberState={memberState} data={data}>
      <div className={`${cls.container} store`}>
        {isDesktop && (
          <div className={cls.navbar}>
            <VerticalNavbar categories={categories} loading={categoryLoading} />
          </div>
        )}
        <main className={cls.main}>
          {React.Children.map(children, (child) => {
            return React.cloneElement(child, { data, categories });
          })}
        </main>
        <div className={cls.cart}>
          {!!data && <CartContainer shop={data} />}
        </div>
        {!!data && <MobileCart shop={data} />}
      </div>
    </ShopProvider>
  );
}
