export interface Muscle {
    id: number;
    slug: string;
    name: string;
    summary: string;
    details: string;
    area: MuscleArea;
    group?: MuscleGroup;
    movements: string[];
}

export interface MuscleArea {
    id: number;
    slug: string;
    name: string;
    summary: string;
    details: string;
}

export interface MuscleGroup {
    id: number;
    slug: string;
    name: string;
    summary: string;
    details: string;
}

// export type Tree = {
//     [areaId: number]: {
//         id: number;
//         slug: string;
//         name: string;
//         summary: string;
//         details: string;
//         groups: {
//             [groupId: number]: {
//                 id: number;
//                 slug: string;
//                 name: string;
//                 summary: string;
//                 details: string;
//                 muscles: Muscle[];
//             };
//         };
//     };
// };

export interface Tree {
    [areaId: number]: MuscleArea & {
        groups: {
            [groupId: number]: MuscleGroup & {
                muscles: Muscle[];
            };
        };
    };
}
