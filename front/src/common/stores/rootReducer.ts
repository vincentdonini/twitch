import { combineReducers } from '@reduxjs/toolkit';
import alertQueueReducer from '@modules/tmi/application/store/alertQueueSlice';
import soundQueueReducer from '@modules/tmi/application/store/soundQueueSlice';
import engagementReducer from '@modules/tmi/application/store/engagementSlice';
import chatReducer from "./chatSlice";
import spotifyReducer from "./spotifySlice";
import activeSubscriptionsReducer from "./activeSubscriptionsSlice";

const rootReducer = combineReducers({
    alertQueue: alertQueueReducer,
    soundQueue: soundQueueReducer,
    chat: chatReducer,
    spotify: spotifyReducer,
    activeSubscriptions: activeSubscriptionsReducer,
    twitchEngagement: engagementReducer,
});

export type RootReducer = ReturnType<typeof rootReducer>;
export default rootReducer;
