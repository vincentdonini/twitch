import React from 'react';
import RootLayout from '@layouts/RootLayout';

const AdminLayout = ({children}: { children: React.ReactNode }) => {
    return (
        <RootLayout>
            <div
                style={{border: '5px solid red'}}
                className="flex justify-center items-center absolute z-[999] top-0 left-0 right-0 bottom-0 w-[750px] h-[1334px] bg-black text-white"
            >
                {children}
            </div>
        </RootLayout>
    );
};

export default AdminLayout;
