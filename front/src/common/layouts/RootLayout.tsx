import "@common/styles/globals.scss";
import Providers from "@common/context/Providers";

export default function RootLayout({children}: { children: React.ReactNode }) {
    return (
        <html lang="fr">
        <body>
        <Providers>
            {children}
        </Providers>
        </body>
        </html>
    );
}
