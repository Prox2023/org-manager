import React from 'react';
import { render, screen, fireEvent } from '@testing-library/react';
import { DynamicForm } from '../DynamicForm';
import { ThemeProvider } from '../../../theme/ThemeProvider';

describe('DynamicForm', () => {
    const mockOnSubmit = jest.fn();

    const renderForm = (fields: any[]) => {
        return render(
            <ThemeProvider>
                <DynamicForm
                    fields={fields}
                    onSubmit={mockOnSubmit}
                    initialValues={{}}
                />
            </ThemeProvider>
        );
    };

    beforeEach(() => {
        mockOnSubmit.mockClear();
    });

    it('renders all field types correctly', () => {
        const fields = [
            {
                type: 'text',
                name: 'text_field',
                label: 'Text Field'
            },
            {
                type: 'password',
                name: 'password_field',
                label: 'Password Field'
            },
            {
                type: 'switch',
                name: 'switch_field',
                label: 'Switch Field'
            }
        ];

        renderForm(fields);

        expect(screen.getByLabelText('Text Field')).toHaveAttribute('type', 'text');
        expect(screen.getByLabelText('Password Field')).toHaveAttribute('type', 'password');
        expect(screen.getByRole('switch')).toBeInTheDocument();
    });

    it('handles form submission with correct values', () => {
        const fields = [
            {
                type: 'text',
                name: 'name',
                label: 'Name'
            },
            {
                type: 'switch',
                name: 'active',
                label: 'Active'
            }
        ];

        renderForm(fields);

        fireEvent.change(screen.getByLabelText('Name'), {
            target: { value: 'Test Name' }
        });

        fireEvent.click(screen.getByRole('switch'));

        fireEvent.submit(screen.getByRole('form'));

        expect(mockOnSubmit).toHaveBeenCalledWith({
            name: 'Test Name',
            active: true
        });
    });

    it('displays field descriptions', () => {
        const fields = [
            {
                type: 'text',
                name: 'field',
                label: 'Field',
                description: 'This is a description'
            }
        ];

        renderForm(fields);

        expect(screen.getByText('This is a description')).toBeInTheDocument();
    });
}); 