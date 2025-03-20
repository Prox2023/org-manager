import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Key, Lock, KeyRound } from "lucide-react";

export interface SecuritySetting {
    icon: typeof Key | typeof Lock | typeof KeyRound;
    title: string;
    description: string;
    action?: {
        label: string;
        onClick: () => void;
    };
    status?: {
        label: string;
        variant: "default" | "destructive" | "outline" | "secondary";
    };
}

interface SecuritySettingsProps {
    settings: SecuritySetting[];
}

export function SecuritySettings({ settings }: SecuritySettingsProps) {
    return (
        <div className="space-y-6">
            {settings.map((setting, index) => (
                <div key={index} className="flex items-center justify-between">
                    <div className="space-y-1">
                        <div className="flex items-center gap-2">
                            <setting.icon className="h-4 w-4 text-muted-foreground" />
                            <h3 className="font-medium">{setting.title}</h3>
                        </div>
                        <p className="text-sm text-muted-foreground">
                            {setting.description}
                        </p>
                    </div>
                    <div className="flex items-center gap-2">
                        {setting.status && (
                            <Badge variant={setting.status.variant}>
                                {setting.status.label}
                            </Badge>
                        )}
                        {setting.action && (
                            <Button
                                variant="outline"
                                size="sm"
                                onClick={setting.action.onClick}
                            >
                                {setting.action.label}
                            </Button>
                        )}
                    </div>
                </div>
            ))}
        </div>
    );
} 