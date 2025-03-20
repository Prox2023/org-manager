import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { Role, Permission } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { Shield, Key, Plus, Trash2, CheckCircle2, XCircle } from "lucide-react";
import { useState } from 'react';

interface Props {
    role: Role;
    availablePermissions: Permission[];
}

export default function Show({ role, availablePermissions = [] }: Props) {
    const [selectedPermissions, setSelectedPermissions] = useState<Permission[]>(
        role.permissions || []
    );

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Role Management',
            href: '/admin/roles',
            children: {
                title: 'Role Info',
                href: '/admin/roles',
                children: {
                    title: role.name,
                    href: `/admin/roles/${role.id}`,
                }
            }
        },
    ];

    const handlePermissionToggle = (permission: Permission) => {
        setSelectedPermissions(prev => {
            const exists = prev.some(p => p.id === permission.id);
            if (exists) {
                return prev.filter(p => p.id !== permission.id);
            }
            return [...prev, permission];
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${role.name} Role`} />
            <div className="container mx-auto px-4 py-8 max-w-7xl">
                <div className="space-y-6">
                    {/* Role Information */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Shield className="h-5 w-5" />
                                Role Information
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                <div className="flex items-center justify-between">
                                    <div className="space-y-1">
                                        <h2 className="text-2xl font-bold">{role.name}</h2>
                                        <p className="text-muted-foreground">Guard: {role.guard_name}</p>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <Badge variant="secondary">
                                            {selectedPermissions.length} Permissions
                                        </Badge>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Permissions Table */}
                    <Card>
                        <CardHeader>
                            <div className="flex items-center justify-between">
                                <CardTitle className="flex items-center gap-2">
                                    <Key className="h-5 w-5" />
                                    Permissions
                                </CardTitle>
                                <Button size="sm" className="flex items-center gap-2">
                                    <Plus className="h-4 w-4" />
                                    Add Permission
                                </Button>
                            </div>
                        </CardHeader>
                        <CardContent>
                            {availablePermissions.length === 0 ? (
                                <div className="text-center py-4 text-muted-foreground">
                                    No permissions available
                                </div>
                            ) : (
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Permission Name</TableHead>
                                            <TableHead>Guard</TableHead>
                                            <TableHead>Status</TableHead>
                                            <TableHead className="text-right">Actions</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {availablePermissions.map((permission) => {
                                            const isSelected = selectedPermissions.some(
                                                p => p.id === permission.id
                                            );
                                            return (
                                                <TableRow key={permission.id}>
                                                    <TableCell className="font-medium">
                                                        {permission.name}
                                                    </TableCell>
                                                    <TableCell>{permission.guard_name}</TableCell>
                                                    <TableCell>
                                                        {isSelected ? (
                                                            <Badge variant="default" className="flex items-center gap-1">
                                                                <CheckCircle2 className="h-3 w-3" />
                                                                Assigned
                                                            </Badge>
                                                        ) : (
                                                            <Badge variant="secondary" className="flex items-center gap-1">
                                                                <XCircle className="h-3 w-3" />
                                                                Not Assigned
                                                            </Badge>
                                                        )}
                                                    </TableCell>
                                                    <TableCell className="text-right">
                                                        <Button
                                                            variant="ghost"
                                                            size="sm"
                                                            onClick={() => handlePermissionToggle(permission)}
                                                            className={isSelected ? "text-destructive" : ""}
                                                        >
                                                            {isSelected ? (
                                                                <Trash2 className="h-4 w-4" />
                                                            ) : (
                                                                <Plus className="h-4 w-4" />
                                                            )}
                                                        </Button>
                                                    </TableCell>
                                                </TableRow>
                                            );
                                        })}
                                    </TableBody>
                                </Table>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
