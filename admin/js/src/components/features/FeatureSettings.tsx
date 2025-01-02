import React from 'react';
import { useParams } from '@tanstack/react-router';
import { useFeatures } from '../../context/FeaturesContext';
import { Card } from '../ui/Card';
import { DiscordSettings } from '../discord/DiscordSettings';

export const FeatureSettings: React.FC = () => {
    const { featureId } = useParams({ from: '/features/$featureId' });
    const { epics } = useFeatures();
    
    // Find the feature across all epics
    const feature = epics.flatMap(epic => epic.features)
        .find(f => f.id === featureId);
    
    if (!feature) {
        return <div>Feature not found</div>;
    }

    // Render specific settings component based on feature ID
    const renderSettings = () => {
        switch (featureId) {
            case 'discord-auth':
                return <DiscordSettings />;
            default:
                return (
                    <Card>
                        <h2>{feature.name}</h2>
                        <p>{feature.description}</p>
                    </Card>
                );
        }
    };

    return (
        <div>
            <h1>{feature.name}</h1>
            {renderSettings()}
        </div>
    );
}; 