export default function numberToQuantity(number, unit) {
  const title = unit?.translation?.title || 'pcs';
  if (!number) {
    return 0;
  }
  switch (unit?.position) {
    case 'after':
      return number + ' ' + title;
    case 'before':
      return title + ' ' + number;

    default:
      break;
  }
}
