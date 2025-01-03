import React from 'react';
import { screen, fireEvent, waitFor } from '@testing-library/react';
import { renderWithProviders } from '../../../test-utils';
import { DiscordSettings } from '../DiscordSettings';

describe('DiscordSettings', () => {
    beforeEach(() => {
        // Mock fetch responses
        (globalThis.fetch as jest.Mock).mockImplementation(() => Promise.resolve({
            ok: true,
            status: 200,
            json: () => Promise.resolve({
                client_id: 'test_id',
                client_secret: 'test_secret',
                redirect_uri: 'http://example.com/callback',
                registration_enabled: true,
                fields: [
                    {
                        type: 'text',
                        name: 'client_id',
                        label: 'Client ID',
                        description: 'Your Discord application client ID'
                    },
                    {
                        type: 'password',
                        name: 'client_secret',
                        label: 'Client Secret',
                        description: 'Your Discord application client secret'
                    }
                ]
            })
        } as Response));
    });

    afterEach(() => {
        jest.clearAllMocks();
    });

    it('renders all fields from the API', async () => {
        renderWithProviders(<DiscordSettings />);

        await waitFor(() => {
            expect(screen.getByLabelText('Client ID')).toBeInTheDocument();
            expect(screen.getByLabelText('Client Secret')).toBeInTheDocument();
        });

        expect(screen.getByLabelText('Client ID')).toHaveValue('test_id');
        expect(screen.getByLabelText('Client Secret')).toHaveValue('test_secret');
    });

    it('handles form submission correctly', async () => {
        // Mock save response
        (globalThis.fetch as jest.Mock).mockImplementationOnce(() => Promise.resolve({
            ok: true,
            status: 200,
            json: () => Promise.resolve({ message: 'Settings saved successfully' })
        } as Response));

        renderWithProviders(<DiscordSettings />);

        await waitFor(() => {
            expect(screen.getByLabelText('Client ID')).toBeInTheDocument();
        });

        fireEvent.change(screen.getByLabelText('Client ID'), {
            target: { value: 'new_client_id' }
        });

        fireEvent.click(screen.getByText('Save Settings'));

        await waitFor(() => {
            expect(screen.getByText('Settings saved successfully')).toBeInTheDocument();
        });
    });

    it('displays error messages when API fails', async () => {
        // Mock API error
        (globalThis.fetch as jest.Mock).mockImplementationOnce(() => Promise.resolve({
            ok: false,
            status: 500,
            json: () => Promise.resolve({ message: 'Failed to save settings' })
        } as Response));

        renderWithProviders(<DiscordSettings />);

        await waitFor(() => {
            expect(screen.getByLabelText('Client ID')).toBeInTheDocument();
        });

        fireEvent.click(screen.getByText('Save Settings'));

        await waitFor(() => {
            expect(screen.getByText('Failed to save settings')).toBeInTheDocument();
        });
    });
}); 