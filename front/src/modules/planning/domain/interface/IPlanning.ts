import {FaDiscord, FaInstagram, FaTiktok, FaTwitter, FaYoutube} from "react-icons/fa6";
import {IconType} from "react-icons";

export interface IPlanning {
    user: IUser;
    date: IDate;
    config: IConfig;
    socials: ISocials;
    partnerships: IPartnerships;
    data: IData;
}

export interface IData {
    monday?: IDataBlock[];
    tuesday?: IDataBlock[];
    wednesday?: IDataBlock[];
    thursday?: IDataBlock[];
    friday?: IDataBlock[];
    saturday?: IDataBlock[];
    sunday?: IDataBlock[];
}

export interface IDataBlock {
    background?: string;
    logo?: string;
    title: string;
    hours: string;
    colors?: Record<string, string>;
}

export type IConfig = {
    gap: number;
    fonts: Record<string, string>;
    background: IConfigBackground;
    colors: Record<string, string>;
    block: IConfigBlock;
}

export type IConfigBackground = {
    url: string;
    isBlurred: boolean;
    blur: number;
}

export type IConfigBlock = {
    isRounded: boolean;
    radius: number;
    isBorder: boolean;
    borderWidth: number;
}

export type ISocialNetwork = "instagram" | "twitter" | "tiktok" | "youtube" | "discord";

export interface IDate {
    from: string;
    to: string;
}

export interface IUser {
    pseudo: string;
    channelUrl: string;
}

export type ISocials = Record<ISocialNetwork, string>;

export const socialIcons: Record<ISocialNetwork, IconType> = {
    instagram: FaInstagram,
    twitter: FaTwitter,
    tiktok: FaTiktok,
    youtube: FaYoutube,
    discord: FaDiscord,
};

export type IPartnerships = Record<string, string>;

export const dayLabels: Record<keyof IData, string> = {
    monday: "Lundi",
    tuesday: "Mardi",
    wednesday: "Mercredi",
    thursday: "Jeudi",
    friday: "Vendredi",
    saturday: "Samedi",
    sunday: "Dimanche",
};