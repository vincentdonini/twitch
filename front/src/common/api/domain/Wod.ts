export interface Wod {
    id: number;
    title: string;
    description: string;
    wodType: WodType;
}

export interface WodType {
    id: number;
    slug: string;
    name: string;
    description: string;
}

export interface WodVersionType {
    id: number;
    slug: string;
    name: string;
    description: string;
}

export interface WodCategory {
    id: number;
    slug: string;
    name: string;
    description: string;
}

export enum GenderFilter {
    Male = "male",
    Female = "female",
}

export type TimeCapFilter = {
    gte?: number;
    lte?: number;
    lt?: number;
    gt?: number;
    between?: [number, number];
};

export type ListFilter = {
    in?: number[];
    notIn?: number[];
};

export type Filters = {
    search?: string;
    wodCategory?: number[];
    wodType?: number[];
    wodVersionType?: number[];
    movements?: ListFilter;
    equipments?: ListFilter;
    muscles?: ListFilter;
    gender?: GenderFilter;
    timeCap?: TimeCapFilter;
};

export type WodParams = Filters & {
    page: number;
    itemsPerPage: number;
    "wodVersions.wodVersionVariants.timeCap"?: TimeCapFilter;
};