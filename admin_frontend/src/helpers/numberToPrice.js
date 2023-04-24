export default function numberToPrice(number, symbol = '$') {
  if (!number) {
    return '0';
  }
  const price = Number(number)
    .toFixed(2)
    .replace(/\d(?=(\d{3})+\.)/g, '$&,');
  return symbol + ' ' + price;
}
