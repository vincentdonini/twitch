import React, {JSX} from "react";
import {FaDiscord, FaInstagram, FaTiktok, FaTwitter, FaYoutube} from "react-icons/fa6";
import {IConfig} from "@modules/planning/domain/interface/IPlanning";

const icons: Record<string, JSX.Element> = {
    instagram: <FaInstagram/>,
    twitter: <FaTwitter/>,
    tiktok: <FaTiktok/>,
    youtube: <FaYoutube/>,
    discord: <FaDiscord/>,
};

type Props = {
    config: IConfig;
    socials: Record<string, string>;
};

const SocialLinks: React.FC<Props> = (
    {
        config,
        socials,
    }
) => {
    const fontTitle = config.fonts.title;

    const textColor = config.colors.text;
    const subTextColor = config.colors.subText;
    const primaryColor = config.colors.primary;

    return (
        <div className="flex flex-row gap-5 text-white">
            {Object.entries(socials).map(([network, handle]) => (
                <div key={network} className="flex items-start gap-2">
                    <div
                        className="w-10 h-10 flex items-center justify-center rounded-sm text-xl"
                        style={{
                            font: fontTitle,
                            color: textColor,
                            backgroundColor: primaryColor,
                        }}
                    >
                        {icons[network]}
                    </div>
                    <div className="flex flex-col">
                        <div className="capitalize font-semibold">
                            {network}
                        </div>
                        <div className="text-sm"
                             style={{
                                 color: subTextColor,
                             }}
                        >
                            @{handle}
                        </div>
                    </div>
                </div>
            ))}
        </div>

    );
};

export default SocialLinks;
