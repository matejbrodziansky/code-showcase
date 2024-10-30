import React, { ChangeEvent } from 'react'
import Form from 'react-bootstrap/Form';
import { FormAction, FormActionTypeEnum, FormState } from '../../../types/types'
import { TicketSystemsType, Systems } from '../../../types/TicketSystems';



interface ConfigSystemControlProps {
    state: FormState
    dispatch: React.Dispatch<FormAction>;
    errors: Record<string, string | undefined>;
    index: number;
}

export const ConfigSystemControl: React.FC<ConfigSystemControlProps> = ({ state, dispatch, index, errors }) => {
    return (

        <Form.Group className="mb-3">
            <Form.Label>System</Form.Label>
            <Form.Select
                value={state.systems[index].system}
                onChange={(e: ChangeEvent<HTMLSelectElement>) => dispatch({
                    type: FormActionTypeEnum.SET_SYSTEM,
                    payload: e.target.value as TicketSystemsType,
                    index
                })}
                isInvalid={!!errors[`systems.${index}.system`]}
            >
                {Systems.map((system, index) => (
                    <option key={index}>{system}</option>
                ))}
            </Form.Select>
        </Form.Group>
    )
}

export default ConfigSystemControl