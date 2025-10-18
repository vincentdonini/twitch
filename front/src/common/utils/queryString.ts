export function buildQueryString(params: Record<string, unknown>, prefix = ""): string {
    const qs: string[] = [];

    Object.entries(params).forEach(([key, value]) => {
        if (value === undefined || value === null) return;

        const prefixedKey = prefix ? `${prefix}.${key}` : key;

        if (Array.isArray(value)) {
            if (value.length === 0) return;
            value.forEach(v => {
                qs.push(`${prefixedKey}[]=${encodeURIComponent(String(v))}`);
            });
        } else if (typeof value === "object") {
            Object.entries(value).forEach(([subKey, subValue]) => {
                if (subValue === undefined || subValue === null) return;

                if (Array.isArray(subValue)) {
                    if (subValue.length === 0) return;
                    subValue.forEach(v => {
                        qs.push(`${prefixedKey}[${subKey}][]=${encodeURIComponent(String(v))}`);
                    });
                } else {
                    qs.push(`${prefixedKey}[${subKey}]=${encodeURIComponent(String(subValue))}`);
                }
            });
        } else {
            qs.push(`${prefixedKey}=${encodeURIComponent(String(value))}`);
        }
    });

    return qs.join("&");
}
