"use client";

import React, {useEffect, useState} from "react";
import "@common/globals.scss";
import {IPlanning} from "@modules/planning/domain/interface/IPlanning";
import Planning from "@modules/planning/ui/Planning";
import SocialBlocks from "@modules/planning/ui/SocialBlocks";
import PartnershipBlocks from "@modules/planning/ui/PartnershipBlocks";

export default function HomePage() {
    const [planning, setPlanning] = useState<IPlanning | null>(null);

    useEffect(() => {
        fetch('/data/planning.json')
            .then((res) => res.json())
            .then((json) => setPlanning(json));
    }, []);

    if (!planning) {
        return (
            <html lang="fr">
            <head>
                <title>Planning</title>
            </head>
            <body>
            <div>
                Chargement du planning...
            </div>
            </body>
            </html>
        );
    }

    const isBlurred = planning.config.background.isBlurred ? planning.config.background.blur : 0;
    const background = planning.config.background.url;

    const textColor = planning.config.colors.text;
    const primaryColor = planning.config.colors.primary;
    const secondaryColor = planning.config.colors.secondary;

    const fontTitle = planning.config.fonts.title;
    const fontBase = planning.config.fonts.base;

    const fromDate = planning.date.from;
    const toDate = planning.date.to;

    const pseudo = planning.user.pseudo;
    const channelUrl = planning.user.channelUrl;

    return (
        <html lang="fr">
        <head>
            <title>Planning</title>
            <style>
                {`@import url('https://fonts.googleapis.com/css2?family=${fontTitle.replace(/ /g, '%20')}&display=swap');`}
                {`@import url('https://fonts.googleapis.com/css2?family=${fontBase.replace(/ /g, '%20')}&display=swap');`}
                {`
                    * {
                        font-family: '${fontBase}', sans-serif;
                    }
                    .item-day:not(:last-child) {
                        border-right: 1px solid ${secondaryColor};
                        padding-right: 2px;
                        margin-right: 2px;
                    }
                `}
            </style>
        </head>
        <body>
        <div id="calendar-container"
             className="w-[1280px] h-[720px] flex flex-col bg-black"
        >
            <div className="w-[1280px] h-[720px] flex absolute z-0 overflow-hidden">
                <div className="w-full h-full absolute top-0 left-0 bg-black bg-opacity-80 blur-[2px]"
                     style={{
                         filter: `blur(${isBlurred})`,
                         backgroundImage: `url('${background}')`,
                         backgroundSize: "cover",
                         backgroundPosition: "center",
                     }}
                ></div>
            </div>

            <div id="calendar-content"
                 className="flex flex-col flex-1 h-full z-999 p-5 gap-5 content-stretch"
            >
                <div id="calendar-header"
                     className="flex items-start justify-between items-end"
                     style={{
                         color: textColor,
                     }}
                >
                    <div className="flex flex-1 items-end text-5xl font-bold gap-4"
                         style={{
                             color: textColor,
                             fontFamily: fontTitle,
                             fontWeight: 700,
                         }}
                    >
                        Programme du
                        <span
                            style={{
                                color: primaryColor,
                                fontFamily: fontTitle,
                            }}
                        >
                            {fromDate}
                        </span>
                        au
                        <span
                            style={{
                                color: primaryColor,
                                fontFamily: fontTitle,
                            }}
                        >
                            {toDate}
                        </span>
                    </div>
                    <div className="flex flex-col items-end text-4xl font-bold"
                         style={{
                             color: textColor,
                             fontFamily: fontTitle,
                         }}
                    >
                        {pseudo}
                        <span className="text-sm"
                              style={{
                                  color: primaryColor,
                              }}
                        >
                            {channelUrl}
                        </span>
                    </div>
                </div>

                <Planning config={planning.config} data={planning.data} />

                <div id="calendar-footer"
                     className="flex items-center justify-between"
                     style={{
                         height: "50px",
                         color: textColor,
                     }}
                >
                    <SocialBlocks config={planning.config} socials={planning.socials}/>
                    <PartnershipBlocks partnerships={planning.partnerships}/>
                </div>
            </div>
        </div>
        </body>
        </html>
    );
}
