import React from "react";
import SEO from "components/seo";
import AboutUs from "containers/aboutUs/aboutUs";
import { useTranslation } from "react-i18next";
import AppSection from "containers/appSection/appSection";

type Props = {};

export default function About({}: Props) {
  const { t } = useTranslation();

  return (
    <>
      <SEO title={t("about")} />
      <AboutUs />
      <AppSection />
    </>
  );
}
