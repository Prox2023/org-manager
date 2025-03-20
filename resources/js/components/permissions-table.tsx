import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { Key, Plus, Trash2, CheckCircle2, XCircle } from "lucide-react";
import { Permission } from "@/types";
import { Switch } from "@/components/ui/switch";
import { Pagination } from "@/components/ui/pagination";

interface PermissionsTableProps {
    permissions: Permission[];
    selectedPermissions: Permission[];
    onTogglePermission: (permission: Permission) => void;
    onAddPermission: () => void;
    currentPage: number;
    lastPage: number;
    total: number;
    perPage: number;
    onPageChange: (page: number) => void;
}

export function PermissionsTable({
    permissions,
    selectedPermissions,
    onTogglePermission,
    onAddPermission,
    currentPage,
    lastPage,
    total,
    perPage,
    onPageChange,
}: PermissionsTableProps) {
    return (
        <div className="space-y-4">
            <div className="flex justify-between items-center">
                <div className="text-sm text-muted-foreground">
                    Showing {((currentPage - 1) * perPage) + 1} to {Math.min(currentPage * perPage, total)} of {total} permissions
                </div>
                <Button onClick={onAddPermission} size="sm">
                    <Plus className="h-4 w-4 mr-2" />
                    Add Permission
                </Button>
            </div>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Name</TableHead>
                        <TableHead>Guard</TableHead>
                        <TableHead className="w-[100px]">Enabled</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {permissions.map((permission) => (
                        <TableRow key={permission.id}>
                            <TableCell>{permission.name}</TableCell>
                            <TableCell>{permission.guard_name}</TableCell>
                            <TableCell>
                                <Switch
                                    checked={selectedPermissions.some(p => p.id === permission.id)}
                                    onCheckedChange={() => onTogglePermission(permission)}
                                />
                            </TableCell>
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