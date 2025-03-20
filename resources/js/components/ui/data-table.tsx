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

export interface Column<T> {
    header: string;
    accessor: keyof T | ((item: T) => ReactNode);
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
}

export function DataTable<T extends { id: number }>({
    columns,
    data,
    onDelete,
    editRoute,
    viewRoute,
    actions,
}: DataTableProps<T>) {
    return (
        <Table>
            <TableHeader>
                <TableRow>
                    {columns.map((column) => (
                        <TableHead key={column.header}>{column.header}</TableHead>
                    ))}
                    {(actions || onDelete || editRoute || viewRoute) && (
                        <TableHead className="text-right">Actions</TableHead>
                    )}
                </TableRow>
            </TableHeader>
            <TableBody>
                {data.map((item) => (
                    <TableRow key={item.id}>
                        {columns.map((column) => (
                            <TableCell key={column.header}>
                                {typeof column.accessor === 'function'
                                    ? column.accessor(item)
                                    : String(item[column.accessor])}
                            </TableCell>
                        ))}
                        {(actions || onDelete || editRoute || viewRoute) && (
                            <TableCell className="text-right">
                                <div className="flex justify-end gap-2">
                                    {viewRoute && (
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            asChild
                                        >
                                            <Link href={viewRoute(item.id)}>
                                                <SearchIcon className="h-4 w-4" />
                                            </Link>
                                        </Button>
                                    )}
                                    {editRoute && (
                                        <Button
                                            variant="ghost"
                                            size="icon"
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
                                                    size="icon"
                                                    className="text-destructive hover:text-destructive"
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
                                            size="icon"
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
    );
} 