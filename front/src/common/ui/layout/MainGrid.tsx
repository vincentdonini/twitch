import * as React from 'react';
import Box from '@mui/material/Box';
import {ReactNode} from "react";

// const data: StatCardProps[] = [
//     {
//         title: 'Users',
//         value: '14k',
//         interval: 'Last 30 days',
//         trend: 'up',
//         data: [
//             200, 24, 220, 260, 240, 380, 100, 240, 280, 240, 300, 340, 320, 360, 340, 380,
//             360, 400, 380, 420, 400, 640, 340, 460, 440, 480, 460, 600, 880, 920,
//         ],
//     },
//     {
//         title: 'Conversions',
//         value: '325',
//         interval: 'Last 30 days',
//         trend: 'down',
//         data: [
//             1640, 1250, 970, 1130, 1050, 900, 720, 1080, 900, 450, 920, 820, 840, 600, 820,
//             780, 800, 760, 380, 740, 660, 620, 840, 500, 520, 480, 400, 360, 300, 220,
//         ],
//     },
//     {
//         title: 'Event count',
//         value: '200k',
//         interval: 'Last 30 days',
//         trend: 'neutral',
//         data: [
//             500, 400, 510, 530, 520, 600, 530, 520, 510, 730, 520, 510, 530, 620, 510, 530,
//             520, 410, 530, 520, 610, 530, 520, 610, 530, 420, 510, 430, 520, 510,
//         ],
//     },
// ];

interface MainGridProps {
    children: ReactNode;
}

export default function MainGrid(
    {
        children
    }: MainGridProps
) {
    return (
        <Box
            sx={{
                display: 'flex',
                flexDirection: 'column',
                alignContent: 'stretch',
                justifyContent: 'stretch',
                flexGrow: 1,
                overflowY: "auto",
                maxWidth: { sm: '100%', md: '1700px' },
                px: 3,
                py: 2,
            }}
        >
            {children}

            {/*<Box*/}
            {/*    sx={{*/}
            {/*        flex: 1, // occupe tout l'espace restant*/}
            {/*        display: "flex",*/}
            {/*        flexDirection: "column",*/}
            {/*        gap: 2,*/}
            {/*    }}*/}
            {/*>*/}
            {/*    <Grid*/}
            {/*        container*/}
            {/*        spacing={2}*/}
            {/*        sx={{*/}
            {/*            border: "1px solid orange",*/}
            {/*        }}*/}
            {/*    >*/}
            {/*        <Grid*/}
            {/*            container*/}
            {/*            spacing={2}*/}
            {/*            sx={{*/}
            {/*                border: "1px solid red",*/}
            {/*            }}*/}
            {/*        >*/}
            {/*            <Typography component="h2" variant="h6" sx={{ mb: 2 }}>*/}
            {/*                Overview*/}
            {/*            </Typography>*/}
            {/*            <Grid*/}
            {/*                container*/}
            {/*                spacing={2}*/}
            {/*                columns={12}*/}
            {/*                sx={{ mb: (theme) => theme.spacing(2) }}*/}
            {/*            >*/}
            {/*                /!*{data.map((card, index) => (*!/*/}
            {/*                /!*    <Grid key={index} size={{ xs: 12, sm: 6, lg: 3 }}>*!/*/}
            {/*                /!*        <StatCard {...card} />*!/*/}
            {/*                /!*    </Grid>*!/*/}
            {/*                /!*))}*!/*/}
            {/*                <Grid size={{ xs: 12, sm: 6, lg: 3 }}>*/}
            {/*                    HighlightedCard*/}
            {/*                </Grid>*/}
            {/*                <Grid size={{ xs: 12, md: 6 }}>*/}
            {/*                    SessionsChart*/}
            {/*                </Grid>*/}
            {/*                <Grid size={{ xs: 12, md: 6 }}>*/}
            {/*                    PageViewsBarChart*/}
            {/*                </Grid>*/}
            {/*            </Grid>*/}
            {/*        </Grid>*/}

            {/*        <Grid*/}
            {/*            container*/}
            {/*            spacing={2}*/}
            {/*            sx={{*/}
            {/*                border: "1px solid red",*/}
            {/*            }}*/}
            {/*        >*/}
            {/*            <Typography component="h2" variant="h6" sx={{ mb: 2 }}>*/}
            {/*            Details*/}
            {/*        </Typography>*/}
            {/*            <Grid container spacing={2} columns={12}>*/}
            {/*                <Grid size={{ xs: 12, lg: 9 }}>*/}
            {/*                    CustomizedDataGrid*/}
            {/*                </Grid>*/}
            {/*                <Grid size={{ xs: 12, lg: 3 }}>*/}
            {/*                    <Stack gap={2} direction={{ xs: 'column', sm: 'row', lg: 'column' }}>*/}
            {/*                        <div>CustomizedTreeView</div>*/}
            {/*                        <div>ChartUserByCountry</div>*/}
            {/*                    </Stack>*/}
            {/*                </Grid>*/}
            {/*            </Grid>*/}
            {/*        </Grid>*/}

            {/*    </Grid>*/}
            {/*</Box>*/}
            {/*<Box sx={{ mt: "auto" }}>*/}
            {/*    */}
            {/*</Box>*/}
        </Box>
    );
};
