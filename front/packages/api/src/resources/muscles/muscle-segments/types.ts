export type MuscleSegmentSummary = {
    id: string;
    slug: string;
    title: string;
    wodCount?: number;
};

export type MuscleSegment = {
    id: string;
    slug: string;
    title: string;
    summary: string;
    details: string | null;
    muscle: {
        id: string;
        slug: string;
        title: string;
    };
};
