import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { User } from '@/types';
import { buttonVariants } from '@/components/ui/button';
import { PencilIcon, SearchIcon, TrashIcon } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User Management',
        href: '/admin/users',
    },
];

export default function Index({ users} : { users: User[] }) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="User Management" />
            <div>
                <Table>
                    <TableCaption>User Management</TableCaption>
                    <TableHeader title="User Management">
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Created At</TableHead>
                            <TableHead>Updated At</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {users.map((user) => (
                            <TableRow key={user.id}>
                                <TableCell>{user.name}</TableCell>
                                <TableCell>{user.email}</TableCell>
                                <TableCell>{user.created_at}</TableCell>
                                <TableCell>{user.updated_at}</TableCell>
                                <TableCell>
                                    <Link href={`/admin/users/${user.id}`}
                                          className={buttonVariants({ variant: "view", size: "sm" })}
                                    ><SearchIcon/></Link>
                                    <Link href={`/admin/users/${user.id}/edit`}
                                          className={buttonVariants({ variant: "edit", size: "sm" })}
                                    ><PencilIcon/></Link>
                                    <Link href={`/admin/users/${user.id}/delete`}
                                          className={buttonVariants({ variant: "destructive", size: "sm" })}
                                    ><TrashIcon/></Link>
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>
        </AppLayout>
    );
}
