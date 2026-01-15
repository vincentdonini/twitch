import React, {useEffect, useState} from 'react';
import {useSelector} from "react-redux";
import {useAudioPlayer} from "react-use-audio-player";
import {RootState, store} from "@stores/stores";
import {removeAlert, setCurrentAlert} from "@modules/tmi/application/store/alertQueueSlice";

const ALERT_DURATION = 10000;
const FADE_DURATION = 500;
const CLEANUP_DELAY = 2000;
const AUDIO_SRC = '/sounds/alert-sound.mp3';

const AlertBox: React.FC = () => {
    const {load, stop, fade} = useAudioPlayer();

    const events = useSelector((state: RootState) => state.alertQueue.alerts);
    const currentAlert = useSelector((state: RootState) => state.alertQueue.currentAlert);

    const [isVisible, setIsVisible] = useState(false);

    useEffect(() => {
        if (!events.length || isVisible) return;

        const nextAlert = events[0];
        store.dispatch(setCurrentAlert(nextAlert));
        setIsVisible(true);

        load(AUDIO_SRC, {
            autoplay: true,
            initialVolume: 0.2,
        });

        fade(0, 1, FADE_DURATION);

        setTimeout(() => {
            fade(1, 0, FADE_DURATION);
            setTimeout(() => stop(), FADE_DURATION);

            setTimeout(() => {
                setIsVisible(false);
                store.dispatch(removeAlert());
            }, CLEANUP_DELAY);

        }, ALERT_DURATION);
    }, [events, isVisible, load, fade, stop]);

    if (!currentAlert) return null;

    return (
        <>
            {isVisible && (
                <div id="alert-box">
                    <div id="line">
                        <div id="box"></div>
                        <div id="mainText">{currentAlert.username}</div>
                        <div id="subText">{currentAlert.message}</div>
                    </div>
                </div>
            )}
        </>
    );
};

export default AlertBox;
