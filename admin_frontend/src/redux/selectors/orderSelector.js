export function calculateTotalWithDeliveryPrice(deliveries, total) {
  const totalDeliveryPrice = deliveries.reduce(
    (total, item) => (total += item.delivery_fee),
    0
  );
  let totalPrice = total;

  if (totalDeliveryPrice) {
    totalPrice += totalDeliveryPrice;
  }

  return totalPrice;
}
export function getCurrentShop(shop, allShops) {
  if (!allShops.length || !shop) {
    return {};
  }
  return allShops.find((item) => item.id === shop.value);
}
