import React from 'react';
import { useTheme } from '../../theme/ThemeProvider';

interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
    variant?: 'primary' | 'secondary';
}

export const Button: React.FC<ButtonProps> = ({ 
    children, 
    variant = 'primary', 
    className = '',
    ...props 
}) => {
    const theme = useTheme();
    
    return (
        <button
            className={className}
            style={{
                backgroundColor: variant === 'primary' ? theme.colors.primary : theme.colors.secondary,
                color: '#fff',
                border: 'none',
                borderRadius: '4px',
                padding: `${theme.spacing.small} ${theme.spacing.medium}`,
                cursor: 'pointer',
                opacity: props.disabled ? 0.7 : 1
            }}
            {...props}
        >
            {children}
        </button>
    );
}; 