export interface Theme {
  colors: {
    primary: string;
    secondary: string;
    background: string;
    text: string;
  };
  spacing: {
    small: string;
    medium: string;
    large: string;
  };
}

export const defaultTheme: Theme = {
  colors: {
    primary: '#007cba',
    secondary: '#455a64',
    background: '#ffffff',
    text: '#1e1e1e',
  },
  spacing: {
    small: '8px',
    medium: '16px',
    large: '24px',
  },
}; 