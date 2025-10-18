"use client";

import {useParams} from "next/navigation";
import {
    Box,
    CircularProgress,
    Typography
} from "@mui/material";
import PublicLayout from "@common/layouts/PublicLayout";
import {useEquipment} from "@common/api/hooks/useEquipment";
import remarkGfm from "remark-gfm";
import ReactMarkdown from "react-markdown";

export default function EquipmentDetailPage() {
    const params = useParams<{ slug: string }>();
    const slug = params?.slug || "";
    const parts = slug.split("-");
    const id = Number(parts[parts.length - 1]);

    const { data: equipment, isLoading } = useEquipment(id);

    return (
        <PublicLayout>
            {
                isLoading ? (
                    <Box className="flex min-h-screen items-center justify-center">
                        <CircularProgress/>
                    </Box>
                ) : equipment ? (
                    <Box>
                        <Typography variant="h1" gutterBottom>
                            {equipment.data.name}
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
                            {equipment.data.details}
                        </ReactMarkdown>
                    </Box>
                ) : (
                    <Typography variant="body1">
                        Aucun équipement trouvé.
                    </Typography>
                )
            }
        </PublicLayout>
    );
}
