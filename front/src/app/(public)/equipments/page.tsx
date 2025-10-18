"use client";

import PublicLayout from "@common/layouts/PublicLayout";
import {
    Box,
    Button,
    Card,
    Link,
    Skeleton,
    Stack,
    Typography,
} from "@mui/material";
import {useEffect} from "react";
import {useApiAuth} from "@common/api/context/ApiAuthProvider";
import {generateSlug} from "@/middleware";
import {useEquipments} from "@common/api/hooks/useEquipments";
import {Equipment} from "@common/api/domain/Equipment";
import {ServiceResponse} from "@common/api/application/types/ServiceResponse";
import {InfiniteData} from "@tanstack/react-query";

export default function EquipmentsPage() {
    const {autoLoginPublic, isPublicAuthenticated, isPublicLoading} = useApiAuth();
    const {data, isLoading, fetchNextPage, hasNextPage, isFetchingNextPage} = useEquipments({perPage: 10});

    useEffect(() => {
        if (!isPublicAuthenticated && !isPublicLoading) autoLoginPublic();
    }, [isPublicAuthenticated, isPublicLoading, autoLoginPublic]);

    if (!isPublicAuthenticated) return null;

    const equipments: Equipment[] = (data as InfiniteData<ServiceResponse<Equipment[]>>)?.pages.flatMap(
        (page) => page.data
    ) ?? [];

    return (
        <PublicLayout>
            <Typography variant="h1" sx={{ mb: 3 }}>
                Equipements
            </Typography>
            {isLoading ? (
                <Stack sx={{gap: 2}}>
                    {[...Array(5)].map((_, i) => (
                        <Skeleton key={i} variant="rounded" width="100%" height={75}/>
                    ))}
                </Stack>
            ) : equipments.length === 0 ? (
                <Box sx={{textAlign: "center", my: 4}}>
                    <Typography variant="body1" color="text.secondary">
                        Aucun équipement trouvé
                    </Typography>
                </Box>
            ) : (
                <Box sx={{display: "flex", flexDirection: "column", gap: 2}}>
                    {equipments.map((equipment) => (
                        <Link
                            key={equipment.id}
                            href={`/equipments/${generateSlug(equipment.name, equipment.id)}`}
                            underline="none"
                        >
                            <Card variant="outlined">
                                <Box sx={{p: 2}}>
                                    <Typography variant="h6">{equipment.name}</Typography>
                                    <Typography color="text.secondary">{equipment.summary}</Typography>
                                </Box>
                            </Card>
                        </Link>
                    ))}

                    {hasNextPage && (
                        <Box sx={{display: "flex", justifyContent: "center", mt: 3}}>
                            <Button
                                variant="contained"
                                onClick={() => fetchNextPage()}
                                disabled={isFetchingNextPage}
                                disableElevation
                                sx={{width: '100%'}}
                            >
                                {isFetchingNextPage ? "Chargement..." : "Charger plus"}
                            </Button>
                        </Box>
                    )}
                </Box>
            )}
        </PublicLayout>
    );
}
