import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { LucideIcon } from "lucide-react";
import { ReactNode } from "react";

interface SectionCardProps {
    title: string;
    icon: LucideIcon;
    children: ReactNode;
    action?: ReactNode;
    className?: string;
}

export function SectionCard({ title, icon: Icon, children, action, className }: SectionCardProps) {
    return (
        <Card className={className}>
            <CardHeader>
                <div className="flex items-center justify-between">
                    <CardTitle className="flex items-center gap-2">
                        <Icon className="h-5 w-5" />
                        {title}
                    </CardTitle>
                    {action}
                </div>
            </CardHeader>
            <CardContent>
                {children}
            </CardContent>
        </Card>
    );
} 