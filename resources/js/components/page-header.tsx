import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Link } from "@inertiajs/react";
import { LucideIcon, Plus } from "lucide-react";

interface PageHeaderProps {
    title: string;
    description: string;
    icon: LucideIcon;
    count: number;
    createRoute?: string;
    createLabel?: string;
}

export function PageHeader({
    title,
    description,
    icon: Icon,
    count,
    createRoute,
    createLabel = "Add New",
}: PageHeaderProps) {
    return (
        <Card>
            <CardHeader>
                <div className="flex items-center justify-between">
                    <CardTitle className="flex items-center gap-2">
                        <Icon className="h-5 w-5" />
                        {title}
                    </CardTitle>
                    {createRoute && (
                        <Button className="flex items-center gap-2" asChild>
                            <Link href={createRoute}>
                                <Plus className="h-4 w-4" />
                                {createLabel}
                            </Link>
                        </Button>
                    )}
                </div>
            </CardHeader>
            <CardContent>
                <div className="flex items-center justify-between">
                    <div className="space-y-1">
                        <h2 className="text-2xl font-bold">{title}</h2>
                        <p className="text-muted-foreground">
                            {description}
                        </p>
                    </div>
                    <div className="flex items-center gap-2">
                        <Badge variant="secondary">
                            {count} Total {title}
                        </Badge>
                    </div>
                </div>
            </CardContent>
        </Card>
    );
} 