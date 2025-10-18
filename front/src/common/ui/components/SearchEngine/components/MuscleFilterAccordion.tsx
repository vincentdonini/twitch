import React, { SyntheticEvent, useEffect, useState } from "react";
import {
    Accordion,
    AccordionSummary,
    AccordionDetails
} from "@common/ui/components/SearchEngine/SearchEngine";
import {
    Box,
    Checkbox,
    CircularProgress,
    FormGroup,
    FormControlLabel,
    TextField,
    Typography
} from "@mui/material";
import { useListFilter } from "@common/ui/components/SearchEngine/hooks/useListFilter";
import { Tree, Muscle } from "@common/api/domain/Muscle";
import { MuscleService } from "@common/api/application";

type Props = {
    idx: number;
    expanded: string | false;
    filter: ReturnType<typeof useListFilter> & {
        title: string;
        excluded: number[];
        setExcluded: React.Dispatch<React.SetStateAction<number[]>>;
    };
    handleChange: (panel: string) => (_: SyntheticEvent, newExpanded: boolean) => void;
};

type State = "neutral" | "include" | "exclude" | "mixed";

// 🔎 calcule l’état d’un groupe/area en fonction de ses muscles
const computeState = (muscles: Muscle[], selected: number[], excluded: number[]): State => {
    const total = muscles.length;
    if (total === 0) return "neutral";

    const included = muscles.filter(m => selected.includes(m.id)).length;
    const ex = muscles.filter(m => excluded.includes(m.id)).length;

    if (included === total) return "include";
    if (ex === total) return "exclude";
    if (included === 0 && ex === 0) return "neutral";
    return "mixed";
};

