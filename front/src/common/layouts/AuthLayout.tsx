"use client";

import {ReactNode, useEffect} from "react";
import {useRouter} from "next/navigation";
import {useApiAuth} from "@common/api/context/ApiAuthProvider";

export default function AuthLayout({children}: { children: ReactNode }) {
    const {isAuthenticated, isLoading} = useApiAuth();
    const router = useRouter();

    useEffect(() => {
        if (!isLoading && isAuthenticated) {
            router.push("/admin");
        }
    }, [isAuthenticated, isLoading, router]);

    if (isLoading) return <p>Loading...</p>;

    return (
        <div className="flex min-h-screen items-center justify-center bg-gray-100">
            <div className="w-full max-w-md p-6 bg-white shadow-md rounded-xl">
                {children}
            </div>
        </div>
    );
}
