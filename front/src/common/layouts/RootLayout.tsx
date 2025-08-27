import React from 'react';
import "@common/globals.scss";
import Providers from "@common/context/Providers";
import AdminTools from "@common/ui/AdminTools";

const RootLayout = ({children}: { children: React.ReactNode }) => {
    return (
        <html lang="fr">
            <body>
                <Providers>
                    <AdminTools/>
                    <div id="overlay">
                        {children}
                    </div>
                </Providers>
            </body>
        </html>
    );
};

export default RootLayout;
