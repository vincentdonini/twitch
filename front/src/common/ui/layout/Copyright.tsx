import * as React from 'react';
import {Box, Link, Stack, Typography} from '@mui/material';

export default function Copyright(props: any) {
    return (
        <Box
            component="footer"
            sx={{
                borderTop: "1px solid #ddd",
                py: 1,
                textAlign: "center",
            }}
        >
            <Typography
                variant="overline"
                sx={{
                    color: 'text.secondary',
                }}
                {...props}
            >
                <Stack
                    direction="row"
                    justifyContent="center"
                    alignItems="center"
                    spacing={0.3}
                >
                    <div>{'Copyright'}</div>
                    <div>{'©'}</div>
                    <div>{new Date().getFullYear()}</div>
                    <Link underline="none" color="inherit" href="https://www.woder.fr/">
                        WODER
                    </Link>
                    <div>{'Tous droits réservés.'}</div>
                </Stack>
            </Typography>
        </Box>
    );
}
