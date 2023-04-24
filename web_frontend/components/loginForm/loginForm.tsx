import React from "react";
import cls from "./loginForm.module.scss";
import { useTranslation } from "react-i18next";
import Link from "next/link";
import TextInput from "components/inputs/textInput";
import CheckboxInput from "components/inputs/checkboxInput";
import PrimaryButton from "components/button/primaryButton";
import PasswordInput from "components/inputs/passwordInput";
import { useFormik } from "formik";
import { useRouter } from "next/router";
import authService from "services/auth";
import { error } from "components/alert/toast";
import { useAuth } from "contexts/auth/auth.context";
import { setCookie } from "utils/session";
import { LoginCredentials } from "interfaces/user.interface";

type Props = {};
interface formValues {
  email?: string;
  phone?: string;
  password: string;
  login?: string;
  keep_logged?: boolean;
}

export default function LoginForm({}: Props) {
  const { t } = useTranslation();
  const { push } = useRouter();
  const { setUserData } = useAuth();

  const formik = useFormik({
    initialValues: {
      login: "",
      password: "",
      keep_logged: true,
    },
    onSubmit: (values: formValues, { setSubmitting }) => {
      let body: LoginCredentials;
      if (values.login?.includes("@")) {
        body = {
          email: values.login,
          password: values.password,
        };
      } else {
        body = {
          phone: values.login,
          password: values.password,
        };
      }
      console.log("body => ", body);
      authService
        .login(body)
        .then(({ data }) => {
          const token = data.token_type + " " + data.access_token;
          setCookie("access_token", token);
          setUserData(data.user);
          push("/");
        })
        .catch(() => error(t("login.invalid")))
        .finally(() => setSubmitting(false));
    },
    validate: (values: formValues) => {
      const errors: formValues = {} as formValues;
      if (!values.login) {
        errors.login = t("required");
      } else if (
        !/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i.test(values.login)
      )
        if (!values.password) {
          errors.password = "Required";
        }
      return errors;
    },
  });

  return (
    <form className={cls.wrapper} onSubmit={formik.handleSubmit}>
      <div className={cls.header}>
        <h1 className={cls.title}>{t("login")}</h1>
        <p className={cls.text}>
          {t("dont.have.account")} <Link href="/register">{t("sign.up")}</Link>
        </p>
      </div>
      <div className={cls.space} />
      <TextInput
        name="login"
        label={t("login")}
        placeholder={t("type.here")}
        value={formik.values.login}
        onChange={formik.handleChange}
        error={!!formik.errors.login && formik.touched.login}
      />
      <div className={cls.space} />
      <PasswordInput
        name="password"
        label={t("password")}
        placeholder={t("type.here")}
        value={formik.values.password}
        onChange={formik.handleChange}
        error={!!formik.errors.password && formik.touched.login}
      />
      <div className={cls.flex}>
        <div className={cls.item}>
          <CheckboxInput
            id="keep_logged"
            name="keep_logged"
            checked={formik.values.keep_logged}
            onChange={formik.handleChange}
          />
          <label htmlFor="keep_logged" className={cls.label}>
            {t("keep.logged")}
          </label>
        </div>
        <div className={cls.item}>
          <Link href="/reset-password">{t("forgot.password")}</Link>
        </div>
      </div>
      <div className={cls.space} />
      <div className={cls.action}>
        <PrimaryButton type="submit" loading={formik.isSubmitting}>
          {t("login")}
        </PrimaryButton>
      </div>
    </form>
  );
}
