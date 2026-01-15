
export interface ITwitchService {
    getDonors(): Promise<any>;
    getTopDonor(): Promise<any>;
    getFollowers(): Promise<any>;
    getLastFollower(): Promise<any>;
    getSubscribers(): Promise<any>;
    getLastSubscriber(): Promise<any>;
}
