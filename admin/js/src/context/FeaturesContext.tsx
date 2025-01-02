import React, { createContext, useContext, useState, useEffect } from 'react';
import { useQuery } from '@tanstack/react-query';

interface Feature {
  id: string;
  name: string;
  description: string;
  tags: string[];
  enabled: boolean;
}

interface Epic {
  id: string;
  name: string;
  tag: string;
  features: Feature[];
}

interface FeaturesContextType {
  epics: Epic[];
  isLoading: boolean;
  error: Error | null;
}

const FeaturesContext = createContext<FeaturesContextType | undefined>(undefined);

export const FeaturesProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const { data: epics = [], isLoading, error } = useQuery({
    queryKey: ['epics'],
    queryFn: async () => {
      const response = await fetch(`${window.orgManagerData.apiUrl}/epics`, {
        headers: {
          'X-WP-Nonce': window.orgManagerData.nonce,
          'Content-Type': 'application/json'
        }
      });
      if (!response.ok) throw new Error('Network response was not ok');
      return response.json();
    }
  });

  return (
    <FeaturesContext.Provider value={{ epics, isLoading, error }}>
      {children}
    </FeaturesContext.Provider>
  );
};

export const useFeatures = () => {
  const context = useContext(FeaturesContext);
  if (context === undefined) {
    throw new Error('useFeatures must be used within a FeaturesProvider');
  }
  return context;
}; 