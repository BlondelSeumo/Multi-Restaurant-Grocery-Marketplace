import React from "react";
import { Product } from "interfaces";
import cls from "./productList.module.scss";
import { Grid, Skeleton, useMediaQuery } from "@mui/material";
import ProductCard from "components/productCard/productCard";
import { useAppDispatch } from "hooks/useRedux";
import { setProduct } from "redux/slices/product";

type Props = {
  title?: string;
  products: Product[];
  loading?: boolean;
  uuid?: string;
};

export default function ProductList({
  title,
  products,
  loading = false,
  uuid = "popular",
}: Props) {
  const isDesktop = useMediaQuery("(min-width:1140px)");
  const dispatch = useAppDispatch();

  const handleOpenProduct = (event: any, data: Product) => {
    event.preventDefault();
    dispatch(setProduct({ product: data }));
  };

  return (
    <section
      className="shop-container"
      id={uuid}
      style={{
        display: !loading && products.length === 0 ? "none" : "block",
      }}
    >
      <div className={cls.container}>
        <div className={cls.header}>
          <h2 className={cls.title}>{title}</h2>
        </div>
        <Grid container spacing={isDesktop ? 3 : 1}>
          {!loading
            ? products.map((item) => (
                <Grid key={item.id} item xs={6} md={6} lg={3}>
                  <ProductCard data={item} handleOpen={handleOpenProduct} />
                </Grid>
              ))
            : Array.from(new Array(4)).map((item, idx) => (
                <Grid key={"shimmer" + idx} item xs={6} md={6} lg={3}>
                  <Skeleton variant="rectangular" className={cls.shimmer} />
                </Grid>
              ))}
        </Grid>
      </div>
    </section>
  );
}
