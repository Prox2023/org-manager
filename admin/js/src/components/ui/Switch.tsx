import React from 'react';
import { useTheme } from '../../theme/ThemeProvider';

interface SwitchProps {
    label: string;
    name: string;
    defaultChecked?: boolean;
    onChange?: (checked: boolean) => void;
}

export const Switch: React.FC<SwitchProps> = ({ 
    label, 
    name, 
    defaultChecked = false,
    onChange 
}) => {
    const theme = useTheme();
    
    return (
        <label
            style={{
                display: 'flex',
                alignItems: 'center',
                cursor: 'pointer'
            }}
        >
            <input
                type="checkbox"
                name={name}
                defaultChecked={defaultChecked}
                onChange={(e) => onChange?.(e.target.checked)}
                style={{
                    marginRight: theme.spacing.small
                }}
            />
            <span>{label}</span>
        </label>
    );
}; 