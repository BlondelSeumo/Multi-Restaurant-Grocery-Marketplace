import React from "react";
import { CircularProgress } from "@mui/material";

type Props = {};

export default function Loader({}: Props) {
  return (
    <div style={{ textAlign: "center", padding: "10px 0" }}>
      <CircularProgress />
    </div>
  );
}
