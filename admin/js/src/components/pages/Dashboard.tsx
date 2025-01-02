import React from 'react';
import { useFeatures } from '../../context/FeaturesContext';
import { Card } from '../ui/Card';

export const Dashboard: React.FC = () => {
    const { epics } = useFeatures();
    
    return (
        <div>
            <h1>Organization Manager</h1>
            <div style={{ 
                display: 'grid', 
                gridTemplateColumns: 'repeat(auto-fill, minmax(300px, 1fr))',
                gap: '20px',
                marginTop: '20px'
            }}>
                {epics.map(epic => (
                    <Card key={epic.id}>
                        <h2>{epic.name}</h2>
                        <p>Features: {epic.features.length}</p>
                        <ul>
                            {epic.features.map(feature => (
                                <li key={feature.id}>{feature.name}</li>
                            ))}
                        </ul>
                    </Card>
                ))}
            </div>
        </div>
    );
}; 