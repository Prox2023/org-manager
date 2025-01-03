import React, { useState } from 'react';
import { useQuery, useMutation } from '@tanstack/react-query';
import { useTheme } from '../../theme/ThemeProvider';
import { Card } from '../ui/Card';
import { Button } from '../ui/Button';
import { Input } from '../ui/Input';
import { Switch } from '../ui/Switch';
import { Alert } from '../ui/Alert';
import { DynamicForm } from '../form/DynamicForm';

interface DiscordSettings {
    client_id: string;
    client_secret: string;
    redirect_uri: string;
    registration_enabled: boolean;
    allowed_roles: string[];
    fields: any[];
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
        mutationFn: async (formData: Record<string, any>) => {
            console.log('Saving settings:', formData);
            const response = await fetch(`${window.orgManagerData.apiUrl}/discord/settings`, {
                method: 'POST',
                headers: {
                    'X-WP-Nonce': window.orgManagerData.nonce,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            console.log('Save response status:', response.status);
            const data = await response.json();
            console.log('Save response data:', data);
            if (!response.ok) {
                throw new Error(data.message || 'Failed to save settings');
            }
            return data;
        },
        onSuccess: (data) => {
            console.log('Settings saved successfully:', data);
            setAlertType('success');
            setAlertMessage('Settings saved successfully');
            setShowAlert(true);
            setTimeout(() => setShowAlert(false), 3000);
        },
        onError: (error: Error) => {
            console.error('Error saving settings:', error);
            setAlertType('error');
            setAlertMessage(error.message);
            setShowAlert(true);
        }
    });

    if (isLoading) {
        return <div>Loading...</div>;
    }

    console.log('Current settings:', settings);

    return (
        <div style={{ padding: theme.spacing.large }}>
            <Card>
                <h2>Discord Integration Settings</h2>
                {showAlert && (
                    <Alert type={alertType}>
                        {alertMessage}
                    </Alert>
                )}
                <DynamicForm
                    fields={settings?.fields ?? []}
                    onSubmit={(data) => {
                        console.log('Form submitted with data:', data);
                        saveSettings(data);
                    }}
                    isLoading={isSaving}
                    initialValues={settings}
                />
            </Card>
        </div>
    );
}; 