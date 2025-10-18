import * as React from 'react';
import {styled} from '@mui/material/styles';
import MuiDrawer, {drawerClasses} from '@mui/material/Drawer';
import {Avatar, Box, Divider, Skeleton, Stack, Typography} from '@mui/material';
import SelectContent from '@common/ui/layout/SelectContent';
import MenuContent from '@common/ui/layout/MenuContent';
import OptionsMenu from '@common/ui/layout/OptionsMenu';
import {useSelector} from "react-redux";
import {RootState} from "@stores/stores";

const drawerWidth = 240;

const Drawer = styled(MuiDrawer)({
    width: drawerWidth,
    flexShrink: 0,
    boxSizing: 'border-box',
    mt: 10,
    [`& .${drawerClasses.paper}`]: {
        width: drawerWidth,
        boxSizing: 'border-box',
    },
});

export default function SideMenu() {
    const { currentUser, status } = useSelector((state: RootState) => state.user);

    return (
        <Drawer
            variant="permanent"
            sx={{
                display: {xs: 'none', md: 'block'},
                [`& .${drawerClasses.paper}`]: {
                    backgroundColor: "#f5f6fa",
                },
            }}
        >
            <Box
                sx={{
                    display: 'flex',
                    mt: 'calc(var(--template-frame-height, 0px) + 4px)',
                    p: 1.5,
                }}
            >
                <SelectContent/>
            </Box>
            <Divider/>
            <Box
                sx={{
                    overflow: 'auto',
                    height: '100%',
                    display: 'flex',
                    flexDirection: 'column',
                }}
            >
                <MenuContent/>
            </Box>
            <Stack
                direction="row"
                sx={{
                    p: 2,
                    gap: 1,
                    alignItems: 'center',
                    borderTop: '1px solid',
                    borderColor: 'divider',
                }}
            >
                {status === 'loading' || !currentUser ? (
                    <>
                        <Skeleton variant="circular" sx={{ width: 36, height: 36 }} />
                        <Box sx={{ mr: 'auto' }}>
                            <Skeleton width={120} height={20} />
                            <Skeleton width={180} height={15} />
                        </Box>
                    </>
                ) : (
                    <>
                        <Avatar
                            sizes="small"
                            alt={`${currentUser.firstName} ${currentUser.lastName}`}
                            src="/static/images/avatar/7.jpg"
                            sx={{ width: 36, height: 36 }}
                        />
                        <Box sx={{ mr: 'auto' }}>
                            <Typography variant="body1" sx={{ fontFamily: "bold", lineHeight: '16px' }}>
                                {currentUser.firstName} {currentUser.lastName}
                            </Typography>
                            <Typography variant="caption" sx={{ color: 'text.secondary' }}>
                                {currentUser.email}
                            </Typography>
                        </Box>
                    </>
                )}
                <OptionsMenu/>
            </Stack>
        </Drawer>
    );
}
