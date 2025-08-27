"use client";

import React from "react";
import RootLayout from "@layouts/RootLayout";

export default function HomePage() {
    return (
        <RootLayout>
            <div
                className="flex flex-col justify-center items-center text-center absolute z-[999] top-0 left-0 right-0 bottom-0 bg-black text-white"
            >
                Index
            </div>
        </RootLayout>
    );
}