export const MuscleFilterAccordion = ({ idx, expanded, filter, handleChange }: Props) => {
    const [tree, setTree] = useState<Tree | null>(null);
    const [loading, setLoading] = useState(true);

    // --- toggle individuel muscle
    const toggleItem = (id: number) => {
        if (filter.selected.includes(id)) {
            // include -> exclude
            filter.setSelected(prev => prev.filter(x => x !== id));
            filter.setExcluded(prev => [...prev, id]);
        } else if (filter.excluded.includes(id)) {
            // exclude -> neutral
            filter.setExcluded(prev => prev.filter(x => x !== id));
        } else {
            // neutral -> include
            filter.setSelected(prev => [...prev, id]);
        }
    };

    // --- appliquer un état à tout un groupe
    const setGroupState = (muscles: Muscle[], mode: State) => {
        if (mode === "include") {
            filter.setSelected(prev => [...new Set([...prev, ...muscles.map(m => m.id)])]);
            filter.setExcluded(prev => prev.filter(id => !muscles.some(m => m.id === id)));
        } else if (mode === "exclude") {
            filter.setExcluded(prev => [...new Set([...prev, ...muscles.map(m => m.id)])]);
            filter.setSelected(prev => prev.filter(id => !muscles.some(m => m.id === id)));
        } else if (mode === "neutral") {
            filter.setSelected(prev => prev.filter(id => !muscles.some(m => m.id === id)));
            filter.setExcluded(prev => prev.filter(id => !muscles.some(m => m.id === id)));
        }
    };

    const setAreaState = (groups: { muscles: Muscle[] }[], mode: State) => {
        const allMuscles = groups.flatMap(g => g.muscles);
        setGroupState(allMuscles, mode);
    };

    useEffect(() => {
        async function load() {
            try {
                setLoading(true);

                const { data } = await new MuscleService().fetchMuscles({ pagination: false });
                const organized = MuscleService.organizeMuscles(data);
                setTree(organized);
            } finally {
                setLoading(false);
            }
        }
        load();
    }, []);

    // --- 🔎 Filtrage
    const normalize = (s: string) => s.toLowerCase().normalize("NFD").replace(/\p{Diacritic}/gu, "");
    const search = normalize(filter.search || "");

    const filterTree = (tree: Tree): Tree => {
        if (!search) return tree;

        const result: Tree = {};
        for (const area of Object.values(tree)) {
            const areaMatch = normalize(area.name).includes(search);
            const groups: typeof area.groups = {};

            for (const group of Object.values(area.groups)) {
                const groupMatch = normalize(group.name).includes(search);
                const muscles = group.muscles.filter(m => normalize(m.name).includes(search));

                if (areaMatch || groupMatch) {
                    groups[group.id] = { ...group };
                } else if (muscles.length > 0) {
                    groups[group.id] = { ...group, muscles };
                }
            }

            if (areaMatch || Object.keys(groups).length > 0) {
                result[area.id] = { ...area, groups };
            }
        }
        return result;
    };

    const filteredTree = tree ? filterTree(tree) : null;

    return (
        <Accordion
            expanded={expanded === `panel${idx}`}
            onChange={handleChange(`panel${idx}`)}
        >
            <AccordionSummary>
                <Typography component="span">{filter.title}</Typography>
            </AccordionSummary>
            <AccordionDetails>
                {loading || !filteredTree ? (
                    <Box display="flex" justifyContent="center">
                        <CircularProgress />
                    </Box>
                ) : (
                    <>
                        <TextField
                            fullWidth
                            size="small"
                            placeholder={`Rechercher ${filter.title.toLowerCase()}...`}
                            value={filter.search}
                            onChange={e => filter.setSearch(e.target.value)}
                            sx={{ mb: 1 }}
                        />

                        <Box sx={{maxHeight: 200, overflowY: 'auto'}}>
                            <FormGroup>
                                {Object.values(filteredTree).map(area => {
                                    const areaState = computeState(
                                        Object.values(area.groups).flatMap(g => g.muscles),
                                        filter.selected,
                                        filter.excluded
                                    );

                                    return (
                                        <Box key={area.id} sx={{ mb: 0 }}>

                                            {/* --- Area --- */}
                                            <FormControlLabel
                                                sx={{ width: "100%" }}
                                                control={
                                                    <Checkbox
                                                        sx={{
                                                            py: 0.5,
                                                            "&.Mui-checked": { color: "success.main" },
                                                            "&.MuiCheckbox-indeterminate": { color: areaState === "exclude" ? "error.main" : "text.primary" }
                                                        }}
                                                        checked={areaState === "include"}
                                                        indeterminate={areaState === "mixed" || areaState === "exclude"}
                                                        onClick={() => {
                                                            if (areaState === "neutral") setAreaState(Object.values(area.groups), "include");
                                                            else if (areaState === "include") setAreaState(Object.values(area.groups), "exclude");
                                                            else setAreaState(Object.values(area.groups), "neutral");
                                                        }}
                                                    />
                                                }
                                                label={
                                                    <Typography
                                                        variant="subtitle1"
                                                        sx={{
                                                            textDecoration: areaState === "exclude" ? "line-through" : "none",
                                                            color: areaState === "exclude" ? "error.main" : "inherit"
                                                        }}
                                                    >
                                                        {area.name} {areaState}
                                                    </Typography>
                                                }
                                            />

                                            {/* --- Groups --- */}
                                            {Object.values(area.groups).map(group => {
                                                const groupState = computeState(group.muscles, filter.selected, filter.excluded);

                                                if (group.slug === "ungrouped") {
                                                    return (
                                                        <Box key={group.id} sx={{ pl: 2 }}>
                                                            <FormGroup>
                                                                {group.muscles.map(muscle => {
                                                                    const muscleState = filter.selected.includes(muscle.id)
                                                                        ? "include"
                                                                        : filter.excluded.includes(muscle.id)
                                                                            ? "exclude"
                                                                            : "neutral";

                                                                    return (
                                                                        <FormControlLabel
                                                                            key={muscle.id}
                                                                            control={
                                                                                <Checkbox
                                                                                    sx={{
                                                                                        py: 0.5,
                                                                                        color: muscleState === "exclude" ? "error.main" : undefined,
                                                                                        "&.Mui-checked": { color: "success.main" },
                                                                                        "&.MuiCheckbox-indeterminate": { color: "error.main" }
                                                                                    }}
                                                                                    checked={muscleState === "include"}
                                                                                    indeterminate={muscleState === "exclude"}
                                                                                    onClick={() => toggleItem(muscle.id)}
                                                                                />
                                                                            }
                                                                            label={
                                                                                <Typography
                                                                                    variant="body1"
                                                                                    sx={{
                                                                                        textDecoration: muscleState === "exclude" ? "line-through" : "none",
                                                                                        color: muscleState === "exclude" ? "error.main" : "inherit"
                                                                                    }}
                                                                                >
                                                                                    {muscle.name}
                                                                                </Typography>
                                                                            }
                                                                        />
                                                                    );
                                                                })}
                                                            </FormGroup>
                                                        </Box>
                                                    );
                                                }

                                                return (
                                                    <Box key={group.id} sx={{ pl: 2 }}>
                                                        <FormControlLabel
                                                            sx={{ width: "100%" }}
                                                            control={
                                                                <Checkbox
                                                                    sx={{
                                                                        py: 0.5,
                                                                        "&.Mui-checked": { color: "success.main" },
                                                                        "&.MuiCheckbox-indeterminate": { color: groupState === "exclude" ? "error.main" : "text.primary" }
                                                                    }}
                                                                    checked={groupState === "include"}
                                                                    indeterminate={groupState === "mixed" || groupState === "exclude"}
                                                                    onClick={() => {
                                                                        if (groupState === "neutral") setGroupState(group.muscles, "include");
                                                                        else if (groupState === "include") setGroupState(group.muscles, "exclude");
                                                                        else setGroupState(group.muscles, "neutral");
                                                                    }}
                                                                />
                                                            }
                                                            label={
                                                                <Typography
                                                                    variant="subtitle1"
                                                                    sx={{
                                                                        textDecoration: groupState === "exclude" ? "line-through" : "none",
                                                                        color: groupState === "exclude" ? "error.main" : "inherit"
                                                                    }}
                                                                >
                                                                    {group.name} {groupState}
                                                                </Typography>
                                                            }
                                                        />
                                                        <FormGroup sx={{ pl: 2 }}>
                                                            {group.muscles.map(muscle => {
                                                                const muscleState = filter.selected.includes(muscle.id)
                                                                    ? "include"
                                                                    : filter.excluded.includes(muscle.id)
                                                                        ? "exclude"
                                                                        : "neutral";

                                                                return (
                                                                    <FormControlLabel
                                                                        key={muscle.id}
                                                                        control={
                                                                            <Checkbox
                                                                                sx={{
                                                                                    py: 0.5,
                                                                                    color: muscleState === "exclude" ? "error.main" : undefined,
                                                                                    "&.Mui-checked": { color: "success.main" },
                                                                                    "&.MuiCheckbox-indeterminate": { color: "error.main" }
                                                                                }}
                                                                                checked={muscleState === "include"}
                                                                                indeterminate={muscleState === "exclude"}
                                                                                onClick={() => toggleItem(muscle.id)}
                                                                            />
                                                                        }
                                                                        label={
                                                                            <Typography
                                                                                variant="body1"
                                                                                sx={{
                                                                                    textDecoration: muscleState === "exclude" ? "line-through" : "none",
                                                                                    color: muscleState === "exclude" ? "error.main" : "inherit"
                                                                                }}
                                                                            >
                                                                                {muscle.name}
                                                                            </Typography>
                                                                        }
                                                                    />
                                                                );
                                                            })}
                                                        </FormGroup>
                                                    </Box>
                                                );
                                            })}
                                        </Box>
                                    );
                                })}
                            </FormGroup>
                        </Box>
                    </>
                )}
            </AccordionDetails>
        </Accordion>
    );
};
