import React from 'react';
import { useTheme } from '../../theme/ThemeProvider';

interface InputProps extends React.InputHTMLAttributes<HTMLInputElement> {
    label: string;
}

export const Input: React.FC<InputProps> = ({ label, className = '', ...props }) => {
    const theme = useTheme();
    
    return (
        <div style={{ marginBottom: theme.spacing.medium }}>
            <label
                style={{
                    display: 'block',
                    marginBottom: theme.spacing.small,
                    color: theme.colors.text
                }}
            >
                {label}
            </label>
            <input
                className={className}
                style={{
                    width: '100%',
                    padding: theme.spacing.small,
                    borderRadius: '4px',
                    border: `1px solid ${theme.colors.secondary}`,
                    fontSize: '14px'
                }}
                {...props}
            />
        </div>
    );
}; 