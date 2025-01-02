import React from 'react';
import { useFeatures } from '../../context/FeaturesContext';
import { useTheme } from '../../theme/ThemeProvider';

export const FeaturesPage: React.FC = () => {
  const { epics, isLoading, error } = useFeatures();
  const theme = useTheme();

  if (isLoading) return <div>Loading...</div>;
  if (error) return <div>Error: {error.message}</div>;

  return (
    <div style={{ padding: theme.spacing.large }}>
      <h1>Features</h1>
      {epics.map(epic => (
        <div key={epic.id} style={{ marginBottom: theme.spacing.large }}>
          <h2>{epic.name}</h2>
          <div style={{ display: 'grid', gap: theme.spacing.medium, gridTemplateColumns: 'repeat(auto-fill, minmax(300px, 1fr))' }}>
            {epic.features.map(feature => (
              <div 
                key={feature.id}
                style={{
                  padding: theme.spacing.medium,
                  backgroundColor: theme.colors.background,
                  borderRadius: '4px',
                  boxShadow: '0 1px 3px rgba(0,0,0,0.1)'
                }}
              >
                <h3>{feature.name}</h3>
                <p>{feature.description}</p>
                <div>
                  {feature.tags.map(tag => (
                    <span 
                      key={tag}
                      style={{
                        backgroundColor: theme.colors.primary,
                        color: '#fff',
                        padding: '2px 8px',
                        borderRadius: '12px',
                        fontSize: '12px',
                        marginRight: '4px'
                      }}
                    >
                      {tag}
                    </span>
                  ))}
                </div>
              </div>
            ))}
          </div>
        </div>
      ))}
    </div>
  );
}; 