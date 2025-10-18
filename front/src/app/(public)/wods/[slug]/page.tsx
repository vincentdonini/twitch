"use client";

import {useParams} from "next/navigation";
import {
    Box,
    CircularProgress,
    Typography
} from "@mui/material";
import PublicLayout from "@common/layouts/PublicLayout";
import {useWod} from "@common/api/hooks/useWod";

export default function WodDetailPage() {
    const params = useParams<{ slug: string }>();
    const slug = params?.slug || "";
    const parts = slug.split("-");
    const id = Number(parts[parts.length - 1]);


    const {data: wod, isLoading} = useWod(id);

    return (
        <PublicLayout>
            {
                isLoading ? (
                    <Box className="flex min-h-screen items-center justify-center">
                        <CircularProgress/>
                    </Box>
                ) : wod ? (
                    <Box>
                        <Typography variant="h1" gutterBottom>
                            {wod.data.title}
                        </Typography>
                        <Typography variant="body1">
                            {wod.data.description}
                        </Typography>
                    </Box>
                ) : (
                    <Typography variant="body1">
                        Aucun WOD trouvé.
                    </Typography>
                )
            }
        </PublicLayout>
    );
}
