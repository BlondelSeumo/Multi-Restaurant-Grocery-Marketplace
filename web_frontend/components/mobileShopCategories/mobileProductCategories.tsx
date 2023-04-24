import React from "react";
import { Category } from "interfaces";
import cls from "./mobileShopCategories.module.scss";
import { useTranslation } from "react-i18next";
import ScrollspyNav from "react-scrollspy-nav";

type Props = {
  data: Category[];
  onClose: () => void;
};

export default function MobileProductCategories({ data, onClose }: Props) {
  const { t } = useTranslation();

  return (
    <div className={cls.wrapper}>
      <ScrollspyNav
        scrollTargetIds={["popular", ...data.map((item) => item.uuid)]}
        offset={-100}
        activeNavClass={cls.active}
        scrollDuration="200"
      >
        <a href={`#popular`} className={`${cls.item}`} onClick={onClose}>
          <span className={cls.text}>{t("popular")}</span>
        </a>
        {data.map((item) => (
          <a
            key={item.id}
            href={`#${item.uuid}`}
            className={cls.item}
            onClick={onClose}
          >
            <span className={cls.text}>{item.translation?.title}</span>
          </a>
        ))}
      </ScrollspyNav>
    </div>
  );
}
