import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { Role } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Shield } from 'lucide-react';
import { DataTable, type Column } from '@/components/ui/data-table';
import { PageHeader } from '@/components/page-header';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Role Management',
        href: '/admin/roles',
    },
];

export default function Index({ roles }: { roles: Role[] }) {
    const handleDelete = (roleId: number) => {
        if (confirm('Are you sure you want to delete this role?')) {
            router.delete(`/admin/roles/${roleId}`);
        }
    };

    const columns: Column<Role>[] = [
        {
            header: 'Name',
            accessor: 'name',
        },
        {
            header: 'Guard',
            accessor: 'guard_name',
        },
        {
            header: 'Permissions',
            accessor: (role) => (
                <Badge variant="secondary">
                    {role.permissions?.length || 0} Permissions
                </Badge>
            ),
        },
        {
            header: 'Created At',
            accessor: (role) => new Date(role.created_at).toLocaleDateString(),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Role Management" />
            <div className="container mx-auto px-4 py-8 max-w-7xl">
                <div className="space-y-6">
                    <PageHeader
                        title="Role Management"
                        description="Manage your organization's roles and their permissions"
                        icon={Shield}
                        count={roles.length}
                        createRoute="/admin/roles/create"
                        createLabel="Add Role"
                    />

                    <Card>
                        <CardHeader>
                            <CardTitle>Role List</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <DataTable
                                columns={columns}
                                data={roles}
                                onDelete={handleDelete}
                                viewRoute={(id) => `/admin/roles/${id}`}
                                editRoute={(id) => `/admin/roles/${id}/edit`}
                            />
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
