import React, { useState } from 'react';
import { useQuery, useMutation } from '@tanstack/react-query';
import { useTheme } from '../../theme/ThemeProvider';
import { Card } from '../ui/Card';
import { Button } from '../ui/Button';
import { Input } from '../ui/Input';
import { Switch } from '../ui/Switch';
import { Alert } from '../ui/Alert';

interface DiscordSettings {
    client_id: string;
    client_secret: string;
    redirect_uri: string;
    registration_enabled: boolean;
    allowed_roles: string[];
}

export const DiscordSettings: React.FC = () => {
    const theme = useTheme();
    const [showAlert, setShowAlert] = useState(false);
    const [alertMessage, setAlertMessage] = useState('');
    const [alertType, setAlertType] = useState<'success' | 'error'>('success');
    
    const { data: settings, isLoading } = useQuery({
        queryKey: ['discord', 'settings'],
        queryFn: async () => {
            const response = await fetch(`${window.orgManagerData.apiUrl}/discord/settings`, {
                headers: {
                    'X-WP-Nonce': window.orgManagerData.nonce
                }
            });
            console.log('Response status:', response.status);
            const data = await response.json();
            console.log('Response data:', data);
            if (!response.ok) throw new Error('Failed to fetch settings');
            return data as DiscordSettings;
        }
    });

    const { mutate: saveSettings, isLoading: isSaving } = useMutation({
        mutationFn: async (newSettings: DiscordSettings) => {
            console.log('Saving settings:', newSettings);
            const response = await fetch(`${window.orgManagerData.apiUrl}/discord/settings`, {
                method: 'POST',
                headers: {
                    'X-WP-Nonce': window.orgManagerData.nonce,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(newSettings)
            });
            console.log('Save response status:', response.status);
            const data = await response.json();
            console.log('Save response data:', data);
            if (!response.ok) {
                throw new Error(data.message || 'Failed to save settings');
            }
            return data;
        },
        onSuccess: () => {
            setAlertType('success');
            setAlertMessage('Settings saved successfully');
            setShowAlert(true);
            setTimeout(() => setShowAlert(false), 3000);
        },
        onError: (error: Error) => {
            setAlertType('error');
            setAlertMessage(error.message);
            setShowAlert(true);
        }
    });

    if (isLoading) {
        return <div>Loading...</div>;
    }

    const defaultRedirectUri = `${window.location.origin}/wp-json/org-manager/v1/discord/auth`;

    return (
        <div style={{ padding: theme.spacing.large }}>
            <Card>
                <h2>Discord Integration Settings</h2>
                {showAlert && (
                    <Alert type={alertType}>
                        {alertMessage}
                    </Alert>
                )}
                <form onSubmit={(e) => {
                    e.preventDefault();
                    const formData = new FormData(e.currentTarget);
                    saveSettings({
                        client_id: formData.get('client_id') as string,
                        client_secret: formData.get('client_secret') as string,
                        redirect_uri: formData.get('redirect_uri') as string || defaultRedirectUri,
                        registration_enabled: formData.get('registration_enabled') === 'true',
                        allowed_roles: []
                    });
                }}>
                    <div style={{ marginBottom: theme.spacing.medium }}>
                        <Input
                            label="Client ID"
                            name="client_id"
                            defaultValue={settings?.client_id}
                            required
                        />
                    </div>
                    <div style={{ marginBottom: theme.spacing.medium }}>
                        <Input
                            label="Client Secret"
                            name="client_secret"
                            type="password"
                            defaultValue={settings?.client_secret}
                            required
                        />
                    </div>
                    <div style={{ marginBottom: theme.spacing.medium }}>
                        <Input
                            label="Redirect URI"
                            name="redirect_uri"
                            defaultValue={settings?.redirect_uri || defaultRedirectUri}
                            placeholder={defaultRedirectUri}
                            required
                        />
                        <p style={{ 
                            fontSize: '0.8em', 
                            color: theme.colors.text,
                            marginTop: '4px' 
                        }}>
                            Add this URL to your Discord application's OAuth2 redirect URLs
                        </p>
                    </div>
                    <div style={{ marginBottom: theme.spacing.medium }}>
                        <Switch
                            label="Enable Registration"
                            name="registration_enabled"
                            defaultChecked={settings?.registration_enabled}
                        />
                    </div>
                    <Button type="submit" disabled={isSaving}>
                        {isSaving ? 'Saving...' : 'Save Settings'}
                    </Button>
                </form>
            </Card>
        </div>
    );
}; 