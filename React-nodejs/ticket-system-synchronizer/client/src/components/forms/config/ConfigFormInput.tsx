import React, { ChangeEvent } from 'react';
import Form from 'react-bootstrap/Form';

export const ConfigFormInput: React.FC<{
    label: string;
    onChange: (e: ChangeEvent<HTMLInputElement>) => void;
    isInvalid: boolean
    value: string
    errorMessage?: string
}> = ({ label, onChange, isInvalid, value, errorMessage }) => {
    return (
        <Form.Group className="mb-3">
            <Form.Label>{label}</Form.Label>
            <Form.Control
                type="text"
                onChange={onChange}
                isInvalid={isInvalid}
                value={value}
            />
            <Form.Control.Feedback type="invalid" className='bg-danger rounded text-white p-1 text-center'>
                {errorMessage}
            </Form.Control.Feedback>
        </Form.Group>
    )
}

export default ConfigFormInput