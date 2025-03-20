import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { User } from '@/types';
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { MapPin, Users, Trophy, MessageSquare, Shield, Key, Lock, KeyRound, Star, Award, Medal } from "lucide-react";
import { SectionCard } from '@/components/section-card';
import { ProfileSection } from '@/components/profile-section';
import { SecuritySettings, type SecuritySetting } from '@/components/security-settings';
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";

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

    const securitySettings: SecuritySetting[] = [
        {
            icon: Key,
            title: 'Password Reset',
            description: 'Force a password reset for this user',
            action: {
                label: 'Reset Password',
                onClick: () => console.log('Reset password'),
            },
        },
        {
            icon: Lock,
            title: 'Two Factor Authentication',
            description: 'Reset 2FA for this user',
            action: {
                label: 'Reset 2FA',
                onClick: () => console.log('Reset 2FA'),
            },
        },
        {
            icon: KeyRound,
            title: '2FA Provider',
            description: 'Configured: Google Authenticator',
            status: {
                label: 'Configured',
                variant: 'secondary',
            },
            action: {
                label: 'Change Provider',
                onClick: () => console.log('Change provider'),
            },
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${user.name}'s Profile`} />
            <div className="container mx-auto px-4 py-8 max-w-7xl">
                <div className="space-y-6">
                    <SectionCard title="Profile Information" icon={Users} className="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/50 dark:to-indigo-950/50">
                        <ProfileSection user={user} roles={roles} />
                    </SectionCard>

                    <SectionCard title="Security Settings" icon={Shield} className="bg-gradient-to-br from-red-50 to-orange-50 dark:from-red-950/50 dark:to-orange-950/50">
                        <SecuritySettings settings={securitySettings} />
                    </SectionCard>

                    <SectionCard title="Discord Integration" icon={MessageSquare} className="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-950/50 dark:to-pink-950/50">
                        <div className="flex items-center space-x-4">
                            <Avatar className="h-12 w-12 ring-2 ring-purple-500/20">
                                <AvatarImage src="https://cdn.discordapp.com/avatars/123456789/john_prox.png" alt="Discord Avatar" />
                                <AvatarFallback className="bg-purple-100 dark:bg-purple-900">JP</AvatarFallback>
                            </Avatar>
                            <div className="space-y-1">
                                <div className="flex items-center gap-2">
                                    <h3 className="font-semibold">john prox</h3>
                                    <Badge variant="secondary" className="bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300">Connected</Badge>
                                </div>
                                <p className="text-sm text-muted-foreground">Discord ID: 123456789</p>
                            </div>
                        </div>
                    </SectionCard>

                    <SectionCard title="Location Information" icon={MapPin} className="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-950/50 dark:to-emerald-950/50">
                        <div className="space-y-2">
                            <p className="text-muted-foreground">Country: Netherlands</p>
                            <p className="text-muted-foreground">City: Emmen</p>
                        </div>
                    </SectionCard>

                    <SectionCard title="Rank Information" icon={Trophy} className="bg-gradient-to-br from-yellow-50 to-amber-50 dark:from-yellow-950/50 dark:to-amber-950/50">
                        <div className="grid gap-4 md:grid-cols-3">
                            <div className="space-y-2">
                                <h3 className="font-semibold">My Rank</h3>
                                <div className="flex items-center gap-2">
                                    <Star className="h-6 w-6 text-yellow-500" />
                                    <p className="text-2xl font-bold">#42</p>
                                </div>
                            </div>
                            <div className="space-y-2">
                                <h3 className="font-semibold">Team Members</h3>
                                <div className="flex items-center gap-2">
                                    <Award className="h-6 w-6 text-amber-500" />
                                    <p className="text-2xl font-bold">12</p>
                                </div>
                            </div>
                            <div className="space-y-2">
                                <h3 className="font-semibold">People in Command</h3>
                                <div className="flex items-center gap-2">
                                    <Medal className="h-6 w-6 text-orange-500" />
                                    <p className="text-2xl font-bold">5</p>
                                </div>
                            </div>
                        </div>
                    </SectionCard>

                    <SectionCard title="Team Overview" icon={Users} className="bg-gradient-to-br from-cyan-50 to-teal-50 dark:from-cyan-950/50 dark:to-teal-950/50">
                        <div className="space-y-4">
                            <div className="flex items-center justify-between">
                                <div className="space-y-1">
                                    <p className="font-medium">Team Name</p>
                                    <p className="text-sm text-muted-foreground">Dutch Navy Seals</p>
                                </div>
                                <div className="text-right">
                                    <p className="font-medium">Role</p>
                                    <Badge variant="secondary" className="bg-cyan-100 text-cyan-700 dark:bg-cyan-900 dark:text-cyan-300">Org Leader</Badge>
                                </div>
                            </div>
                        </div>
                    </SectionCard>
                </div>
            </div>
        </AppLayout>
    );
}
