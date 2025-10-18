import * as React from 'react';
import {
    Box,
    Button,
    Container,
    Divider,
    IconButton,
    InputLabel,
    Link,
    Stack,
    TextField,
    Typography
} from "@mui/material";
import GitHubIcon from '@mui/icons-material/GitHub';
import LinkedInIcon from '@mui/icons-material/LinkedIn';
import TwitterIcon from '@mui/icons-material/X';

export default function Footer() {
    return (
        <>
            <React.Fragment>
                <Divider/>
                <Container
                    sx={{
                        display: 'flex',
                        flexDirection: 'column',
                        alignItems: 'center',
                        gap: {xs: 4, sm: 8},
                        py: {xs: 8, sm: 10},
                        textAlign: {sm: 'center', md: 'left'},
                    }}
                >
                    <Box
                        sx={{
                            display: 'flex',
                            flexDirection: {xs: 'column', sm: 'row'},
                            width: '100%',
                            justifyContent: 'space-between',
                        }}
                    >
                        <Box
                            sx={{
                                display: 'flex',
                                flexDirection: 'column',
                                gap: 4,
                                minWidth: {xs: '100%', sm: '60%'},
                            }}
                        >
                            <Box sx={{width: {xs: '100%', sm: '60%'}}}>
                                {/*<SitemarkIcon/>*/}
                                <Typography
                                    variant="body2"
                                    gutterBottom
                                    sx={{fontWeight: 600, mt: 2}}
                                >
                                    Join the newsletter
                                </Typography>
                                <Typography variant="body2" sx={{color: 'text.secondary', mb: 2}}>
                                    Subscribe for weekly updates. No spams ever!
                                </Typography>
                                <InputLabel htmlFor="email-newsletter">Email</InputLabel>
                                <Stack direction="row" spacing={1} useFlexGap>
                                    <TextField
                                        id="email-newsletter"
                                        hiddenLabel
                                        size="small"
                                        variant="outlined"
                                        fullWidth
                                        aria-label="Enter your email address"
                                        placeholder="Your email address"
                                        slotProps={{
                                            htmlInput: {
                                                autoComplete: 'off',
                                                'aria-label': 'Enter your email address',
                                            },
                                        }}
                                        sx={{width: '250px'}}
                                    />
                                    <Button
                                        variant="contained"
                                        color="primary"
                                        size="small"
                                        disableElevation
                                        sx={{flexShrink: 0}}
                                    >
                                        Subscribe
                                    </Button>
                                </Stack>
                            </Box>
                        </Box>
                        <Box
                            sx={{
                                display: {xs: 'none', sm: 'flex'},
                                flexDirection: 'column',
                                gap: 1,
                            }}
                        >
                            <Typography variant="subtitle1">
                                Product
                            </Typography>
                            <Link color="text.secondary" variant="body2" href="#">
                                Features
                            </Link>
                            <Link color="text.secondary" variant="body2" href="#">
                                Testimonials
                            </Link>
                            <Link color="text.secondary" variant="body2" href="#">
                                Highlights
                            </Link>
                            <Link color="text.secondary" variant="body2" href="#">
                                Pricing
                            </Link>
                            <Link color="text.secondary" variant="body2" href="#">
                                FAQs
                            </Link>
                        </Box>
                        <Box
                            sx={{
                                display: {xs: 'none', sm: 'flex'},
                                flexDirection: 'column',
                                gap: 1,
                            }}
                        >
                            <Typography variant="subtitle1">
                                Catalogue
                            </Typography>
                            <Link color="text.secondary" variant="body2" href="#">
                                Wods
                            </Link>
                            <Link color="text.secondary" variant="body2" href="#">
                                Equipments
                            </Link>
                            <Link color="text.secondary" variant="body2" href="#">
                                Muscle
                            </Link>
                        </Box>
                        <Box
                            sx={{
                                display: {xs: 'none', sm: 'flex'},
                                flexDirection: 'column',
                                gap: 1,
                            }}
                        >
                            <Typography variant="subtitle1">
                                Company
                            </Typography>
                            <Link color="text.secondary" variant="body2" href="#">
                                About us
                            </Link>
                            <Link color="text.secondary" variant="body2" href="#">
                                Careers
                            </Link>
                            <Link color="text.secondary" variant="body2" href="#">
                                Press
                            </Link>
                        </Box>
                        <Box
                            sx={{
                                display: {xs: 'none', sm: 'flex'},
                                flexDirection: 'column',
                                gap: 1,
                            }}
                        >
                            <Typography variant="subtitle1">
                                Legal
                            </Typography>
                            <Link color="text.secondary" variant="body2" href="#">
                                Terms
                            </Link>
                            <Link color="text.secondary" variant="body2" href="#">
                                Privacy
                            </Link>
                            <Link color="text.secondary" variant="body2" href="#">
                                Contact
                            </Link>
                        </Box>
                    </Box>
                    <Box
                        sx={{
                            display: 'flex',
                            justifyContent: 'space-between',
                            pt: {xs: 4, sm: 8},
                            width: '100%',
                            borderTop: '1px solid',
                            borderColor: 'divider',
                        }}
                    >
                        <div>
                            <Link color="text.secondary" variant="body2" href="#">
                                Privacy Policy
                            </Link>
                            <Typography sx={{display: 'inline', mx: 0.5, opacity: 0.5}}>
                                &nbsp;•&nbsp;
                            </Typography>
                            <Link color="text.secondary" variant="body2" href="#">
                                Terms of Service
                            </Link>
                            {/*<Copyright />*/}
                        </div>
                        <Stack
                            direction="row"
                            spacing={1}
                            useFlexGap
                            sx={{justifyContent: 'left', color: 'text.secondary'}}
                        >
                            <IconButton
                                color="inherit"
                                size="small"
                                href="https://github.com/mui"
                                aria-label="GitHub"
                                sx={{alignSelf: 'center'}}
                            >
                                <GitHubIcon/>
                            </IconButton>
                            <IconButton
                                color="inherit"
                                size="small"
                                href="https://x.com/MaterialUI"
                                aria-label="X"
                                sx={{alignSelf: 'center'}}
                            >
                                <TwitterIcon/>
                            </IconButton>
                            <IconButton
                                color="inherit"
                                size="small"
                                href="https://www.linkedin.com/company/mui/"
                                aria-label="LinkedIn"
                                sx={{alignSelf: 'center'}}
                            >
                                <LinkedInIcon/>
                            </IconButton>
                        </Stack>
                    </Box>
                </Container>
            </React.Fragment>
            {/*<Box*/}
            {/*    component="footer"*/}
            {/*    sx={{*/}
            {/*        borderTop: "1px solid #ddd",*/}
            {/*        py: 1,*/}
            {/*        textAlign: "center",*/}
            {/*    }}*/}
            {/*>*/}
            {/*    <Typography*/}
            {/*        variant="overline"*/}
            {/*        sx={{*/}
            {/*            color: 'text.secondary',*/}
            {/*        }}*/}
            {/*    >*/}
            {/*        <Stack*/}
            {/*            direction="row"*/}
            {/*            justifyContent="center"*/}
            {/*            alignItems="center"*/}
            {/*            spacing={0.3}*/}
            {/*        >*/}
            {/*            <div>{'Copyright'}</div>*/}
            {/*            <div>{'©'}</div>*/}
            {/*            <div>{new Date().getFullYear()}</div>*/}
            {/*            <Link underline="none" color="inherit" href="https://www.woder.fr/">*/}
            {/*                WODER*/}
            {/*            </Link>*/}
            {/*            <div>{'Tous droits réservés.'}</div>*/}
            {/*        </Stack>*/}
            {/*    </Typography>*/}
            {/*</Box>*/}
        </>
    );
}
