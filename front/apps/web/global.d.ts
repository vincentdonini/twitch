import en from "./messages/en.json"
import uiEn from "@workspace/ui/messages/en.json"

type Messages = typeof en & { ui: typeof uiEn }

declare global {
  interface IntlMessages extends Messages {}
}
