import * as React from 'react';
import {styled} from '@mui/material/styles';
import {
    CircularProgress,
    Divider,
    Menu,
    MenuItem as MuiMenuItem,
    ListItemText,
    ListItemIcon,
    dividerClasses,
    paperClasses,
    listClasses,
    listItemIconClasses
} from '@mui/material/';
import LogoutRoundedIcon from '@mui/icons-material/LogoutRounded';
import MoreVertRoundedIcon from '@mui/icons-material/MoreVertRounded';
import MenuButton from '@common/ui/layout/MenuButton';
import {useApiAuth} from "@common/api/context/ApiAuthProvider";

const MenuItem = styled(MuiMenuItem)({
    margin: '2px 0',
});

export default function OptionsMenu() {
    const {logoutUser, isLoading} = useApiAuth();

    const [anchorEl, setAnchorEl] = React.useState<null | HTMLElement>(null);
    const open = Boolean(anchorEl);
    const handleClick = (event: React.MouseEvent<HTMLElement>) => {
        setAnchorEl(event.currentTarget);
    };
    const handleClose = () => {
        setAnchorEl(null);
    };

    const handleLogout = () => {
        logoutUser();
        handleClose();
    };

    return (
        <React.Fragment>
            <MenuButton
                aria-label="Open menu"
                onClick={handleClick}
                sx={{borderColor: 'transparent'}}
            >
                <MoreVertRoundedIcon/>
            </MenuButton>
            <Menu
                anchorEl={anchorEl}
                id="menu"
                open={open}
                onClose={handleClose}
                onClick={handleClose}
                transformOrigin={{horizontal: 'right', vertical: 'top'}}
                anchorOrigin={{horizontal: 'right', vertical: 'bottom'}}
                sx={{
                    [`& .${listClasses.root}`]: {
                        padding: '4px',
                    },
                    [`& .${paperClasses.root}`]: {
                        padding: 0,
                    },
                    [`& .${dividerClasses.root}`]: {
                        margin: '4px -4px',
                    },
                }}
            >
                <MenuItem onClick={handleClose}>Profile</MenuItem>
                <MenuItem onClick={handleClose}>My account</MenuItem>
                <Divider/>
                <MenuItem onClick={handleClose}>Add another account</MenuItem>
                <MenuItem onClick={handleClose}>Settings</MenuItem>
                <Divider/>
                <MenuItem
                    onClick={handleLogout}
                    sx={{
                        [`& .${listItemIconClasses.root}`]: {
                            ml: 'auto',
                            minWidth: 0,
                        },
                    }}
                >
                    <ListItemText>Logout</ListItemText>
                    {isLoading ? (
                        <CircularProgress size={15} />
                    ) : (
                        <ListItemIcon>
                            <LogoutRoundedIcon fontSize="small" />
                        </ListItemIcon>
                    )}
                </MenuItem>
            </Menu>
        </React.Fragment>
    );
}
