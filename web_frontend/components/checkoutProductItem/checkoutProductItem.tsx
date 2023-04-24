import React, { useState } from "react";
import { CartStockWithProducts } from "interfaces";
import cls from "./checkoutProductItem.module.scss";
import SubtractFillIcon from "remixicon-react/SubtractFillIcon";
import AddFillIcon from "remixicon-react/AddFillIcon";
import getImage from "utils/getImage";
import Price from "components/price/price";
import useDebounce from "hooks/useDebounce";
import { selectCurrency } from "redux/slices/currency";
import { useAppDispatch, useAppSelector } from "hooks/useRedux";
import { useRouter } from "next/router";
import cartService from "services/cart";
import { updateUserCart } from "redux/slices/userCart";
import { useMutation, useQuery } from "react-query";
import useDidUpdate from "hooks/useDidUpdate";
import Loading from "components/loader/loading";
import { useTranslation } from "react-i18next";
import calculateCartProductTotal from "utils/calculateCartProductTotal";
import FallbackImage from "components/fallbackImage/fallbackImage";

type Props = {
  data: CartStockWithProducts;
  disabled: boolean;
};

export default function CheckoutProductItem({ data, disabled }: Props) {
  const { t } = useTranslation();
  const [quantity, setQuantity] = useState(data.quantity);
  const debouncedQuantity = useDebounce(quantity, 400);
  const currency = useAppSelector(selectCurrency);
  const dispatch = useAppDispatch();
  const { query } = useRouter();
  const shopId = Number(query.id);
  const isReduceDisabled = quantity < 1 || data.bonus || disabled;
  const isAddDisabled =
    !(data.stock.quantity > quantity) || data.bonus || disabled;
  const { totalPrice, oldPrice } = calculateCartProductTotal(data);

  const { refetch, isLoading: isCartLoading } = useQuery(
    "cart",
    () => cartService.get(),
    {
      onSuccess: (data) => dispatch(updateUserCart(data.data)),
      enabled: false,
    }
  );

  const { mutate: storeProduct, isLoading } = useMutation({
    mutationFn: (data: any) => cartService.insert(data),
    onSuccess: (data) => {
      dispatch(updateUserCart(data.data));
    },
  });

  const { mutate: deleteProducts, isLoading: isDeleteLoading } = useMutation({
    mutationFn: (data: any) => cartService.deleteCartProducts(data),
    onSuccess: () => refetch(),
  });

  function addProduct() {
    setQuantity((count) => count + 1);
  }

  function reduceProduct() {
    if (quantity === 1) {
      setQuantity(0);
    } else {
      setQuantity((count) => count - 1);
    }
  }

  useDidUpdate(() => {
    if (debouncedQuantity) {
      storeProductToCart(data);
    } else {
      deleteFromCart(data);
    }
  }, [debouncedQuantity]);

  function storeProductToCart(product: CartStockWithProducts) {
    const body = {
      shop_id: shopId,
      currency_id: currency?.id,
      rate: currency?.rate,
      products: [
        {
          stock_id: product.stock.id,
          quantity,
        },
      ],
    };
    if (!product.bonus) {
      storeProduct(body);
    }
  }

  function deleteFromCart(product: CartStockWithProducts) {
    const addons = product.addons?.map((item) => item.stock.id) || [];
    deleteProducts({ ids: [product.id, ...addons] });
  }

  return (
    <div className={cls.row}>
      <div className={cls.col}>
        <h4 className={cls.title}>
          {data.stock.product?.translation.title}{" "}
          {data.stock.extras?.length
            ? data.stock.extras.map((item, idx) => (
                <span key={"extra" + idx}>({item.value})</span>
              ))
            : ""}
          {data.bonus && <span className={cls.red}> {t("bonus")}</span>}
        </h4>
        <p className={cls.desc}>
          {data.addons
            ?.map(
              (item) =>
                item.stock?.product?.translation?.title + " x " + item.quantity
            )
            .join(", ")}
        </p>
        <div className={cls.actions}>
          <div className={cls.counter}>
            <button
              type="button"
              className={`${cls.counterBtn} ${
                isReduceDisabled ? cls.disabled : ""
              }`}
              disabled={isReduceDisabled}
              onClick={reduceProduct}
            >
              <SubtractFillIcon />
            </button>
            <div className={cls.count}>{quantity}</div>
            <button
              type="button"
              className={`${cls.counterBtn} ${
                isAddDisabled ? cls.disabled : ""
              }`}
              disabled={isAddDisabled}
              onClick={addProduct}
            >
              <AddFillIcon />
            </button>
          </div>
          <div className={cls.price}>
            {!!data.discount && (
              <span className={cls.oldPrice}>
                <Price number={oldPrice} old />
              </span>
            )}
            <Price number={totalPrice} />
          </div>
        </div>
      </div>
      <div className={cls.imageWrapper}>
        <FallbackImage
          fill
          src={getImage(data.stock.product?.img)}
          alt={data.stock.product?.translation.title}
          sizes="320px"
          quality={90}
        />
      </div>

      {(isLoading || isCartLoading || isDeleteLoading) && <Loading />}
    </div>
  );
}
