export interface Theme {
  colors: {
    primary: string;
    secondary: string;
    background: string;
    text: string;
    border: string;
    surface: string;
    hover: string;
  };
  spacing: {
    small: string;
    medium: string;
    large: string;
  };
}

export const lightTheme: Theme = {
  colors: {
    primary: '#2271b1',
    secondary: '#135e96',
    background: '#f0f0f1',
    text: '#1e1e1e',
    border: '#c3c4c7',
    surface: '#ffffff',
    hover: '#f0f0f1'
  },
  spacing: {
    small: '8px',
    medium: '16px',
    large: '24px'
  }
}; 