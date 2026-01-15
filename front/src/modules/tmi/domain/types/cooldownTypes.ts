export type CooldownMap = Record<string, number>;

export type CooldownType = "user" | "command";

export interface CooldownCheck {
    key: string;
    duration: number | false;
    map: CooldownMap;
    type: CooldownType;
}

export interface CooldownMapsStructure {
    userCooldowns: CooldownMap;
    commandCooldowns: CooldownMap;
}
