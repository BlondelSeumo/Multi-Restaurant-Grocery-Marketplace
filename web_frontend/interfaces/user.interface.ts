import { INotification, IShop } from "interfaces";

export interface IUser {
  id: number;
  firstname: string;
  lastname: string;
  img: string;
  birthday?: string;
  email?: string;
  gender?: string;
  phone?: string;
  role?: string;
  uuid?: string;
  wallet?: IUserWallet;
  notifications?: INotification[];
  shop?: IShop;
  referral_from_topup_count?: number;
  referral_from_topup_price?: number;
  referral_from_withdraw_count?: number;
  referral_from_withdraw_price?: number;
  referral_to_topup_count?: number;
  referral_to_topup_price?: number;
  referral_to_withdraw_count?: number;
  referral_to_withdraw_price?: number;
  my_referral?: string;
  empty_p?: boolean;
}

export interface LoginCredentials {
  email?: string;
  phone?: string;
  password: string;
}

export interface RegisterCredentials extends LoginCredentials {
  firstname: string;
  lastname: string;
  password_confirmation: string;
  gender: string;
  referral?: string;
  type?: string;
}

export interface LoginResponse {
  access_token: string;
  token_type: string;
  user: IUser;
}

export interface SocialLoginCredentials {
  type: "google" | "facebook" | "apple";
  data: {
    name: string | null;
    email: string | null;
    id: string;
    avatar: string | null;
  };
}

export interface IUserWallet {
  uuid: string;
  price: number;
  symbol: string;
}

export interface VerifyCredentials {
  verifyId?: string;
  verifyCode?: string;
}
