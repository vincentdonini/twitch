import AdminLayout from "@layouts/AdminLayout";
import {Metadata} from "next";

export const metadata: Metadata = {
    title: 'ADMIN : Remote',
};

export default function Page() {
    return (
        <AdminLayout>
            <h1>
                Remote
            </h1>
        </AdminLayout>
    );
}
