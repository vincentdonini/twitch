import React, {useState, useEffect, SyntheticEvent, useCallback} from "react";
import {styled} from "@mui/material/styles";
import {Card, Button} from "@mui/material";
import MuiAccordion, {AccordionProps} from "@mui/material/Accordion";
import MuiAccordionSummary, {AccordionSummaryProps, accordionSummaryClasses} from "@mui/material/AccordionSummary";
import MuiAccordionDetails from "@mui/material/AccordionDetails";
import ArrowForwardIosSharpIcon from "@mui/icons-material/ArrowForwardIosSharp";
import {
    useListFilter,
    ListFilterAccordion,
    GenderFilterAccordion, Gender,
    MovementFilterAccordion,
    EquipmentFilterAccordion,
    MuscleFilterAccordion,
    TimeCapFilterAccordion,
} from "@common/ui/components/SearchEngine";
import {
    WodCategoryService,
    WodTypeService,
    WodVersionTypeService,
    MovementService,
    EquipmentService,
    MuscleService,
} from "@common/api/application";

export const Accordion = styled((props: AccordionProps) => (
    <MuiAccordion disableGutters elevation={0} square {...props} />
))(({theme}) => ({
    "&:not(:first-of-type)": {borderTop: `1px solid ${theme.palette.divider}`},
    "&::before": {display: "none"},
}));

export const AccordionSummary = styled((props: AccordionSummaryProps) => (
    <MuiAccordionSummary expandIcon={<ArrowForwardIosSharpIcon sx={{fontSize: "0.9rem"}}/>} {...props} />
))(({theme}) => ({
    backgroundColor: "rgba(0, 0, 0, .03)",
    flexDirection: "row-reverse",
    [`& .${accordionSummaryClasses.expandIconWrapper}.${accordionSummaryClasses.expanded}`]: {
        transform: "rotate(90deg)",
    },
    [`& .${accordionSummaryClasses.content}`]: {
        marginLeft: theme.spacing(1),
    },
}));

export const AccordionDetails = styled(MuiAccordionDetails)(({theme}) => ({
    padding: theme.spacing(2),
    borderTop: "1px solid rgba(0, 0, 0, .125)",
}));

export type Filters = {
    wodCategory?: number[];
    wodType?: number[];
    wodVersionType?: number[];
    movements?: { in?: number[]; notIn?: number[] };
    equipments?: { in?: number[]; notIn?: number[] };
    muscles?: { in?: number[]; notIn?: number[] };
    gender?: Gender;
    timeCap?: { gte?: number; lt?: number };
};

type Props = {
    onSearch?: (filters: Partial<Filters>) => Promise<void>;
    loading?: boolean;
    initialValues?: Partial<Filters>;
};

