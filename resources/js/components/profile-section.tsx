import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge";
import { User } from "@/types";

interface ProfileSectionProps {
    user: User;
    roles: Array<{
        name: string;
        variant: "default" | "destructive" | "outline" | "secondary";
    }>;
}

export function ProfileSection({ user, roles }: ProfileSectionProps) {
    return (
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
    );
} 