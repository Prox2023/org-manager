import React from 'react';
import { Input } from '../ui/Input';
import { Switch } from '../ui/Switch';
import { Button } from '../ui/Button';
import { useTheme } from '../../theme/ThemeProvider';

interface Field {
    type: string;
    name: string;
    label: string;
    description?: string;
    defaultValue?: any;
    options?: any[];
}

interface DynamicFormProps {
    fields: Field[];
    onSubmit: (data: any) => void;
    isLoading?: boolean;
    initialValues?: Record<string, any>;
}

export const DynamicForm: React.FC<DynamicFormProps> = ({
    fields,
    onSubmit,
    isLoading = false,
    initialValues = {}
}) => {
    const theme = useTheme();

    const renderField = (field: Field) => {
        const value = initialValues[field.name] ?? field.defaultValue;

        switch (field.type) {
            case 'switch':
                return (
                    <Switch
                        key={field.name}
                        label={field.label}
                        name={field.name}
                        defaultChecked={value}
                    />
                );
            case 'select':
                return (
                    <select
                        name={field.name}
                        defaultValue={value}
                        style={{
                            width: '100%',
                            padding: theme.spacing.small,
                            borderRadius: '4px',
                            border: `1px solid ${theme.colors.border}`
                        }}
                    >
                        {field.options?.map(option => (
                            <option key={option.value} value={option.value}>
                                {option.label}
                            </option>
                        ))}
                    </select>
                );
            default:
                return (
                    <Input
                        key={field.name}
                        label={field.label}
                        name={field.name}
                        type={field.type}
                        defaultValue={value}
                    />
                );
        }
    };

    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        const formData = new FormData(e.currentTarget);
        const data: Record<string, any> = {};
        
        fields.forEach(field => {
            if (field.type === 'switch') {
                data[field.name] = formData.get(field.name) === 'true';
            } else {
                data[field.name] = formData.get(field.name);
            }
        });

        onSubmit(data);
    };

    return (
        <form onSubmit={handleSubmit}>
            {fields.map(field => (
                <div key={field.name} style={{ marginBottom: theme.spacing.medium }}>
                    {renderField(field)}
                    {field.description && (
                        <p style={{ 
                            fontSize: '0.8em',
                            color: theme.colors.text,
                            marginTop: '4px'
                        }}>
                            {field.description}
                        </p>
                    )}
                </div>
            ))}
            <Button type="submit" disabled={isLoading}>
                {isLoading ? 'Saving...' : 'Save Settings'}
            </Button>
        </form>
    );
}; 