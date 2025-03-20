import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { User } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Users } from 'lucide-react';
import { DataTable, type Column } from '@/components/ui/data-table';
import { PageHeader } from '@/components/page-header';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User Management',
        href: '/admin/users',
    },
];

export default function Index({ users }: { users: User[] }) {
    const handleDelete = (userId: number) => {
        if (confirm('Are you sure you want to delete this user?')) {
            router.delete(`/admin/users/${userId}`);
        }
    };

    const columns: Column<User>[] = [
        {
            header: 'Name',
            accessor: 'name',
        },
        {
            header: 'Email',
            accessor: 'email',
        },
        {
            header: 'Organization',
            accessor: (user) => (
                <Badge variant="outline">
                    {user.organization?.name || 'N/A'}
                </Badge>
            ),
        },
        {
            header: 'Team',
            accessor: (user) => (
                <Badge variant="outline">
                    {user.team?.name || 'N/A'}
                </Badge>
            ),
        },
        {
            header: 'Created At',
            accessor: (user) => new Date(user.created_at).toLocaleDateString(),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="User Management" />
            <div className="container mx-auto px-4 py-8 max-w-7xl">
                <div className="space-y-6">
                    <PageHeader
                        title="User Management"
                        description="Manage your organization's users and their permissions"
                        icon={Users}
                        count={users.length}
                        createRoute="/admin/users/create"
                        createLabel="Add User"
                    />

                    <Card>
                        <CardHeader>
                            <CardTitle>User List</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <DataTable
                                columns={columns}
                                data={users}
                                onDelete={handleDelete}
                                viewRoute={(id) => `/admin/users/${id}`}
                                editRoute={(id) => `/admin/users/${id}/edit`}
                            />
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
