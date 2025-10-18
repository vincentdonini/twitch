import React, {SyntheticEvent} from "react";
import {Typography, Stack, Slider, Box, TextField} from "@mui/material";
import {Accordion, AccordionSummary, AccordionDetails} from "@common/ui/components/SearchEngine/SearchEngine";

type Props = {
    idx: number;
    value: [number | undefined, number | undefined];
    expanded: string | false;
    handleChange: (panel: string) => (_: SyntheticEvent, newExpanded: boolean) => void;
    onChange: (newValue: [number | undefined, number | undefined]) => void;
    maxValue?: number;
};

export const TimeCapFilterAccordion = (
    {
        idx,
        value,
        expanded,
        handleChange,
        onChange,
        maxValue = 100
    }: Props
) => {
    const minDistance = 1;

    // Slider doit toujours avoir des nombres
    const sliderValue: [number, number] = [
        value[0] ?? 0,
        value[1] ?? maxValue
    ];

    const handleSliderChange = (_: Event, newValue: number[], activeThumb: number) => {
        let [min, max] = newValue as [number, number];

        // Respecter la distance minimale
        if (max - min < minDistance) {
            if (activeThumb === 0) {
                min = Math.min(min, maxValue - minDistance);
                max = min + minDistance;
            } else {
                max = Math.max(max, minDistance);
                min = max - minDistance;
            }
        }

        // Convertir en undefined si borne extrême
        const newMin = min === 0 ? undefined : min;
        const newMax = max === maxValue ? undefined : max;

        onChange([newMin, newMax]);
    };

    const handleInputChange = (index: 0 | 1, inputValue: number | undefined) => {
        const newValue: [number | undefined, number | undefined] = [...value];
        newValue[index] = inputValue;
        onChange(newValue);
    };

    return (
        <Accordion
            expanded={expanded === `panel${idx}`}
            onChange={handleChange(`panel${idx}`)}
        >
            <AccordionSummary>
                <Typography component="span">Time Cap (en minutes)</Typography>
            </AccordionSummary>
            <AccordionDetails>
                <Stack gap={1}>
                    <Slider
                        value={sliderValue}
                        valueLabelDisplay="auto"
                        valueLabelFormat={(val, index) => {
                            if ((index === 0 && value[0] === undefined) || (index === 1 && value[1] === undefined)) {
                                return "";
                            }
                            return val.toString();
                        }}
                        onChange={handleSliderChange}
                        disableSwap
                        min={0}
                        max={maxValue}
                    />
                    <Box sx={{display: 'flex', flexDirection: 'row', gap: 2}}>
                        <TextField
                            label="Min"
                            type="number"
                            value={value[0] === undefined || value[0] === 0 ? "" : value[0]}
                            onChange={e => {
                                const val = e.target.value === "" ? undefined : Number(e.target.value);
                                handleInputChange(0, val);
                            }}
                        />
                        <TextField
                            label="Max"
                            type="number"
                            value={value[1] === undefined || value[1] === maxValue ? "" : value[1]}
                            onChange={e => {
                                const val = e.target.value === "" ? undefined : Number(e.target.value);
                                handleInputChange(1, val);
                            }}
                        />
                    </Box>
                </Stack>
            </AccordionDetails>
        </Accordion>
    );
};
