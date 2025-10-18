import React, {SyntheticEvent, useEffect, useState} from "react";
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
import {useListFilter} from "@common/ui/components/SearchEngine/hooks/useListFilter";
import {MovementService} from "@common/api/application";

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

type State = "neutral" | "include" | "exclude";

const computeState = (id: number, selected: number[], excluded: number[]): State => {
    if (selected.includes(id)) return "include";
    if (excluded.includes(id)) return "exclude";
    return "neutral";
};

export const MovementFilterAccordion = ({idx, expanded, filter, handleChange}: Props) => {
    const [items, setItems] = useState<{ id: number; name: string }[]>([]);
    const [loading, setLoading] = useState(true);

    const toggleItem = (id: number) => {
        if (filter.selected.includes(id)) {
            filter.setSelected(prev => prev.filter(x => x !== id));
            filter.setExcluded(prev => [...prev, id]);
        } else if (filter.excluded.includes(id)) {
            filter.setExcluded(prev => prev.filter(x => x !== id));
        } else {
            filter.setSelected(prev => [...prev, id]);
        }
    };

    useEffect(() => {
        async function load() {
            setLoading(true);
            try {
                const {data} = await new MovementService().fetchMovements({pagination: false});
                setItems(data);
            } finally {
                setLoading(false);
            }
        }

        load();
    }, []);

    return (
        <Accordion expanded={expanded === `panel${idx}`} onChange={handleChange(`panel${idx}`)}>
            <AccordionSummary>
                <Typography component="span">{filter.title}</Typography>
            </AccordionSummary>
            <AccordionDetails>
                {loading ? (
                    <Box display="flex" justifyContent="center"><CircularProgress/></Box>
                ) : (
                    <>
                        <TextField
                            fullWidth
                            size="small"
                            placeholder={`Rechercher ${filter.title.toLowerCase()}...`}
                            value={filter.search}
                            onChange={e => filter.setSearch(e.target.value)}
                            sx={{mb: 1}}
                        />
                        <Box sx={{maxHeight: 200, overflowY: 'auto'}}>
                            <FormGroup>
                                {items
                                    .filter(item => item.name.toLowerCase().includes(filter.search?.toLowerCase() || ""))
                                    .map(item => {
                                        const state = computeState(item.id, filter.selected, filter.excluded);
                                        return (
                                            <FormControlLabel
                                                key={item.id}
                                                control={
                                                    <Checkbox
                                                        sx={{
                                                            py: 0.5,
                                                            color: state === "exclude" ? "error.main" : undefined,
                                                            "&.Mui-checked": {color: "success.main"},
                                                            "&.MuiCheckbox-indeterminate": {color: "error.main"}
                                                        }}
                                                        checked={state === "include"}
                                                        indeterminate={state === "exclude"}
                                                        onClick={() => toggleItem(item.id)}
                                                    />
                                                }
                                                label={
                                                    <Typography
                                                        sx={{
                                                            textDecoration: state === "exclude" ? "line-through" : "none",
                                                            color: state === "exclude" ? "error.main" : "inherit"
                                                        }}
                                                    >
                                                        {item.name}
                                                    </Typography>
                                                }
                                            />
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
