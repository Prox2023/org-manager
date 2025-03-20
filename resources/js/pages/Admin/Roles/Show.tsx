import { Head, router } from "@inertiajs/react";
import { PageHeader } from "@/components/page-header";
import { SectionCard } from "@/components/section-card";
import { PermissionsTable } from "@/components/permissions-table";
import { Role, Permission } from "@/types";
import { Shield, Key, Users, Search } from "lucide-react";
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Input } from "@/components/ui/input";
import { useDebounce } from "@/hooks/use-debounce";
import { useEffect, useState } from "react";

interface Props {
    role: Role;
    permissions: {
        data: Permission[];
        current_page: number;
        last_page: number;
        total: number;
        per_page: number;
    };
    filters: {
        search?: string;
    };
}

export default function Show({ role, permissions, filters }: Props) {
    const [search, setSearch] = useState(filters.search || '');
    const debouncedSearch = useDebounce(search, 300);

    useEffect(() => {
        router.get(
            route('admin.roles.show', role.id),
            { search: debouncedSearch },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    }, [debouncedSearch]);

    const handleTogglePermission = (permission: Permission) => {
        router.post(route('admin.roles.permissions.toggle', {
            role: role.id,
            permission: permission.id
        }), {
            search: debouncedSearch
        }, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                console.log('Permission toggled successfully');
            },
            onError: (errors) => {
                console.error('Error toggling permission:', errors);
            }
        });
    };

    const handleAddPermission = () => {
        // TODO: Implement add permission logic
        console.log("Add permission");
    };

    const handlePageChange = (page: number) => {
        router.get(
            route('admin.roles.show', role.id),
            { 
                search: debouncedSearch,
                page 
            },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    };

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Role Management',
            href: route('admin.roles.index'),
            children: {
                title: 'Role Info',
                href: route('admin.roles.index'),
                children: {
                    title: role.name,
                    href: route('admin.roles.show', role.id),
                }
            }
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Role: ${role.name}`} />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <PageHeader
                        title={role.name}
                        description={`Manage permissions for the ${role.name} role`}
                        icon={Shield}
                        count={role.permissions?.length || 0}
                    />

                    <div className="mt-8 space-y-6">
                        <SectionCard
                            title="Role Information"
                            icon={Users}
                            className="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/50 dark:to-indigo-950/50"
                        >
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 className="text-sm font-medium text-muted-foreground">Name</h3>
                                    <p className="mt-1 text-sm">{role.name}</p>
                                </div>
                                <div>
                                    <h3 className="text-sm font-medium text-muted-foreground">Guard</h3>
                                    <p className="mt-1 text-sm">{role.guard_name}</p>
                                </div>
                                <div>
                                    <h3 className="text-sm font-medium text-muted-foreground">Created At</h3>
                                    <p className="mt-1 text-sm">{role.created_at}</p>
                                </div>
                                <div>
                                    <h3 className="text-sm font-medium text-muted-foreground">Updated At</h3>
                                    <p className="mt-1 text-sm">{role.updated_at}</p>
                                </div>
                            </div>
                        </SectionCard>

                        <SectionCard
                            title="Permissions"
                            icon={Key}
                            className="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-950/50 dark:to-pink-950/50"
                        >
                            <div className="mb-4">
                                <div className="relative">
                                    <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                                    <Input
                                        placeholder="Search permissions..."
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        className="pl-8"
                                    />
                                </div>
                            </div>
                            <PermissionsTable
                                permissions={permissions.data}
                                selectedPermissions={role.permissions || []}
                                onTogglePermission={handleTogglePermission}
                                onAddPermission={handleAddPermission}
                                currentPage={permissions.current_page}
                                lastPage={permissions.last_page}
                                total={permissions.total}
                                perPage={permissions.per_page}
                                onPageChange={handlePageChange}
                            />
                        </SectionCard>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
