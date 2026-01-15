import React from 'react';
import RootLayout from '@layouts/RootLayout';
import BackgroundBox from "@common/ui/BackgroundBox";

interface TwitchLayoutProps {
    children: React.ReactNode;
    background: string;
}

const TwitchLayout: React.FC<TwitchLayoutProps> = ({ children, background }) => {
    return (
        <RootLayout>
            <BackgroundBox background={background}>
                {children}
            </BackgroundBox>
        </RootLayout>
    );
};

export default TwitchLayout;
