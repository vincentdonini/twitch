"use client";

import TwitchLayout from "@layouts/TwitchLayout";
import {GlitchHandle, useGlitch} from "react-powerglitch";

export default function BeRightBackPage() {

    const glitch: GlitchHandle = useGlitch({
        glitchTimeSpan: { start: 0.2, end: 0.3 },
    });

    const background = "background.jpg";

    return (
        <TwitchLayout background={background}>
            <div
                // ref={glitch.ref}
                className="text-white absolute top-[380px] left-[150px]"
            >
                <div
                    className="heading-pro-extra-bold-italic text-uppercase text-[200px] leading-none p-0 m-0"
                    // style={{
                    //     textShadow: '10px 10px 10px rgba(0, 0, 0, 0.5)',
                    // }}
                >
                    Be Right Back
                </div>
                <div
                    className="heading-pro-italic primary-color text-uppercase text-[110px] leading-none p-0 mt-[-40px]"
                    // style={{
                    //     textShadow: '10px 10px 10px rgba(0, 0, 0, 0.5)',
                    // }}
                >
                    Live is about to start
                </div>
            </div>
        </TwitchLayout>
    );
}