export default function SearchEngine({onSearch, loading, initialValues}: Props) {
    const [expanded, setExpanded] = useState<string | false>("panel0");
    const [selectedGender, setSelectedGender] = useState<Gender | undefined>();
    const [sliderValue, setSliderValue] = useState<[number | undefined, number | undefined]>([0, 100]);

    const handleChange = (panel: string) => (_: SyntheticEvent, newExpanded: boolean) => setExpanded(newExpanded ? panel : false);

    /* --- TimeCap Initialization --- */
    useEffect(() => {
        if (!initialValues?.timeCap) return;
        const min = initialValues.timeCap.gte ?? 0;
        const max = initialValues.timeCap.lt ?? 100;
        setSliderValue([min / 60, max / 60]);
    }, [initialValues?.timeCap]);

    /* --- List Fetchers --- */
    const wodCategoryFetcher = useCallback(
        async () => {
            const {data} = await new WodCategoryService().fetchWodCategories();
            return data;
        },
        []
    );

    const wodTypeFetcher = useCallback(
        async () => {
            const {data} = await new WodTypeService().fetchWodTypes();
            return data;
        },
        []
    );

    const wodVersionFetcher = useCallback(
        async () => {
            const {data} = await new WodVersionTypeService().fetchWodVersionTypes();
            return data;
        },
        []
    );

    const equipmentsFetcher = useCallback(
        async () => {
            const {data} = await new EquipmentService().fetchEquipments({pagination: false});
            return data;
        },
        []
    );

    const movementsFetcher = useCallback(async () => {
        const {data} = await new MovementService().fetchMovements({pagination: false});
        return data;
    }, []);

    const musclesFetcher = useCallback(async () => {
        const {data} = await new MuscleService().fetchMuscles({pagination: false});
        return data;
    }, []);

    /* --- List Filters --- */
    const wodCategoryFilter = useListFilter(wodCategoryFetcher, initialValues?.wodCategory || []);
    const wodTypeFilter = useListFilter(wodTypeFetcher, initialValues?.wodType || []);
    const wodVersionFilter = useListFilter(wodVersionFetcher, initialValues?.wodVersionType || []);
    const movementsFilter = useListFilter(movementsFetcher, initialValues?.movements?.in || [], initialValues?.movements?.notIn || []);
    const equipmentsFilter = useListFilter(equipmentsFetcher, initialValues?.equipments?.in || [], initialValues?.equipments?.notIn || []);
    const musclesFilter = useListFilter(musclesFetcher, initialValues?.muscles?.in || [], initialValues?.muscles?.notIn || []);

    const handleSubmit = () => {
        const search = {
            wodCategory: wodCategoryFilter.selected.length ? wodCategoryFilter.selected : undefined,
            wodType: wodTypeFilter.selected.length ? wodTypeFilter.selected : undefined,
            wodVersionType: wodVersionFilter.selected.length ? wodVersionFilter.selected : undefined,
            movements: (movementsFilter.selected.length || movementsFilter.excluded.length)
                ? {in: movementsFilter.selected, notIn: movementsFilter.excluded}
                : undefined,
            equipments: (equipmentsFilter.selected.length || equipmentsFilter.excluded.length)
                ? {in: equipmentsFilter.selected, notIn: equipmentsFilter.excluded}
                : undefined,
            muscles: (musclesFilter.selected.length || musclesFilter.excluded.length)
                ? {in: musclesFilter.selected, notIn: musclesFilter.excluded}
                : undefined,
            gender: selectedGender,
            // timeCap: {gte: sliderValue[0]! * 60, lt: sliderValue[1]! * 60}
        }
        onSearch?.(search);
    };

    return (
        <>
            <Card variant="outlined">
                <Card variant="outlined">
                    <ListFilterAccordion
                        idx={0}
                        filter={{...wodCategoryFilter, title: "Catégorie WOD"}}
                        expanded={expanded}
                        handleChange={handleChange}
                    />
                    <ListFilterAccordion
                        idx={1}
                        filter={{...wodTypeFilter, title: "Type de WOD"}}
                        expanded={expanded}
                        handleChange={handleChange}
                    />
                    <ListFilterAccordion
                        idx={2}
                        filter={{...wodVersionFilter, title: "Version Type de WOD"}}
                        expanded={expanded}
                        handleChange={handleChange}
                    />

                    {/* Specific filters */}
                    <MovementFilterAccordion
                        idx={3}
                        filter={{...movementsFilter, title: "Mouvements"}}
                        expanded={expanded}
                        handleChange={handleChange}
                    />

                    <EquipmentFilterAccordion
                        idx={4}
                        filter={{...equipmentsFilter, title: "Équipements"}}
                        expanded={expanded}
                        handleChange={handleChange}
                    />

                    <MuscleFilterAccordion
                        idx={5}
                        filter={{...musclesFilter, title: "Muscles"}}
                        expanded={expanded}
                        handleChange={handleChange}
                    />

                    <GenderFilterAccordion
                        value={selectedGender}
                        idx={6}
                        expanded={expanded}
                        handleChange={handleChange}
                        onChange={setSelectedGender}
                    />

                    <TimeCapFilterAccordion
                        value={sliderValue}
                        idx={7}
                        expanded={expanded}
                        handleChange={handleChange}
                        onChange={setSliderValue}
                    />
                </Card>
            </Card>
            <Button variant="contained" disableElevation loading={loading} onClick={handleSubmit}
                    sx={{mt: 2, width: "100%"}}>
                Search
            </Button>
        </>
    );
}
