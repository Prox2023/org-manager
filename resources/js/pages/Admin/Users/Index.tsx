import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { User } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Users, Search, ArrowUpDown } from "lucide-react";
import { DataTable, type Column } from '@/components/ui/data-table';
import { PageHeader } from '@/components/page-header';
import { Input } from "@/components/ui/input";
import { useDebounce } from "@/hooks/use-debounce";
import { useEffect, useState } from "react";
import { Button } from "@/components/ui/button";

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User Management',
        href: '/admin/users',
    },
];

interface Props {
    users: {
        data: User[];
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

export default function Index({ users, filters, sort }: Props) {
    const [search, setSearch] = useState(filters.search || '');
    const debouncedSearch = useDebounce(search, 300);

    useEffect(() => {
        router.get(
            route('admin.users.index'),
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

    const handleDelete = (userId: number) => {
        if (confirm('Are you sure you want to delete this user?')) {
            router.delete(`/admin/users/${userId}`);
        }
    };

    const handleSort = (field: string) => {
        const newDirection = sort.field === field && sort.direction === 'asc' ? 'desc' : 'asc';
        router.get(
            route('admin.users.index'),
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

    const columns: Column<User>[] = [
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
                    onClick={() => handleSort('email')}
                    className="flex items-center gap-1"
                >
                    Email
                    {sort.field === 'email' && (
                        <ArrowUpDown className="h-4 w-4" />
                    )}
                </Button>
            ),
            accessor: 'email',
        },
        {
            header: 'Organization',
            accessor: (user) => (
                <Badge variant="outline" className="bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300">
                    {user.organization?.name || 'N/A'}
                </Badge>
            ),
        },
        {
            header: 'Team',
            accessor: (user) => (
                <Badge variant="outline" className="bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300">
                    {user.current_team?.name || 'N/A'}
                </Badge>
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
                        count={users.total}
                        createRoute="/admin/users/create"
                        createLabel="Add User"
                    />

                    <Card className="bg-gradient-to-br from-white to-gray-50 dark:from-gray-950 dark:to-gray-900">
                        <CardHeader>
                            <div className="flex items-center justify-between">
                                <CardTitle className="flex items-center gap-2">
                                    <Users className="h-5 w-5 text-blue-500" />
                                    User List
                                </CardTitle>
                                <div className="relative w-64">
                                    <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                                    <Input
                                        placeholder="Search users..."
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
                                data={users.data}
                                onDelete={handleDelete}
                                viewRoute={(id) => `/admin/users/${id}`}
                                currentPage={users.current_page}
                                lastPage={users.last_page}
                                total={users.total}
                                perPage={users.per_page}
                                onPageChange={(page) => {
                                    router.get(
                                        route('admin.users.index'),
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
