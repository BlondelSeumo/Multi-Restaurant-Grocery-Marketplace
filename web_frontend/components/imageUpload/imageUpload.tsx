import React from "react";
import cls from "./imageUpload.module.scss";
import { FormikProps } from "formik";
import { ShopFormType } from "interfaces";
import { useMutation } from "react-query";
import galleryService from "services/gallery";
import Image from "next/image";
import getImage from "utils/getImage";
import DeleteBinLineIcon from "remixicon-react/DeleteBinLineIcon";
import UploadCloud2LineIcon from "remixicon-react/UploadCloud2LineIcon";
import Loading from "components/loader/loading";

type Props = {
  formik?: FormikProps<ShopFormType>;
  name: string;
  label: string;
  value: string;
  error?: boolean;
};

export default function ImageUpload({
  formik,
  name,
  label,
  value,
  error,
}: Props) {
  const { mutate: upload, isLoading: isUploading } = useMutation({
    mutationFn: (data: any) => galleryService.upload(data),
    onSuccess: (data) => {
      formik?.setFieldValue(name, data.data.title);
    },
  });

  const handleChange = (event: any) => {
    const file = event.target.files[0];
    const formData = new FormData();
    formData.append("image", file);
    formData.append("type", "shops");
    upload(formData);
  };

  const handleDelete = () => {
    formik?.setFieldValue(name, "");
  };

  return (
    <div>
      {value ? (
        <div className={`${cls.imageContainer} ${error ? cls.error : ""}`}>
          <Image
            fill
            src={getImage(value)}
            className={cls.images}
            sizes="180px"
            alt="shop"
          />
          <div className={cls.overlay}>
            <button
              type="button"
              onClick={handleDelete}
              className={cls.deleteBtn}
            >
              <DeleteBinLineIcon />
            </button>
          </div>
        </div>
      ) : (
        <label htmlFor="file" className={cls.fileInput}>
          <div className={`${cls.uploadButton} ${error ? cls.error : ""}`}>
            <input hidden id="file" type="file" onChange={handleChange} />
            <UploadCloud2LineIcon className={cls.icon} />
            <p className={cls.text}>{label}</p>
            {isUploading && <Loading />}
          </div>
        </label>
      )}
    </div>
  );
}
