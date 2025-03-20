import { Button } from "@/components/ui/button";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Plus } from "lucide-react";
import { useState } from "react";
import { useForm } from "@inertiajs/react";

interface RoleCreateModalProps {
    onSuccess?: () => void;
}

export function RoleCreateModal({ onSuccess }: RoleCreateModalProps) {
    const [open, setOpen] = useState(false);
    const { data, setData, post, processing, errors, reset } = useForm({
        name: "",
        guard_name: "web",
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post("/admin/roles", {
            onSuccess: () => {
                reset();
                setOpen(false);
                onSuccess?.();
            },
        });
    };

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button size="sm" className="flex items-center gap-2">
                    <Plus className="h-4 w-4" />
                    Add Role
                </Button>
            </DialogTrigger>
            <DialogContent>
                <form onSubmit={handleSubmit}>
                    <DialogHeader>
                        <DialogTitle>Create New Role</DialogTitle>
                        <DialogDescription>
                            Add a new role to your application. Roles can be assigned to users and have specific permissions.
                        </DialogDescription>
                    </DialogHeader>
                    <div className="grid gap-4 py-4">
                        <div className="grid gap-2">
                            <Label htmlFor="name">Role Name</Label>
                            <Input
                                id="name"
                                value={data.name}
                                onChange={(e) => setData("name", e.target.value)}
                                placeholder="Enter role name"
                                disabled={processing}
                            />
                            {errors.name && (
                                <p className="text-sm text-destructive">{errors.name}</p>
                            )}
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="guard_name">Guard Name</Label>
                            <Input
                                id="guard_name"
                                value={data.guard_name}
                                onChange={(e) => setData("guard_name", e.target.value)}
                                placeholder="web"
                                disabled={processing}
                            />
                            {errors.guard_name && (
                                <p className="text-sm text-destructive">{errors.guard_name}</p>
                            )}
                        </div>
                    </div>
                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => setOpen(false)}
                            disabled={processing}
                        >
                            Cancel
                        </Button>
                        <Button type="submit" disabled={processing}>
                            {processing ? "Creating..." : "Create Role"}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
} 