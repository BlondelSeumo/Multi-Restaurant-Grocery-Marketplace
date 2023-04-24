import React from "react";
import cls from "./button.module.scss";

type ButtonType = "button" | "submit";
type ButtonSize = "small" | "medium" | "large";

type Props = {
  children: any;
  disabled?: boolean;
  onClick?: () => void;
  type?: ButtonType;
  icon?: React.ReactElement;
  size?: ButtonSize;
};

export default function DarkButton({
  children,
  disabled,
  onClick,
  type = "button",
  icon,
  size = "medium",
}: Props) {
  return (
    <button
      type={type}
      className={`${cls.darkBtn} ${cls[size]} ${disabled ? cls.disabled : ""}`}
      disabled={disabled}
      onClick={onClick}
    >
      {icon ? icon : ""}
      <span className={cls.text}>{children}</span>
    </button>
  );
}
