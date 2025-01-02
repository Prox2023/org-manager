import React from 'react';
import { FeaturesProvider } from './context/FeaturesContext';
import { RouterProvider } from '@tanstack/react-router';
import { router } from './router';

const App: React.FC = () => {
  return (
    <FeaturesProvider>
      <RouterProvider router={router} />
    </FeaturesProvider>
  );
};

export default App; 