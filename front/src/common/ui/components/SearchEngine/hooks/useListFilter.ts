import { useState, useEffect } from "react";

export type FilterOption = { id: number; name: string };

export function useListFilter(fetcher: () => Promise<FilterOption[]>, initialSelected: number[] = [], initialExcluded: number[] = []) {
    const [items, setItems] = useState<FilterOption[]>([]);
    const [selected, setSelected] = useState<number[]>(initialSelected);
    const [excluded, setExcluded] = useState<number[]>(initialExcluded);
    const [search, setSearch] = useState("");
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        let mounted = true;
        fetcher().then(data => {
            if (!mounted) return;
            setItems(data);
            setLoading(false);
        });
        return () => { mounted = false; };
    }, [fetcher]);

    useEffect(() => {
        if (items.length === 0) return;
        setSelected(prev => prev.length === 0 ? initialSelected.filter(id => items.some(item => item.id === id)) : prev);
        setExcluded(prev => prev.length === 0 ? initialExcluded.filter(id => items.some(item => item.id === id)) : prev);
    }, [items]);

    return { items, selected, setSelected, excluded, setExcluded, search, setSearch, loading };
}
