import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { Role } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Shield, Search, ArrowUpDown } from "lucide-react";
import { DataTable, type Column } from '@/components/ui/data-table';
import { PageHeader } from '@/components/page-header';
import { Input } from "@/components/ui/input";
import { useDebounce } from "@/hooks/use-debounce";
import { useEffect, useState } from "react";
import { Button } from "@/components/ui/button";

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Role Management',
        href: route('admin.roles.index'),
    },
];

interface Props {
    roles: {
        data: Role[];
        current_page: number;
        last_page: number;
        total: number;
        per_page: number;
    };
    filters: {
        search?: string;
    };
    sort: {
        field: string;
        direction: 'asc' | 'desc';
    };
}

export default function Index({ roles, filters, sort }: Props) {
    const [search, setSearch] = useState(filters.search || '');
    const debouncedSearch = useDebounce(search, 300);

    useEffect(() => {
        router.get(
            route('admin.roles.index'),
            { 
                search: debouncedSearch,
                sort_field: sort.field,
                sort_direction: sort.direction
            },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    }, [debouncedSearch, sort.field, sort.direction]);

    const handleDelete = (roleId: number) => {
        if (confirm('Are you sure you want to delete this role?')) {
            router.delete(`/admin/roles/${roleId}`);
        }
    };

    const handleSort = (field: string) => {
        const newDirection = sort.field === field && sort.direction === 'asc' ? 'desc' : 'asc';
        router.get(
            route('admin.roles.index'),
            { 
                search: debouncedSearch,
                sort_field: field,
                sort_direction: newDirection
            },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    };

    const columns: Column<Role>[] = [
        {
            header: () => (
                <Button
                    variant="ghost"
                    onClick={() => handleSort('name')}
                    className="flex items-center gap-1"
                >
                    Name
                    {sort.field === 'name' && (
                        <ArrowUpDown className="h-4 w-4" />
                    )}
                </Button>
            ),
            accessor: 'name',
        },
        {
            header: () => (
                <Button
                    variant="ghost"
                    onClick={() => handleSort('guard_name')}
                    className="flex items-center gap-1"
                >
                    Guard
                    {sort.field === 'guard_name' && (
                        <ArrowUpDown className="h-4 w-4" />
                    )}
                </Button>
            ),
            accessor: 'guard_name',
        },
        {
            header: 'Permissions',
            accessor: (role) => (
                <div className="flex flex-wrap gap-1">
                    {role.permissions?.map((permission) => (
                        <Badge key={permission.id} variant="secondary">
                            {permission.name}
                        </Badge>
                    ))}
                </div>
            ),
        },
        {
            header: () => (
                <Button
                    variant="ghost"
                    onClick={() => handleSort('created_at')}
                    className="flex items-center gap-1"
                >
                    Created At
                    {sort.field === 'created_at' && (
                        <ArrowUpDown className="h-4 w-4" />
                    )}
                </Button>
            ),
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
                        description="Manage your organization's roles and permissions"
                        icon={Shield}
                        count={roles.total}
                        createRoute={route('admin.roles.create')}
                        createLabel="Add Role"
                    />

                    <Card>
                        <CardHeader>
                            <div className="flex items-center justify-between">
                                <CardTitle>Role List</CardTitle>
                                <div className="relative w-64">
                                    <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                                    <Input
                                        placeholder="Search roles..."
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        className="pl-8"
                                    />
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <DataTable
                                columns={columns}
                                data={roles.data}
                                onDelete={handleDelete}
                                viewRoute={(id) => route('admin.roles.show', id)}
                                currentPage={roles.current_page}
                                lastPage={roles.last_page}
                                total={roles.total}
                                perPage={roles.per_page}
                                onPageChange={(page) => {
                                    router.get(
                                        route('admin.roles.index'),
                                        { 
                                            search: debouncedSearch,
                                            sort_field: sort.field,
                                            sort_direction: sort.direction,
                                            page
                                        },
                                        {
                                            preserveState: true,
                                            preserveScroll: true,
                                        }
                                    );
                                }}
                            />
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
