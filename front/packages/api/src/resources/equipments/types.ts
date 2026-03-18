export type Equipment = {
    id: string;
    slug: string;
    title: string;
    summary: string;
    details: string;
    wodCount: number;
};

export type EquipmentSummary = {
    id: string;
    slug: string;
    title: string;
};

export type CreateEquipmentPayload = {
    slug: string;
    title: string;
    summary: string;
    details: string;
};

export type UpdateEquipmentPayload = CreateEquipmentPayload;
