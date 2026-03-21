import { useMutation } from "../../hooks/useMutation"
import type { CreateWodScorePayload } from "./types"

export function useCreateWodScore() {
    return useMutation<{ id: string }, CreateWodScorePayload>("/wod-scores", "POST")
}
