import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { Shield } from "lucide-react";
import { PageHeader } from '@/components/page-header';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { useState } from "react";
import { Label } from "@/components/ui/label";

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Role Management',
        href: route('admin.roles.index'),
        children: {
            title: 'Create Role',
            href: route('admin.roles.create'),
        }
    },
];

export default function Create() {
    const [name, setName] = useState('');
    const [guardName, setGuardName] = useState('web');

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        router.post(route('admin.roles.store'), {
            name,
            guard_name: guardName,
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Role" />
            <div className="container mx-auto px-4 py-8 max-w-7xl">
                <div className="space-y-6">
                    <PageHeader
                        title="Create Role"
                        description="Create a new role and assign permissions"
                        icon={Shield}
                        count={0}
                    />

                    <Card>
                        <CardHeader>
                            <CardTitle>Role Details</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={handleSubmit} className="space-y-4">
                                <div className="space-y-2">
                                    <Label htmlFor="name">Name</Label>
                                    <Input
                                        id="name"
                                        value={name}
                                        onChange={(e) => setName(e.target.value)}
                                        placeholder="Enter role name"
                                    />
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="guard_name">Guard Name</Label>
                                    <Input
                                        id="guard_name"
                                        value={guardName}
                                        onChange={(e) => setGuardName(e.target.value)}
                                        placeholder="Enter guard name"
                                    />
                                </div>
                                <Button type="submit">Create Role</Button>
                            </form>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
} 