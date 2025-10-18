import React, {SyntheticEvent} from "react";
import {
    FormControlLabel,
    RadioGroup,
    Radio,
    Typography
} from "@mui/material";
import {Accordion, AccordionSummary, AccordionDetails} from "@common/ui/components/SearchEngine/SearchEngine";
import {GenderFilter} from "@common/api/domain/Wod";

type Props = {
    idx: number;
    value?: GenderFilter;
    expanded: string | false;
    handleChange: (panel: string) => (_: SyntheticEvent, newExpanded: boolean) => void;
    onChange: (g: GenderFilter) => void;
};

export const GenderFilterAccordion = (
    {
        idx,
        value,
        expanded,
        handleChange,
        onChange,
    }: Props
) => (
    <Accordion
        expanded={expanded === `panel${idx}`}
        onChange={handleChange(`panel${idx}`)}
    >
        <AccordionSummary>
            <Typography component="span">Genre</Typography>
        </AccordionSummary>
        <AccordionDetails>
            <RadioGroup value={value ?? ""} onChange={e => onChange(e.target.value as GenderFilter)}>
                <FormControlLabel value={GenderFilter.Male} control={<Radio/>} label={GenderFilter.Male}/>
                <FormControlLabel value={GenderFilter.Female} control={<Radio/>} label={GenderFilter.Female}/>
            </RadioGroup>
        </AccordionDetails>
    </Accordion>
);
