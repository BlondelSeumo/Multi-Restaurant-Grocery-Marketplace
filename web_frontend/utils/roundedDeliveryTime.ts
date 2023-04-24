import { Dayjs } from "dayjs";

export default function roundedDeliveryTime(date: Dayjs, minuteToAdd: number) {
  const deliveryTime = date.format("HH:mm");
  const minutes = Number(deliveryTime.split(":")[1]);
  const rounded = Math.ceil(minutes / 5) * 5;
  const leftMinutes = rounded - minutes + minuteToAdd;
  return date.add(leftMinutes, "minute").format("HH:mm");
}
