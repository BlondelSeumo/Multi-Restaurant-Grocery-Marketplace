import cart from './slices/cart';
import formLang from './slices/formLang';
import menu from './slices/menu';
import order from './slices/order';
import category from './slices/category';
import brand from './slices/brand';
import banner from './slices/banner';
import product from './slices/product';
import restourant from './slices/restourant';
import unit from './slices/unit';
import orders from './slices/orders';
import currency from './slices/currency';
import discount from './slices/discount';
import deliveries from './slices/deliveries';
import blog from './slices/blog';
import notification from './slices/notification';
import deliveryman from './slices/deliveryman';
import user from './slices/user';
import extraGroup from './slices/extraGroup';
import extraValue from './slices/extraValue';
import payment from './slices/payment';
import invite from './slices/invite';
import faq from './slices/faq';
import client from './slices/client';
import transaction from './slices/transaction';
import allShops from './slices/allShops';
import auth from './slices/auth';
import backup from './slices/backup';
import productReview from './slices/productReview';
import orderReview from './slices/orderReview';
import globalSettings from './slices/globalSettings';
import chat from './slices/chat';
import statisticsCount from './slices/statistics/count';
import statisticsSum from './slices/statistics/sum';
import topCustomers from './slices/statistics/topCustomers';
import topProducts from './slices/statistics/topProducts';
import orderCounts from './slices/statistics/orderCounts';
import orderSales from './slices/statistics/orderSales';
import myShop from './slices/myShop';
import wallet from './slices/wallet';
import payoutRequests from './slices/payoutRequests';
import theme from './slices/theme';
import point from './slices/point';
import role from './slices/role';
import languages from './slices/languages';
import shopCategory from './slices/shopCategory';
import orderStatus from './slices/orderStatus';
import shop from './slices/shop';
import bonus from './slices/product-bonus';
import shopBonus from './slices/shop-bonus';
import subscriber from './slices/subscriber';
import messageSubscriber from './slices/messegeSubscriber';
import storeis from './slices/storeis';
import emailProvider from './slices/emailProvider';
import workingDays from './slices/shopWorkingDays';
import closeDates from './slices/shopClosedDays';
import refund from './slices/refund';
import productReport from './slices/report/products';
import categoryReport from './slices/report/categories';
import orderReport from './slices/report/order';
import stockReport from './slices/report/stock';
import revenueReport from './slices/report/revenue';
import overviewReport from './slices/report/overview';
import extrasReport from './slices/report/extras';
import branch from './slices/branch';
import deliveryStatistics from './slices/delivery-statistic';
import addons from './slices/addons';
import shopTag from './slices/shopTag';
import adminPayouts from './slices/adminPayouts';
import sellerOrders from './slices/sellerOrders';
import deliveryboyReview from './slices/deliveryboyReview';
import bonusList from './slices/bonus-list';
import coupons from './slices/coupons';
import todo from './slices/todo';
import paymentPayload from './slices/paymentPayload';
import sms from './slices/sms-geteways';
import box from './slices/box';
import recipeCategory from './slices/recipe-category';
import reciept from './slices/reciept'

const rootReducer = {
  sms,
  coupons,
  bonusList,
  sellerOrders,
  deliveryboyReview,
  shopTag,
  deliveryStatistics,
  addons,
  branch,
  refund,
  closeDates,
  workingDays,
  emailProvider,
  storeis,
  messageSubscriber,
  subscriber,
  bonus,
  shopBonus,
  shop,
  orderStatus,
  shopCategory,
  languages,
  cart,
  menu,
  formLang,
  order,
  category,
  brand,
  banner,
  product,
  restourant,
  unit,
  orders,
  currency,
  discount,
  deliveries,
  blog,
  notification,
  deliveryman,
  user,
  extraGroup,
  extraValue,
  payment,
  invite,
  faq,
  client,
  transaction,
  allShops,
  auth,
  backup,
  productReview,
  orderReview,
  globalSettings,
  chat,
  statisticsCount,
  statisticsSum,
  topProducts,
  topCustomers,
  orderCounts,
  orderSales,
  myShop,
  wallet,
  payoutRequests,
  theme,
  point,
  role,
  productReport,
  categoryReport,
  orderReport,
  stockReport,
  revenueReport,
  overviewReport,
  extrasReport,
  adminPayouts,
  todo,
  paymentPayload,
  box,
  recipeCategory,
  reciept
};

export default rootReducer;
