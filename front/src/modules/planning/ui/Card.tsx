import React from "react";

type Props = {
    backgroundUrl: string;
    logoUrl: string;
    text: string;
    className?: string;
};

const BackgroundImageWithLogo: React.FC<Props> = (
    {
        backgroundUrl,
        logoUrl,
        text,
        className = "",
    }) => {
    return (
        <div
            className={`flex-1 relative flex flex-col items-center justify-between p-4 ${className}`}
            style={{
                backgroundImage: `url('${backgroundUrl}')`,
                backgroundSize: "cover",
                backgroundPosition: "center",
            }}
        >
            <img src={logoUrl} className="w-auto h-auto z-10 max-h-[40%]"/>
            <div className="text-white">
                {text}
            </div>
        </div>
    );
};

export default BackgroundImageWithLogo;
