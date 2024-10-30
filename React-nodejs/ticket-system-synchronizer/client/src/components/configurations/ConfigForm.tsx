import { FormAction, FormActionTypeEnum, FormState, SystemFormProps } from '../../types/types';
import { Directions, Direction } from '../../types/TicketSystems';
import { useConfigForm } from './useConfigForm'
import { ConfigProjectIdControl } from '../forms/config/ConfigProjectIdControl'
import { ConfigSystemControl } from '../forms/config/ConfigSystemControl'
import { ConfigTokenControl } from '../forms/config/ConfigTokenControl'
import { ConfigUrlControl } from '../forms/config/ConfigUrlControl'
import React, { ChangeEvent } from 'react';
import Button from 'react-bootstrap/Button';
import ButtonGroup from 'react-bootstrap/ButtonGroup';
import Form from 'react-bootstrap/Form';
// import {Direction} from '../../../../shared/types/syncConfig' //TODO all types merge

interface SystemFormGroupProps {
    systemLabel: string;
    dispatch: React.Dispatch<FormAction>;
    bootstrapBgColor: string;
    errors: Record<string, string | undefined>;
    index: number;
    state: FormState
}

const SystemFormGroup: React.FC<SystemFormGroupProps> = ({
    dispatch,
    bootstrapBgColor,
    errors,
    index,
    state
}) => (
    <div>
        <Form.Group className={`mb-3  rounded p-2 ${bootstrapBgColor} bg-opacity-25`}>

            <ConfigSystemControl
                state={state}
                dispatch={dispatch}
                errors={errors}
                index={index}
            />

            <ConfigProjectIdControl
                state={state}
                dispatch={dispatch}
                errors={errors}
                index={index}
            />

            <ConfigTokenControl
                state={state}
                dispatch={dispatch}
                errors={errors}
                index={index}
            />

            <ConfigUrlControl
                state={state}
                dispatch={dispatch}
                errors={errors}
                index={index}
            />

        </Form.Group>
    </div>
);


const ConfigForm: React.FC<SystemFormProps> = props => {
    const { state, dispatch, submitForm} = useConfigForm(props);
    const numberOfSystems = state.systems.length;

    return (
        <div className={`mx-auto mt-5 rounded p-2  text-white  ${props.isModal ? 'col-10' : 'col-8'}`}>
            <h1 className='text-center '>Create configuration</h1>

            {state.errors?.general && (
                <div className="alert alert-danger text-center">
                    {state.errors.general}
                </div>
            )}

            <Form
                onSubmit={submitForm}
            >
                <Form.Group className="mb-3" controlId="formBasicName">
                    <Form.Label>Configuration Name</Form.Label>
                    <Form.Control
                        type="text"
                        value={state.name}
                        isInvalid={(!!state.errors['name'])}
                        onChange={(e: ChangeEvent<HTMLInputElement>) => dispatch({
                            'type': FormActionTypeEnum.SET_NAME, payload: e.target.value
                        })}
                    />
                    <Form.Control.Feedback
                        type="invalid"
                        className='bg-danger rounded text-white p-1 text-center'
                    >
                        {state.errors?.name}
                    </Form.Control.Feedback>
                </Form.Group>

                {Array.from({ length: numberOfSystems as number }).map((_, index) =>
                    <SystemFormGroup
                        key={index}
                        systemLabel={`System ${index + 1}`}
                        dispatch={dispatch}
                        bootstrapBgColor={index % 2 === 0 ? 'bg-primary' : 'bg-info'}
                        errors={state.errors}
                        index={index}
                        state={state}
                    />
                )}

                <Form.Group className="mb-3">
                    <Form.Label>System</Form.Label>
                    <Form.Select
                        value={state.direction}
                        onChange={(e: ChangeEvent<HTMLSelectElement>) => dispatch({
                            type: FormActionTypeEnum.SET_DIRECTION,
                            payload: e.target.value as Direction,
                        })}
                        isInvalid={(!!state.errors['direction'])}
                    >
                        {Directions.map((system, index) => (
                            <option key={index}>{system}</option>
                        ))}
                    </Form.Select>
                </Form.Group>

                <Form.Group className="mb-3">
                    <Form.Label>Enabled</Form.Label>
                    <Form.Check
                        type="switch"
                        onChange={(e: ChangeEvent<HTMLInputElement>) => dispatch({ type: FormActionTypeEnum.SET_ENABLED, payload: e.target.checked })}
                    />
                </Form.Group>


                <ButtonGroup size="lg" className="mt-2 ">
                    <Button
                        type='submit'
                        variant="secondary">
                        {props.isEditing ? 'Edit' : 'Create'}
                    </Button>
                </ButtonGroup>
            </Form>
        </div>
    )
}

export default ConfigForm