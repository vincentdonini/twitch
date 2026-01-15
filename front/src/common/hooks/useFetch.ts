import { useState, useEffect } from "react";

export function useFetch<T>(fetchFunction: () => Promise<T>, deps: any[] = []) {
    const [data, setData] = useState<T | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        setLoading(true);
        fetchFunction()
            .then(setData)
            .catch((err) => setError(err.message))
            .finally(() => setLoading(false));
    }, deps);

    return { data, loading, error };
}
