import React from "react";
import { CartProduct } from "interfaces";
import cls from "./cartProduct.module.scss";
import SubtractFillIcon from "remixicon-react/SubtractFillIcon";
import AddFillIcon from "remixicon-react/AddFillIcon";
import getImage from "utils/getImage";
import Price from "components/price/price";
import { useAppDispatch } from "hooks/useRedux";
import { addToCart, reduceCartItem, removeFromCart } from "redux/slices/cart";
import FallbackImage from "components/fallbackImage/fallbackImage";

type Props = {
  data: CartProduct;
};

export default function CartItem({ data }: Props) {
  const dispatch = useAppDispatch();

  function addProduct() {
    dispatch(addToCart({ ...data, quantity: 1 }));
  }

  function reduceProduct() {
    if (data.quantity === 1) {
      dispatch(removeFromCart(data));
    } else {
      dispatch(reduceCartItem(data));
    }
  }

  return (
    <div className={cls.wrapper}>
      <div className={cls.block}>
        <h6 className={cls.title}>
          {data.translation?.title}{" "}
          {data.extras.length > 0 ? `(${data.extras.join(", ")})` : ""}
        </h6>
        <p className={cls.description}>
          {data.addons
            ?.map((item) => item.translation?.title + " x " + item.quantity)
            .join(", ")}
        </p>
        <div className={cls.actions}>
          <div className={cls.counter}>
            <button
              type="button"
              className={cls.counterBtn}
              onClick={reduceProduct}
            >
              <SubtractFillIcon />
            </button>
            <div className={cls.count}>{data.quantity}</div>
            <button
              type="button"
              className={`${cls.counterBtn} ${
                data.stock.quantity > data.quantity ? "" : cls.disabled
              }`}
              disabled={!(data.stock.quantity > data.quantity)}
              onClick={addProduct}
            >
              <AddFillIcon />
            </button>
          </div>
          <div className={cls.price}>
            <Price number={data.stock.price * data.quantity} />
          </div>
        </div>
      </div>
      <div className={cls.imageWrapper}>
        <FallbackImage
          fill
          src={getImage(data.img)}
          alt={data.translation?.title}
          sizes="320px"
          quality={90}
        />
      </div>
    </div>
  );
}
