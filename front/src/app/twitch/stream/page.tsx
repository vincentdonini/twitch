"use client";

import React from "react";
import Layout from "@layouts/Layout";
import TwitchLayout from "@layouts/TwitchLayout";
import TwitchChat from "@modules/tmi/ui/TwitchChat";
import AlertBox from "@modules/tmi/ui/AlertBox";
import SoundBox from "@modules/tmi/ui/SoundBox";
import SpotifyNowPlayingCard from "@modules/spotify/ui/SpotifyNowPlayingCard";
import UserEngagementList from "@common/ui/UserEngagementList";

export default function StreamPage() {

    const background = "overlay_clean.png";

    return (
        <TwitchLayout background={background}>
            <UserEngagementList/>
            <Layout
                leftComponent={
                    <>
                        <TwitchChat/>
                    </>
                }
                centerComponent={
                    <>
                        <div className="grow flex flex-col h-full" style={{ border: "10px solid purple" }}>
                            <div className="flex-1" style={{ border: "10px solid red" }}>
                                <AlertBox/>
                                <SoundBox/>
                            </div>
                            <div className="flex-1 relative" style={{ border: "10px solid pink" }}>

                            </div>
                            <div className="flex-1 relative" style={{ border: "10px solid blue" }}>
                                <div className="cat">
                                    <div className="whiskers"></div>
                                    <div className="face">
                                        <div className="ear-l"></div>
                                        <div className="ear-r"></div>
                                    </div>
                                    <div className="tag"></div>
                                    <div className="tail"></div>
                                </div>
                            </div>
                            {/*</div>*/}
                        </div>
                        {/*<SpotifyNowPlayingCard/>*/}
                    </>
                }
                rightComponent={
                    <>
                        rightComponent
                    </>
                }
            />
        </TwitchLayout>
    );
}
