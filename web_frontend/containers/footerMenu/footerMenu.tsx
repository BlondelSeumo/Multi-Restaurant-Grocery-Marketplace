import React from "react";
import Link from "next/link";
import cls from "./footerMenu.module.scss";
import RestaurantFillIcon from "remixicon-react/RestaurantFillIcon";
import HistoryFillIcon from "remixicon-react/HistoryFillIcon";
import HeartLineIcon from "remixicon-react/HeartLineIcon";
import { useTranslation } from "react-i18next";
import { useRouter } from "next/router";
import { useScrollDirection } from "hooks/useScrollDirection";
import { useAuth } from "contexts/auth/auth.context";
import ProtectedCartButton from "components/cartButton/protectedCartButton";
import CartButton from "components/cartButton/cartButton";

type Props = {};

export default function FooterMenu({}: Props) {
  const { t } = useTranslation();
  const { pathname } = useRouter();
  const scrollDirection = useScrollDirection();
  const { isAuthenticated } = useAuth();

  return (
    <div className={cls.root}>
      <div className={`${cls.scroll} ${cls[scrollDirection]}`}>
        <div className={cls.flex}>
          <div className={cls.wrapper}>
            <ul className={cls.list}>
              <li className={cls.item}>
                <Link
                  href={"/"}
                  className={`${cls.link} ${
                    pathname === "/" ? cls.active : ""
                  }`}
                >
                  <RestaurantFillIcon />
                  <span className={cls.text}>{t("foods")}</span>
                </Link>
              </li>
              {isAuthenticated && (
                <li className={cls.item}>
                  <Link
                    href={"/orders"}
                    className={`${cls.link} ${
                      pathname.includes("orders") ? cls.active : ""
                    }`}
                  >
                    <HistoryFillIcon />
                    <span className={cls.text}>{t("orders")}</span>
                  </Link>
                </li>
              )}
              <li className={cls.item}>
                <Link
                  href={"/liked"}
                  className={`${cls.link} ${
                    pathname.includes("liked") ? cls.active : ""
                  }`}
                >
                  <HeartLineIcon />
                  <span className={cls.text}>{t("liked")}</span>
                </Link>
              </li>
            </ul>
          </div>
          {isAuthenticated ? <ProtectedCartButton /> : <CartButton />}
        </div>
      </div>
    </div>
  );
}
