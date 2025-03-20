import React from "react";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Link } from "@inertiajs/react";
import { LucideIcon, SearchIcon, PencilIcon, TrashIcon } from "lucide-react";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
import { ReactNode } from "react";
import { Pagination } from "@/components/ui/pagination";

export interface Column<T> {
    header: string | (() => React.ReactNode);
    accessor: keyof T | ((item: T) => React.ReactNode);
}

interface DataTableProps<T> {
    columns: Column<T>[];
    data: T[];
    onDelete?: (id: number) => void;
    editRoute?: (id: number) => string;
    viewRoute?: (id: number) => string;
    actions?: {
        icon: LucideIcon;
        label: string;
        onClick: (item: T) => void;
        variant?: "ghost" | "destructive";
    }[];
    currentPage: number;
    lastPage: number;
    total: number;
    perPage: number;
    onPageChange: (page: number) => void;
}

export function DataTable<T extends { id: number }>({
    columns,
    data,
    onDelete,
    editRoute,
    viewRoute,
    actions,
    currentPage,
    lastPage,
    total,
    perPage,
    onPageChange,
}: DataTableProps<T>) {
    return (
        <div className="space-y-4">
            <div className="text-sm text-muted-foreground">
                Showing {((currentPage - 1) * perPage) + 1} to {Math.min(currentPage * perPage, total)} of {total} items
            </div>

            <Table>
                <TableHeader>
                    <TableRow>
                        {columns.map((column, index) => (
                            <TableHead key={index}>
                                {typeof column.header === 'function' ? column.header() : column.header}
                            </TableHead>
                        ))}
                        {(onDelete || viewRoute) && <TableHead className="w-[100px]">Actions</TableHead>}
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {data.map((item) => (
                        <TableRow key={item.id}>
                            {columns.map((column, index) => (
                                <TableCell key={index}>
                                    {typeof column.accessor === 'function'
                                        ? column.accessor(item)
                                        : String(item[column.accessor])}
                                </TableCell>
                            ))}
                            {(onDelete || viewRoute) && (
                                <TableCell>
                                    <div className="flex items-center gap-2">
                                        {viewRoute && (
                                            <Link href={viewRoute(item.id)}>
                                                <Button variant="ghost" size="sm">
                                                    <SearchIcon className="h-4 w-4" />
                                                </Button>
                                            </Link>
                                        )}
                                        {editRoute && (
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                asChild
                                            >
                                                <Link href={editRoute(item.id)}>
                                                    <PencilIcon className="h-4 w-4" />
                                                </Link>
                                            </Button>
                                        )}
                                        {onDelete && (
                                            <AlertDialog>
                                                <AlertDialogTrigger asChild>
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        className="text-destructive"
                                                    >
                                                        <TrashIcon className="h-4 w-4" />
                                                    </Button>
                                                </AlertDialogTrigger>
                                                <AlertDialogContent>
                                                    <AlertDialogHeader>
                                                        <AlertDialogTitle>Are you sure?</AlertDialogTitle>
                                                        <AlertDialogDescription>
                                                            This action cannot be undone. This will permanently delete the item
                                                            and remove their data from our servers.
                                                        </AlertDialogDescription>
                                                    </AlertDialogHeader>
                                                    <AlertDialogFooter>
                                                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                                                        <AlertDialogAction
                                                            onClick={() => onDelete(item.id)}
                                                            className="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                                                        >
                                                            Delete
                                                        </AlertDialogAction>
                                                    </AlertDialogFooter>
                                                </AlertDialogContent>
                                            </AlertDialog>
                                        )}
                                        {actions?.map((action, index) => (
                                            <Button
                                                key={index}
                                                variant={action.variant || "ghost"}
                                                size="sm"
                                                onClick={() => action.onClick(item)}
                                            >
                                                <action.icon className="h-4 w-4" />
                                            </Button>
                                        ))}
                                    </div>
                                </TableCell>
                            )}
                        </TableRow>
                    ))}
                </TableBody>
            </Table>

            <div className="flex justify-center">
                <Pagination
                    currentPage={currentPage}
                    lastPage={lastPage}
                    onPageChange={onPageChange}
                />
            </div>
        </div>
    );
} 