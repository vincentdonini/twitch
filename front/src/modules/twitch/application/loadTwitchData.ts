import {TwitchApi} from "@modules/twitch/infrastructure/TwitchApi";
import {GetLastFollower} from "@modules/twitch/application/GetLastFollower";
import {GetLastSubscriber} from "@modules/twitch/application/GetLastSubscriber";
import {GetTopDonor} from "@modules/twitch/application/GetTopDonor";
import {store} from "@stores/stores";
import {
    setTopDonor,
    setLastFollow,
    setLastSubscription,
} from "@modules/tmi/application/store/engagementSlice";

export async function loadTwitchData(token: string) {
    const api = new TwitchApi(token);
    const getTopDonor = new GetTopDonor(api);
    const getLastFollower = new GetLastFollower(api);
    const getLastSubscriber = new GetLastSubscriber(api);

    const [topDonor, lastFollower, lastSubscriber] = await Promise.all([
        getTopDonor.execute(),
        getLastFollower.execute(),
        getLastSubscriber.execute(),
    ]);

    store.dispatch(setTopDonor(topDonor.username));
    store.dispatch(setLastFollow(lastFollower.username));
    store.dispatch(setLastSubscription(lastSubscriber.username));
}