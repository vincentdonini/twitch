import {AppDispatch} from "@/common/stores/stores";
import {addSound, SoundEvent} from "@modules/tmi/application/store/soundQueueSlice";

export function queueSound(dispatch: AppDispatch, sound: SoundEvent) {
    dispatch(addSound(sound));
}
