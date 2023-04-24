import React, { useState } from "react";
import { useTranslation } from "react-i18next";
import RadioInput from "components/inputs/radioInput";
import cls from "./paymentMethod.module.scss";
import PrimaryButton from "components/button/primaryButton";
import { FormikProps } from "formik";
import { OrderFormValues, Payment } from "interfaces";

type Props = {
  formik: FormikProps<OrderFormValues>;
  list: Payment[];
  handleClose: () => void;
};

export default function PaymentMethod({ formik, list, handleClose }: Props) {
  const { t } = useTranslation();
  const [selectedValue, setSelectedValue] = useState(
    formik.values.payment_type?.tag
  );

  const handleChange = (event: React.ChangeEvent<HTMLInputElement>) => {
    setSelectedValue(event.target.value);
  };

  const controlProps = (item: string) => ({
    checked: selectedValue === item,
    onChange: handleChange,
    value: item,
    id: item,
    name: "payment_method",
    inputProps: { "aria-label": item },
  });

  const handleSubmit = () => {
    const payment = list.find((item) => item.tag === selectedValue);
    formik.setFieldValue("payment_type", payment);
    handleClose();
  };

  return (
    <div className={cls.wrapper}>
      <div className={cls.body}>
        {list.map((item) => (
          <div key={item.id} className={cls.row}>
            <RadioInput {...controlProps(item.tag)} />
            <label className={cls.label} htmlFor={item.tag}>
              <span className={cls.text}>{t(item.tag)}</span>
            </label>
          </div>
        ))}
      </div>
      <div className={cls.footer}>
        <div className={cls.action}>
          <PrimaryButton onClick={handleSubmit}>{t("save")}</PrimaryButton>
        </div>
      </div>
    </div>
  );
}
