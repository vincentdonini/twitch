import React, {useContext, useEffect} from 'react';
import {RootState, store} from "@stores/stores";
import {useSelector} from "react-redux";
import {TwitchAuthContext} from "@modules/twitch/context/TwitchAuthProvider";
import UserEngagement from "@common/ui/UserEngagement";
import {Divider} from "@mui/material";
import {loadTwitchData} from "@modules/twitch/application/loadTwitchData";

const UserEngagementList: React.FC = () => {

    const {lastSub, topDonor, lastDonor, lastFollow} = useSelector((state: RootState) => state.twitchEngagement);
    const auth = useContext(TwitchAuthContext);

    useEffect(() => {
        if (!auth) return;
        const token = localStorage.getItem("twitch_token");
        if (!token) return;
        loadTwitchData(token).catch(console.error);
    }, [auth]);

    return (
        <>
            <div
                className="absolute left-0 right-0 bottom-[5px] w-[1920px] h-[48px] flex flex-row items-end overflow-hidden z-[999]">
                <div className="flex flex-row w-[721px] h-[48px] text-white overflow-hidden">
                    <UserEngagement title="Top cheerer" text={topDonor || "..."}/>
                    <Divider/>
                    <UserEngagement title="Last cheerer" text={lastDonor || "..."}/>
                </div>
                <div
                    style={{
                        width: 478,
                        textAlign: 'center',
                        overflow: 'hidden',
                        alignItems: 'end',
                    }}
                >
                    <div
                        className="heading-pro-regular flex items-center justify-center text-center overflow-hidden text-white h-[27px] uppercase text-[20px]">
                        <div className="leading-[1em] pt-[2px]">
                            {/*twitch.tv/vaince_woder*/}
                        </div>

                    </div>
                </div>
                <div className="flex flex-row w-[721px] h-[48px] overflow-hidden">
                    <UserEngagement title="Last follow" text={lastFollow || "..."}/>
                    <Divider/>
                    <UserEngagement title="Last susbcriber" text={lastSub || "..."}/>
                </div>
            </div>
        </>
    );
};

export default UserEngagementList;
