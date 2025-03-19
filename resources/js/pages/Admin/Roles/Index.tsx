import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { Role } from '@/types';
import { buttonVariants, Button } from "@/components/ui/button"
import { Link } from '@inertiajs/react';
import { PencilIcon, SearchIcon, TrashIcon } from 'lucide-react';


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Role Management',
        href: '/admin/roles',
    },
];

export default function Index({ roles} : { roles: Role[] }) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Role Management" />
            <Button>Some button</Button>
            <div>
                <Table>
                    <TableCaption>User Management</TableCaption>
                    <TableHeader title="Role Management">
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Created At</TableHead>
                            <TableHead>Updated At</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {roles.map((role) => (
                            <TableRow key={role.id}>
                                <TableCell>{role.name}</TableCell>
                                <TableCell>{role.guard_name}</TableCell>
                                <TableCell>{role.created_at}</TableCell>
                                <TableCell>{role.updated_at}</TableCell>
                                <TableCell>
                                    <Link href={`/admin/roles/${role.id}`}
                                          className={buttonVariants({ variant: "view", size: "sm" })}
                                    ><SearchIcon/></Link>
                                    <Link href={`/admin/roles/${role.id}/edit`}
                                          className={buttonVariants({ variant: "edit", size: "sm" })}
                                    ><PencilIcon/></Link>
                                    <Link href={`/admin/roles/${role.id}/delete`}
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
