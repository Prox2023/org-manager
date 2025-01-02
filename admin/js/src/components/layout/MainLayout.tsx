import React from 'react';
import { useTheme } from '../../theme/ThemeProvider';
import { Link, Outlet } from '@tanstack/react-router';
import { useFeatures } from '../../context/FeaturesContext';

export const MainLayout: React.FC = () => {
    const theme = useTheme();
    const { epics } = useFeatures();
    
    return (
        <div style={{
            display: 'grid',
            gridTemplateColumns: '250px 1fr',
            minHeight: '100vh',
            backgroundColor: theme.colors.background
        }}>
            <aside style={{
                borderRight: `1px solid ${theme.colors.border}`,
                padding: theme.spacing.medium,
                backgroundColor: theme.colors.surface
            }}>
                <nav>
                    <ul style={{ listStyle: 'none', padding: 0, margin: 0 }}>
                        {epics.map(epic => (
                            <li key={epic.id} style={{ marginBottom: theme.spacing.medium }}>
                                <h3 style={{ 
                                    color: theme.colors.primary,
                                    marginBottom: theme.spacing.small 
                                }}>
                                    {epic.name}
                                </h3>
                                <ul style={{ 
                                    listStyle: 'none', 
                                    padding: `0 0 0 ${theme.spacing.medium}` 
                                }}>
                                    {epic.features.map(feature => (
                                        <li key={feature.id}>
                                            <Link 
                                                to="/features/$featureId"
                                                params={{ featureId: feature.id }}
                                                style={{
                                                    display: 'block',
                                                    padding: theme.spacing.small,
                                                    color: theme.colors.text,
                                                    textDecoration: 'none',
                                                    borderRadius: '4px',
                                                    ':hover': {
                                                        backgroundColor: theme.colors.hover
                                                    }
                                                }}
                                            >
                                                {feature.name}
                                            </Link>
                                        </li>
                                    ))}
                                </ul>
                            </li>
                        ))}
                    </ul>
                </nav>
            </aside>
            <main style={{ padding: theme.spacing.large }}>
                <Outlet />
            </main>
        </div>
    );
}; 