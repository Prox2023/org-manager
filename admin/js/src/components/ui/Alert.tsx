import React from 'react';
import { useTheme } from '../../theme/ThemeProvider';

interface AlertProps {
    children: React.ReactNode;
    type?: 'success' | 'error' | 'warning' | 'info';
}

export const Alert: React.FC<AlertProps> = ({ children, type = 'info' }) => {
    const theme = useTheme();
    
    const colors = {
        success: '#4caf50',
        error: '#f44336',
        warning: '#ff9800',
        info: theme.colors.primary
    };
    
    return (
        <div
            style={{
                backgroundColor: `${colors[type]}22`,
                border: `1px solid ${colors[type]}`,
                color: colors[type],
                padding: theme.spacing.medium,
                borderRadius: '4px',
                marginBottom: theme.spacing.medium
            }}
        >
            {children}
        </div>
    );
}; 