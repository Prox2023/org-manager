import { useQuery } from '@tanstack/react-query';

const fetchData = async () => {
  const response = await fetch('/wp-json/your-plugin/v1/data');
  if (!response.ok) {
    throw new Error('Network response was not ok');
  }
  return response.json();
};

export const useData = () => {
  return useQuery({
    queryKey: ['data'],
    queryFn: fetchData,
  });
}; 