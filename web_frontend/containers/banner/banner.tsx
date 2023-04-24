import React from "react";
import cls from "./banner.module.scss";
import { Swiper, SwiperSlide } from "swiper/react";
import { Banner, Story } from "interfaces";
import StoryComponent from "components/storySingle/storySingle";
import BannerSingle from "components/bannerSingle/bannerSingle";
import {
  NextButton,
  PrevButton,
} from "components/carouselArrows/carouselArrows";
import { Skeleton } from "@mui/material";

const bannerSettings = {
  spaceBetween: 10,
  preloadImages: false,
  className: "banner-swiper full-width",
  breakpoints: {
    1140: {
      slidesPerView: 2,
      spaceBetween: 20,
    },
  },
};

const storySettings = {
  spaceBetween: 10,
  preloadImages: false,
  className: "story-swiper full-width",
  breakpoints: {
    1140: {
      slidesPerView: 4,
      spaceBetween: 20,
    },
  },
};

type BannerProps = {
  stories?: Story[][];
  banners: Banner[];
  loadingStory: boolean;
  loadingBanner: boolean;
};

export default function BannerContainer({
  stories,
  banners,
  loadingStory,
  loadingBanner,
}: BannerProps) {
  return (
    <div className={`container ${cls.container}`}>
      <div className={cls.banner}>
        <div className={cls.storyContainer}>
          {!loadingStory ? (
            <Swiper {...storySettings} slidesPerView="auto">
              {stories?.map((item, idx) => (
                <SwiperSlide key={idx}>
                  <StoryComponent data={item} list={stories} />
                </SwiperSlide>
              ))}
            </Swiper>
          ) : (
            <div className={cls.shimmerContainer}>
              {Array.from(new Array(4)).map((item, idx) => (
                <Skeleton
                  key={"story" + idx}
                  variant="rectangular"
                  className={cls.shimmer}
                />
              ))}
            </div>
          )}
        </div>
        <div className={cls.bannerContainer}>
          {!loadingBanner ? (
            <Swiper {...bannerSettings} slidesPerView="auto">
              {banners.map((item) => (
                <SwiperSlide key={item.id}>
                  <BannerSingle data={item} />
                </SwiperSlide>
              ))}
              {banners.length > 2 && (
                <>
                  <PrevButton />
                  <NextButton />
                </>
              )}
            </Swiper>
          ) : (
            <div className={cls.shimmerContainer}>
              {Array.from(new Array(2)).map((item, idx) => (
                <Skeleton
                  key={"banner" + idx}
                  variant="rectangular"
                  className={cls.shimmer}
                />
              ))}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
