"use client";

import Image from "next/image";
import TwitchLayout from "@layouts/TwitchLayout";
import {GlitchHandle, useGlitch} from "react-powerglitch";

export default function EndingSoonPage() {

    const glitch: GlitchHandle = useGlitch({
        glitchTimeSpan: { start: 0.2, end: 0.3 },
    });

    const background = "background.jpg";

    return (
        <TwitchLayout background={background}>
            <div
                ref={glitch.ref}
                className="text-white absolute top-[30px] right-[60px]"
            >
                <div
                    className="heading-pro-extra-bold-italic text-uppercase text-[200px] leading-none p-0 m-0"
                >
                    Stream ended
                </div>
                <div
                    className="heading-pro-extra-bold-italic primary-color text-uppercase text-[110px] leading-none p-0 mt-[-40px]"
                >
                    Thanks for watching
                </div>

                {/*<div*/}
                {/*    className="heading-pro-extra-bold-italic primary-color text-uppercase text-[60px] leading-none p-0 mt-[40px]"*/}
                {/*>*/}
                {/*    Don't forget to follow if you like my stream*/}
                {/*</div>*/}
            </div>
        </TwitchLayout>
    );
}
