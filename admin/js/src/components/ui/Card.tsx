import React from 'react';
import { useTheme } from '../../theme/ThemeProvider';

interface CardProps {
    children: React.ReactNode;
    className?: string;
}

export const Card: React.FC<CardProps> = ({ children, className = '' }) => {
    const theme = useTheme();
    
    return (
        <div
            className={className}
            style={{
                backgroundColor: theme.colors.background,
                borderRadius: '8px',
                padding: theme.spacing.large,
                boxShadow: '0 1px 3px rgba(0,0,0,0.1)'
            }}
        >
            {children}
        </div>
    );
}; 