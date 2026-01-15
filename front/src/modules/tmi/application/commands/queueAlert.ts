import {AppDispatch} from "@/common/stores/stores";
import {addAlert, AlertEvent} from "@modules/tmi/application/store/alertQueueSlice";

export function queueAlert(dispatch: AppDispatch, alert: AlertEvent) {
    dispatch(addAlert(alert));
}
