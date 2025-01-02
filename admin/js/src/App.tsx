import React from 'react';
import { FeaturesProvider } from './context/FeaturesContext';
import { FeaturesPage } from './components/pages/FeaturesPage';

const App: React.FC = () => {
  return (
    <FeaturesProvider>
      <FeaturesPage />
    </FeaturesProvider>
  );
};

export default App; 