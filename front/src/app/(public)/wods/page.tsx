"use client";

import PublicLayout from "@common/layouts/PublicLayout";
import {
    Box,
    Button,
    Card,
    CircularProgress,
    Link,
    Skeleton,
    Stack,
    Typography,
} from "@mui/material";
import {useEffect, useState, useMemo} from "react";
import {useApiAuth} from "@common/api/context/ApiAuthProvider";
import InfiniteScroll from "react-infinite-scroll-component";
import {Filters, GenderFilter, Wod, WodParams} from "@common/api/domain/Wod";
import {WodService} from "@common/api/application";
import SearchEngine from "@common/ui/components/SearchEngine";
import SearchBar from "@common/ui/components/SearchBar";
import {useRouter} from "next/navigation";
import {generateSlug} from "@/middleware";

export default function WodsPage() {
    const router = useRouter();
    const {autoLoginPublic, isPublicAuthenticated, isPublicLoading} = useApiAuth();

    const [wods, setWods] = useState<Wod[]>([]);
    const [isLoading, setIsLoading] = useState(false);
    const [isSearching, setIsSearching] = useState(false);
    const [page, setPage] = useState(1);
    const [hasMore, setHasMore] = useState(true);
    const [filters, setFilters] = useState<Filters>({});

    const ITEMS_PER_PAGE = 10;

    // --- Fonction fetch WODs ---
    const fetchWods = async (reset = false, currentFilters?: Filters) => {
        const service = new WodService();
        const appliedFilters = currentFilters ?? filters;

        const params: WodParams = {
            page: reset ? 1 : page,
            itemsPerPage: ITEMS_PER_PAGE,
            ...(appliedFilters.search ? {title: appliedFilters.search} : {}),
            ...(appliedFilters.search ? {description: appliedFilters.search} : {}),
            ...(appliedFilters.wodCategory ? {wodCategory: appliedFilters.wodCategory} : {}),
            ...(appliedFilters.wodType ? {wodType: appliedFilters.wodType} : {}),
            ...(appliedFilters.wodVersionType ? {wodVersionType: appliedFilters.wodVersionType} : {}),
            ...(appliedFilters.movements ? {movements: appliedFilters.movements} : {}),
            ...(appliedFilters.equipments ? {equipments: appliedFilters.equipments} : {}),
            ...(appliedFilters.muscles ? {muscles: appliedFilters.muscles} : {}),
            ...(appliedFilters.gender ? {gender: appliedFilters.gender} : {}),
            ...(appliedFilters.timeCap ? {"wodVersions.wodVersionVariants.timeCap": appliedFilters.timeCap} : {}),
        };

        try {
            const {data} = await service.fetchWods(params);
            setWods(prev => (reset ? data : [...prev, ...data]));
            setPage(prev => (reset ? 2 : prev + 1));
            setHasMore(data.length === ITEMS_PER_PAGE);
            setIsLoading(true);
        } catch (err) {
            console.error("Impossible de charger les WODs", err);
        } finally {
            setIsLoading(false);
        }
    };

    // --- Initialisation filtre depuis URL (une seule fois) ---
    useEffect(() => {
        const urlParams = new URLSearchParams(window.location.search);
        const mapParamToNumberArray = (param: string[]) => param?.map(Number) || [];

        const initialFilters: Filters = {
            search: urlParams.get("search") || undefined,
            gender: (urlParams.get("gender") as GenderFilter) || undefined,
            wodCategory: mapParamToNumberArray(urlParams.getAll("wodCategory[]")),
            wodType: mapParamToNumberArray(urlParams.getAll("wodType[]")),
            wodVersionType: mapParamToNumberArray(urlParams.getAll("wodVersionType[]")),
            movements: {
                in: mapParamToNumberArray(urlParams.getAll("movements[in][]")),
                notIn: mapParamToNumberArray(urlParams.getAll("movements[notIn][]")),
            },
            equipments: {
                in: mapParamToNumberArray(urlParams.getAll("equipments[in][]")),
                notIn: mapParamToNumberArray(urlParams.getAll("equipments[notIn][]")),
            },
            muscles: {
                in: mapParamToNumberArray(urlParams.getAll("muscles[in][]")),
                notIn: mapParamToNumberArray(urlParams.getAll("muscles[notIn][]")),
            },
            timeCap: {
                ...(urlParams.get("wodVersions.wodVersionVariants.timeCap[gte]") ? { gte: Number(urlParams.get("wodVersions.wodVersionVariants.timeCap[gte]")) } : {}),
                ...(urlParams.get("wodVersions.wodVersionVariants.timeCap[lt]") ? { lt: Number(urlParams.get("wodVersions.wodVersionVariants.timeCap[lt]")) } : {}),
            }
        };

        setFilters(initialFilters);
        fetchWods(true, initialFilters);
    }, []);

    // --- Application des filtres utilisateur + mise à jour URL ---
    const applyFilters = async (extraFilters: Partial<Filters>) => {
        setIsSearching(true);
        const newFilters = { ...filters, ...extraFilters };
        setFilters(newFilters);

        await fetchWods(true, newFilters);

        const query = new URLSearchParams();
        if (newFilters.search) query.set("search", newFilters.search);
        newFilters.wodCategory?.forEach(v => query.append("wodCategory[]", String(v)));
        newFilters.wodType?.forEach(v => query.append("wodType[]", String(v)));
        newFilters.wodVersionType?.forEach(v => query.append("wodVersionType[]", String(v)));

        newFilters.movements?.in?.forEach(v => query.append("movements[in][]", String(v)));
        newFilters.movements?.notIn?.forEach(v => query.append("movements[notIn][]", String(v)));

        newFilters.equipments?.in?.forEach(v => query.append("equipments[in][]", String(v)));
        newFilters.equipments?.notIn?.forEach(v => query.append("equipments[notIn][]", String(v)));

        newFilters.muscles?.in?.forEach(v => query.append("muscles[in][]", String(v)));
        newFilters.muscles?.notIn?.forEach(v => query.append("muscles[notIn][]", String(v)));

        if (newFilters.gender) query.set("gender", newFilters.gender);
        if (newFilters.timeCap?.gte) query.set("wodVersions.wodVersionVariants.timeCap[gte]", String(newFilters.timeCap.gte));
        if (newFilters.timeCap?.lt) query.set("wodVersions.wodVersionVariants.timeCap[lt]", String(newFilters.timeCap.lt));

        router.replace(`${window.location.pathname}?${query.toString()}`, { scroll: false });

        setIsSearching(false);
    };

    const handleSearch = (query: string) => applyFilters({ search: query });
    const handleFilters = (extraFilters: Partial<Filters>) => applyFilters(extraFilters);

    // --- Auto login ---
    useEffect(() => {
        if (!isPublicAuthenticated && !isPublicLoading) autoLoginPublic();
    }, [isPublicAuthenticated, isPublicLoading, autoLoginPublic]);

    const searchEngineInitialValues = useMemo(() => ({
        wodCategory: filters.wodCategory || [],
        wodType: filters.wodType || [],
        wodVersionType: filters.wodVersionType || [],
        movements: filters.movements || { in: [], notIn: [] },
        equipments: filters.equipments || { in: [], notIn: [] },
        muscles: filters.muscles || { in: [], notIn: [] },
        gender: filters.gender,
        timeCap: filters.timeCap,
    }), [filters]);

    return (
        <PublicLayout>
            {
                isPublicLoading || !isPublicAuthenticated ? (
                    <Box className="flex min-h-screen items-center justify-center">
                        <CircularProgress/>
                    </Box>
                ) : (
                    <Box>
                        <Typography variant="h1" sx={{ mb: 3 }}>
                            WODs
                        </Typography>
                        <Box sx={{ display: "flex", gap: 2 }}>
                            <Box sx={{ position: "sticky", alignSelf: "flex-start", top: 84, py: "20px", minWidth: 300, maxWidth: 300, width: 300 }}>
                                <SearchEngine
                                    onSearch={handleFilters}
                                    loading={isSearching}
                                    initialValues={searchEngineInitialValues}
                                />
                            </Box>

                            <Box sx={{ flexGrow: 1 }}>
                                <Box sx={{ position: "sticky", display: "flex", flexDirection: "column", alignSelf: "flex-start", top: 84, py: "20px", width: "100%", zIndex: 10, backgroundColor: "#fcfcfc", gap: 2 }}>
                                    <SearchBar onSearch={handleSearch} loading={isSearching} value={{ search: filters.search || "" }} />
                                </Box>

                                {isLoading ? (
                                    <Stack sx={{ gap: 2 }}>
                                        {[...Array(5)].map((_, i) => (
                                            <Skeleton key={i} variant="rounded" width="100%" height={75} />
                                        ))}
                                    </Stack>
                                ) : !isLoading && wods.length === 0 ? (
                                    <Box sx={{ textAlign: "center", my: 4 }}>
                                        <Typography variant="body1" color="text.secondary">Aucun WOD trouvé</Typography>
                                    </Box>
                                ) : (
                                    <InfiniteScroll
                                        dataLength={wods.length}
                                        next={() => {
                                        }}
                                        hasMore={false}
                                        loader={null}
                                    >
                                        <Box sx={{display: "flex", flexDirection: "column", gap: 2}}>
                                            {wods.map((wod) => (
                                                <Link key={wod.id} href={`/wods/${generateSlug(wod.title, wod.id)}`}>
                                                    <Card variant="outlined">
                                                        <Box sx={{p: 2}}>
                                                            <Typography variant="h6">{wod.title}</Typography>
                                                            <Typography>{wod.description}</Typography>
                                                        </Box>
                                                    </Card>
                                                </Link>
                                            ))}
                                        </Box>

                                        {hasMore && (
                                            <Box sx={{display: "flex", justifyContent: "center", mt: 3}}>
                                                <Button
                                                    variant="contained"
                                                    onClick={() => fetchWods()}
                                                    disabled={isLoading}
                                                    sx={{width: '100%'}}
                                                >
                                                    {isLoading ? "Chargement..." : "Charger plus"}
                                                </Button>
                                            </Box>
                                        )}
                                    </InfiniteScroll>
                                )}
                            </Box>
                        </Box>
                    </Box>
                )
            }
        </PublicLayout>
    );
}
