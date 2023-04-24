import React from "react";
import SEO from "components/seo";
import { useRouter } from "next/router";
import careers from "data/careers";
import CareersContent from "containers/content/careersContent";

type Props = {};

export default function CareerSingle({}: Props) {
  const { query } = useRouter();
  const data = careers.find((item) => item.id == Number(query.id));

  return (
    <>
      <SEO title={data?.title} />
      <CareersContent data={data} />
    </>
  );
}
