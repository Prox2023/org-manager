import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { User } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge";
import { MapPin, Users, Trophy, UserCog, MessageSquare } from "lucide-react";

/*
    * This is the main component of the page.
    * it shows the propery of the user
 */
export default function Show({ user }: { user: User }) {
    // Example roles - replace with actual user roles from backend
    const roles = [
        { name: 'Admin', variant: 'destructive' as const },
        { name: 'Org Leader', variant: 'default' as const },
    ];

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'User Management',
            href: '/admin/users',
            children: {
                title: 'User Info',
                href: '/admin/users',
                children: {
                    title: user.name,
                    href: `/admin/users/${user.id}`,
                }
            }
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${user.name}'s Profile`} />
            <div className="container mx-auto px-4 py-8 max-w-7xl">
                <div className="space-y-6">
                    {/* Profile Section */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Profile Information</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="flex items-center space-x-4">
                                <Avatar className="h-20 w-20">
                                    <AvatarImage src={user.avatar} alt={user.name} />
                                    <AvatarFallback>{user.name.charAt(0)}</AvatarFallback>
                                </Avatar>
                                <div className="space-y-2">
                                    <div className="space-y-1">
                                        <h2 className="text-2xl font-bold">{user.name}</h2>
                                        <p className="text-muted-foreground">{user.email}</p>
                                    </div>
                                    <div className="flex flex-wrap gap-2">
                                        {roles.map((role) => (
                                            <Badge key={role.name} variant={role.variant}>
                                                {role.name}
                                            </Badge>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Discord Integration Section */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <MessageSquare className="h-5 w-5" />
                                Discord Integration
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="flex items-center space-x-4">
                                <Avatar className="h-12 w-12">
                                    <AvatarImage src="https://cdn.discordapp.com/avatars/123456789/john_prox.png" alt="Discord Avatar" />
                                    <AvatarFallback>JP</AvatarFallback>
                                </Avatar>
                                <div className="space-y-1">
                                    <div className="flex items-center gap-2">
                                        <h3 className="font-semibold">john prox</h3>
                                        <Badge variant="secondary">Connected</Badge>
                                    </div>
                                    <p className="text-sm text-muted-foreground">Discord ID: 123456789</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Location Section */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <MapPin className="h-5 w-5" />
                                Location Information
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-2">
                                <p className="text-muted-foreground">Country: Netherlands</p>
                                <p className="text-muted-foreground">City: Emmen</p>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Rank Section */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Trophy className="h-5 w-5" />
                                Rank Information
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="grid gap-4 md:grid-cols-3">
                                <div className="space-y-2">
                                    <h3 className="font-semibold">My Rank</h3>
                                    <p className="text-2xl font-bold">#42</p>
                                </div>
                                <div className="space-y-2">
                                    <h3 className="font-semibold">Team Members</h3>
                                    <p className="text-2xl font-bold">12</p>
                                </div>
                                <div className="space-y-2">
                                    <h3 className="font-semibold">People in Command</h3>
                                    <p className="text-2xl font-bold">5</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Team Section */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Users className="h-5 w-5" />
                                Team Overview
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                <div className="flex items-center justify-between">
                                    <div className="space-y-1">
                                        <p className="font-medium">Team Name</p>
                                        <p className="text-sm text-muted-foreground">Dutch Navy Seals</p>
                                    </div>
                                    <div className="text-right">
                                        <p className="font-medium">Role</p>
                                        <p className="text-sm text-muted-foreground">Org Leader</p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
