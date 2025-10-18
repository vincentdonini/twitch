"use client";

import {useParams} from "next/navigation";
import {
    Box,
    CircularProgress,
    Typography
} from "@mui/material";
import PublicLayout from "@common/layouts/PublicLayout";
import remarkGfm from "remark-gfm";
import ReactMarkdown from 'react-markdown';
import {useMuscleGroup} from "@common/api/hooks/useMuscleGroup";

export default function MuscleGroupDetailPage() {
    const params = useParams<{ slug: string }>();
    const slug = params?.slug || "";
    const parts = slug.split("-");
    const id = Number(parts[parts.length - 1]);

    const {data: muscleGroup, isLoading} = useMuscleGroup(id);

    return (
        <PublicLayout>
            {
                isLoading ? (
                    <Box className="flex min-h-screen items-center justify-center">
                        <CircularProgress/>
                    </Box>
                ) : muscleGroup ? (
                    <Box>
                        <Typography variant="h1" gutterBottom>
                            {muscleGroup.data.name}
                        </Typography>
                        <ReactMarkdown
                            remarkPlugins={[remarkGfm]}
                            components={{
                                h2: ({...props}) => <Typography variant="h4" sx={{mt: 3}} {...props} />,
                                h3: ({...props}) => <Typography variant="h5" gutterBottom sx={{mt: 3}} {...props} />,
                                h4: ({...props}) => <Typography variant="h6" gutterBottom sx={{mt: 3}} {...props} />,
                                p: ({...props}) => <Typography variant="body1" component="p" gutterBottom {...props} />,
                                li: ({...props}) => <Typography variant="body1" component="li" {...props} />,
                            }}
                        >
                            {muscleGroup.data.details}
                        </ReactMarkdown>
                    </Box>
                ) : (
                    <Typography variant="body1">
                        Aucun groupe musculaire trouvé.
                    </Typography>
                )
            }
        </PublicLayout>
    );
}
