import React, {SyntheticEvent, useMemo} from "react";
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
import {Accordion, AccordionSummary, AccordionDetails} from "@common/ui/components/SearchEngine/SearchEngine";

type Props = {
    filter: ReturnType<typeof useListFilter> & { title: string };
    idx: number;
    expanded: string | false;
    handleChange: (panel: string) => (_: SyntheticEvent, newExpanded: boolean) => void;
};

export const ListFilterAccordion = ({filter, idx, expanded, handleChange}: Props) => {
    const filteredItems = useMemo(() =>
            filter.items.filter(i => i.name.toLowerCase().includes(filter.search.toLowerCase())),
        [filter.items, filter.search]
    );

    const toggleItem = (id: number) => {
        filter.setSelected(prev => prev.includes(id) ? prev.filter(x => x !== id) : [...prev, id]);
    };


    return (
        <Accordion
            expanded={expanded === `panel${idx}`}
            onChange={handleChange(`panel${idx}`)}
        >
            <AccordionSummary>
                <Typography component="span">{filter.title}</Typography>
            </AccordionSummary>
            <AccordionDetails>
                {filter.loading ? (
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
                                {filteredItems.map(i => (
                                    <FormControlLabel
                                        key={i.id}
                                        control={
                                            <Checkbox
                                                checked={filter.selected.includes(i.id)}
                                                onChange={() => toggleItem(i.id)}
                                            />
                                        }
                                        label={i.name}
                                    />
                                ))}
                            </FormGroup>
                        </Box>
                    </>
                )}
            </AccordionDetails>
        </Accordion>
    );
};
