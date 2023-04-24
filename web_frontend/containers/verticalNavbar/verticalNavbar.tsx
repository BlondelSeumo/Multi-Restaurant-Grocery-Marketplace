import React from "react";
import { Category } from "interfaces";
import cls from "./verticalNavbar.module.scss";
import { useTranslation } from "react-i18next";
import ScrollspyNav from "react-scrollspy-nav";
import Link from "next/link";
import ArrowLeftLineIcon from "remixicon-react/ArrowLeftLineIcon";
import { Skeleton } from "@mui/material";

type Props = {
  categories: Category[];
  loading?: boolean;
};

export default function VerticalNavbar({ categories = [], loading }: Props) {
  const { t } = useTranslation();

  return (
    <div className={cls.container}>
      <div className={cls.wrapper}>
        <Link href="/shop" className={cls.backButton}>
          <ArrowLeftLineIcon />
          <span className={cls.text}>{t("all.shops")}</span>
        </Link>
        <h3 className={cls.title}>{t("catalog")}</h3>
        {!loading ? (
          <ScrollspyNav
            scrollTargetIds={[
              "popular",
              ...categories.map((item) => item.uuid),
            ]}
            offset={-50}
            activeNavClass={cls.active}
            scrollDuration="200"
          >
            <ul className={cls.navbar}>
              <li className={cls.navItem}>
                <a href={`#popular`} className={`${cls.navLink}`}>
                  {t("popular")}
                </a>
              </li>
              {categories.map((item) => (
                <li key={item.id} className={cls.navItem}>
                  <a href={`#${item.uuid}`} className={`${cls.navLink}`}>
                    {item.translation?.title}
                  </a>
                </li>
              ))}
            </ul>
          </ScrollspyNav>
        ) : (
          Array.from(new Array(4)).map((_, idx) => (
            <Skeleton
              key={"category" + idx}
              variant="rectangular"
              className={cls.shimmer}
            />
          ))
        )}
      </div>
    </div>
  );
}
