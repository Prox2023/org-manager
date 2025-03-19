import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { User } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User Management',
        href: '/admin/users',
        children: {
            title: 'Show',
            href: '/admin/users/1',
        }
    },
];

/*
    * This is the main component of the page.
    * it shows the propery of the user
 */
export default function Index(  {  user } : { user : User }) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="User Management" />
            <div className="container">
                user.name: {user.name}
            </div>
        </AppLayout>
    );
}
