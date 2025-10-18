import * as React from "react";
import { useState, useRef, ChangeEvent, useEffect } from "react";
import { FormControl, OutlinedInput, InputAdornment, Divider } from "@mui/material";
import SearchIcon from "@mui/icons-material/Search";

type SearchBarProps = {
    onSearch?: (query: string) => Promise<void>;
    loading?: boolean;
    value?: { search: string };
};

export default function SearchBar({ onSearch, loading, value }: SearchBarProps) {
    // State interne pour l'input
    const [query, setQuery] = useState(value?.search || "");
    const timeoutRef = useRef<NodeJS.Timeout | null>(null);

    // Synchronisation avec la valeur passée en props
    useEffect(() => {
        if (value?.search !== undefined && value.search !== query) {
            setQuery(value.search);
        }
    }, [value?.search]);

    const handleChange = (e: ChangeEvent<HTMLInputElement>) => {
        const newValue = e.target.value;
        setQuery(newValue);

        // clear le timer précédent
        if (timeoutRef.current) {
            clearTimeout(timeoutRef.current);
        }

        if (newValue.length >= 3) {
            // debounce normal
            timeoutRef.current = setTimeout(() => {
                onSearch?.(newValue);
            }, 500);
        } else if (newValue.length === 0) {
            // si l'utilisateur supprime tout, on réinitialise immédiatement
            onSearch?.("");
        }
        // sinon, ne rien faire si < 3 caractères
    };

    return (
        <FormControl variant="outlined" sx={{ width: "100%" }}>
            <OutlinedInput
                value={query}
                onChange={handleChange}
                placeholder="Rechercher..."
                startAdornment={
                    <InputAdornment position="start">
                        <SearchIcon />
                    </InputAdornment>
                }
                disabled={loading}
            />
            <Divider sx={{ mt: 2 }} />
        </FormControl>
    );
}
