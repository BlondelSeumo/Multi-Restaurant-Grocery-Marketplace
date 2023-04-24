import { useCallback, useEffect, useRef } from "react";
import SEO from "components/seo";
import dynamic from "next/dynamic";
import { useInfiniteQuery, useQuery } from "react-query";
import { useTranslation } from "react-i18next";
import { useMediaQuery } from "@mui/material";
import shopService from "services/shop";
import categoryService from "services/category";
import { selectShopFilter } from "redux/slices/shopFilter";
import { useAppSelector } from "hooks/useRedux";
import storyService from "services/story";
import bannerService from "services/banner";
import useUserLocation from "hooks/useUserLocation";
import qs from "qs";

const Empty = dynamic(() => import("components/empty/empty"));
const Loader = dynamic(() => import("components/loader/loader"));
const Navbar = dynamic(() => import("containers/navbar/navbar"));
const MobileNavbar = dynamic(
  () => import("containers/mobileNavbar/mobileNavbar")
);
const BannerContainer = dynamic(() => import("containers/banner/banner"));
const FeaturedShopsContainer = dynamic(
  () => import("containers/featuredShopsContainer/featuredShopsContainer")
);
const StoreList = dynamic(() => import("containers/storeList/storeList"));
const ZoneNotFound = dynamic(
  () => import("components/zoneNotFound/zoneNotFound")
);
const ShopList = dynamic(() => import("containers/shopList/shopList"));
const FooterMenu = dynamic(() => import("containers/footerMenu/footerMenu"));

const PER_PAGE = 12;

export default function Home() {
  const { t, i18n } = useTranslation();
  const locale = i18n.language;
  const isDesktop = useMediaQuery("(min-width:1140px)");
  const loader = useRef(null);
  const { category_id, newest, order_by, group } =
    useAppSelector(selectShopFilter);
  const isFilterActive = !!Object.keys(group).length;
  const location = useUserLocation();

  const { data: stories, isLoading: isStoriesLoading } = useQuery(
    ["stories", locale],
    () => storyService.getAll()
  );

  const { data: banners, isLoading: isBannerLoading } = useQuery(
    ["banners", locale],
    () => bannerService.getAll()
  );

  const { isSuccess: isInsideZone, isLoading: isZoneLoading } = useQuery(
    ["shopZones", location],
    () =>
      shopService.checkZone({
        address: location,
      })
  );

  const { data: shops, isLoading: isShopLoading } = useQuery(
    ["shops", location, locale],
    () =>
      shopService.getAllShops(
        qs.stringify({
          perPage: PER_PAGE,
          address: location,
          open: 1,
        })
      )
  );

  const {
    data,
    error,
    fetchNextPage,
    hasNextPage,
    isFetchingNextPage,
    isLoading,
  } = useInfiniteQuery(
    ["restaurants", category_id, locale, order_by, group, location, newest],
    ({ pageParam = 1 }) =>
      shopService.getAllRestaurants(
        qs.stringify({
          page: pageParam,
          perPage: PER_PAGE,
          category_id: category_id || undefined,
          order_by: newest ? "new" : order_by,
          free_delivery: group.free_delivery,
          take: group.tag,
          rating: group.rating?.split(","),
          prices: group.prices,
          address: location,
          open: Number(group.open) || undefined,
          deals: group.deals,
        })
      ),
    {
      getNextPageParam: (lastPage: any) => {
        if (lastPage.meta.current_page < lastPage.meta.last_page) {
          return lastPage.meta.current_page + 1;
        }
        return undefined;
      },
    }
  );
  const restaurants = data?.pages?.flatMap((item) => item.data) || [];

  const { data: recommendedShops, isLoading: recommendedLoading } = useQuery(
    ["recommendedShops", locale, location],
    () => shopService.getRecommended({ address: location })
  );

  const { data: shopCategories } = useQuery(["shopCategories", locale], () =>
    categoryService.getAllShopCategories({ perPage: 100 })
  );

  const handleObserver = useCallback(
    (entries: any) => {
      const target = entries[0];
      if (target.isIntersecting && hasNextPage) {
        fetchNextPage();
      }
    },
    [fetchNextPage, hasNextPage]
  );

  useEffect(() => {
    const option = {
      root: null,
      rootMargin: "20px",
      threshold: 0,
    };
    const observer = new IntersectionObserver(handleObserver, option);
    if (loader.current) observer.observe(loader.current);
  }, [handleObserver, hasNextPage, fetchNextPage]);

  if (error) {
    console.log("error => ", error);
  }

  return (
    <>
      <SEO />
      <BannerContainer
        stories={stories || []}
        banners={banners?.data || []}
        loadingStory={isStoriesLoading}
        loadingBanner={isBannerLoading}
      />
      <StoreList
        title={t("shops")}
        shops={shops?.data || []}
        loading={isShopLoading}
      />
      {isDesktop ? (
        <Navbar categories={shopCategories?.data || []} />
      ) : (
        <MobileNavbar categories={shopCategories?.data || []} />
      )}
      <div style={{ minHeight: "60vh" }}>
        {!category_id && !newest && !isFilterActive && isInsideZone && (
          <FeaturedShopsContainer
            title={t("recommended")}
            featuredShops={recommendedShops?.data || []}
            loading={recommendedLoading}
          />
        )}
        <ShopList
          title={newest ? t("news.week") : t("all.restaurants")}
          shops={restaurants}
          loading={isLoading && !isFetchingNextPage}
        />
        {isFetchingNextPage && <Loader />}
        <div ref={loader} />

        {!isInsideZone && !isZoneLoading && <ZoneNotFound />}
        {!restaurants.length && !isLoading && isInsideZone && (
          <Empty text={t("no.restaurants")} />
        )}
      </div>
      <FooterMenu />
    </>
  );
}
