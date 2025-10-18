"use client";

import {useParams} from "next/navigation";
import {
    Box,
    Card,
    CardContent,
    CircularProgress,
    Link,
    Stack,
    Typography
} from "@mui/material";
import PublicLayout from "@common/layouts/PublicLayout";
import remarkGfm from "remark-gfm";
import ReactMarkdown from 'react-markdown';
import {useMuscle} from "@common/api/hooks/useMuscle";

export default function MuscleDetailPage() {
    const params = useParams<{ slug: string }>();
    const slug = params?.slug || "";
    const parts = slug.split("-");
    const id = Number(parts[parts.length - 1]);

    const {data: muscle, isLoading} = useMuscle(id);

    return (
        <PublicLayout>
            {
                isLoading ? (
                    <Box className="flex min-h-screen items-center justify-center">
                        <CircularProgress/>
                    </Box>
                ) : muscle ? (
                    <Stack spacing={3}>
                        <Box>
                            <Typography variant="h1" gutterBottom>
                                {muscle.data.name}
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
                                {muscle.data.details}
                            </ReactMarkdown>
                        </Box>

                        <Stack spacing={3} direction={"row"}>
                            <Card variant="outlined">
                                <CardContent>
                                    <Typography variant="h5" gutterBottom>
                                        Zone anatomique
                                    </Typography>
                                    <Typography variant="h6">
                                        {muscle.data.area.name}
                                    </Typography>
                                    <Typography variant="body2" color="text.secondary">
                                        {muscle.data.area.summary}
                                    </Typography>
                                </CardContent>
                            </Card>

                            {muscle.data.group && (
                                <Card variant="outlined">
                                    <CardContent>
                                        <Typography variant="h5" gutterBottom>
                                            Groupe musculaire
                                        </Typography>
                                        <Typography variant="h6">
                                            {muscle.data.group.name}
                                        </Typography>
                                        <Typography variant="body2" color="text.secondary">
                                            {muscle.data.group.summary}
                                        </Typography>
                                    </CardContent>
                                </Card>
                            )}
                        </Stack>

                        {muscle.data.movements?.length > 0 && (
                            <Card variant="outlined">
                                <CardContent>
                                    <Typography variant="h5" gutterBottom>
                                        Mouvements associés
                                    </Typography>
                                    <Stack spacing={1}>
                                        {muscle.data.movements.map((movementUrl, index) => {
                                            const id = movementUrl.split("/").pop();
                                            return (
                                                <Link
                                                    key={index}
                                                    href={`/movements/${id}`}
                                                    underline="none"
                                                >
                                                    Mouvement #{id}
                                                </Link>
                                            );
                                        })}
                                    </Stack>
                                </CardContent>
                            </Card>
                        )}
                    </Stack>
                ) : (
                    <Typography variant="body1">
                        Aucun groupe musculaire trouvé.
                    </Typography>
                )
            }
        </PublicLayout>
    );
}
