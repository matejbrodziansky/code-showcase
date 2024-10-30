import React, { useReducer } from 'react'
import { useNavigate } from 'react-router-dom'
import { syncConfigurationSchema } from './validators'
import { SyncConfiguration, Direction, TicketSystems } from '../../types/TicketSystems';
import { FormAction, FormActionTypeEnum, FormState, SystemFormProps } from '../../types/types';


export const initialState: FormState = {
    name: '',
    systems: [
        {
            system: TicketSystems.Jira,
            projectId: '',
            token: '',
            url: '',
        },
        {
            system: TicketSystems.Jira,
            projectId: '',
            token: '',
            url: '',
        }
    ],
    enabled: false,
    direction: Direction.TwoWay,
    errors: {}
};

export function formReducer(state: FormState, action: FormAction): FormState {
    switch (action.type) {
        case FormActionTypeEnum.SET_NAME:
            return { ...state, name: action.payload };
        case FormActionTypeEnum.SET_SYSTEM:
            return {
                ...state,
                systems: state.systems.map((system, index) =>
                    index === action.index ? { ...system, system: action.payload } : system
                ),
            };
        case FormActionTypeEnum.SET_PROJECT_ID:
            return {
                ...state,
                systems: state.systems.map((system, index) =>
                    index === action.index ? { ...system, projectId: action.payload } : system
                ),
            };
        case FormActionTypeEnum.SET_TOKEN:
            return {
                ...state,
                systems: state.systems.map((system, index) =>
                    index === action.index ? { ...system, token: action.payload } : system
                ),
            };
        case FormActionTypeEnum.SET_URL:
            return {
                ...state,
                systems: state.systems.map((system, index) =>
                    index === action.index ? { ...system, url: action.payload } : system
                ),
            };
        case FormActionTypeEnum.SET_ENABLED:
            return { ...state, enabled: action.payload };
        case FormActionTypeEnum.SET_DIRECTION:
            return { ...state, direction: action.payload };
        case FormActionTypeEnum.SET_ERRORS:
            return { ...state, errors: action.payload };
        default:
            return state;
    }
}


export const useConfigForm = (props: SystemFormProps) => {
    const navigate = useNavigate();


    const initializeState = (initialData: SyncConfiguration | undefined): FormState => {
        if (initialData) {
            return {
                name: initialData.name,
                systems: initialData.systems.map(system => ({
                    system: system.system,
                    projectId: system.projectId,
                    token: system.token,
                    url: system.url
                })),
                enabled: false,
                direction: Direction.TwoWay,
                errors: {}
            }
        }
        return initialState
    }

    const [state, dispatch] = useReducer(formReducer, initializeState(props.initialData ?? undefined));
    const apiUrl = process.env.REACT_APP_API_URL; //TODO: NEJAK DEFAULT aj pre create config ?

    const submitForm = async (e: React.FormEvent) => {
        e.preventDefault();

        const config: SyncConfiguration = {
            name: state.name,
            systems: [
                {
                    system: state.systems[0].system,
                    projectId: state.systems[0].projectId,
                    token: state.systems[0].token,
                    url: state.systems[0].url
                },
                {
                    system: state.systems[1].system,
                    projectId: state.systems[1].projectId,
                    token: state.systems[1].token,
                    url: state.systems[1].url
                }
            ],
            enabled: state.enabled,
            direction: state.direction
        };


        const validationResult = syncConfigurationSchema.safeParse(config);

        if (!validationResult.success) {
            const newErrors = validationResult.error.errors.reduce((acc, error) => {
                const field = error.path.join('.');
                acc[field] = error.message;
                return acc;
            }, {} as Record<string, string | undefined>);
            dispatch({ type: FormActionTypeEnum.SET_ERRORS, payload: newErrors });
            return;
        }

        dispatch({ type: FormActionTypeEnum.SET_ERRORS, payload: {} });

        const requestOption = {
            method: props.initialData ? 'PATCH' : 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(config)
        };

        try {
            const response = await fetch(
                props.initialData ? `${apiUrl}/config/update/${props.initialData._id}` : `${apiUrl}/config/create`,
                requestOption
            );

            if (!response.ok) {
                const isJson = response.headers.get('content-type')?.includes('application/json');
                const errorData = isJson ? await response.json() : { message: response.statusText };
                throw new Error(errorData.message || 'Failed to create configuration');
            }

            const data = await response.json();

            if (props.onCreateConfiguration && props.isModal) {
                props.onCreateConfiguration(data.data);
                props.onCreateConfigurationMessage!(data.message)
            } else {
                navigate('/config/list', { state: { message: data.message } });
            }

        } catch (error) {
            let errorMessage = 'An unexpected error occurred';
            if (error instanceof Error) {
                errorMessage = error.message;
            }
            dispatch({ type: FormActionTypeEnum.SET_ERRORS, payload: { general: errorMessage } });
        }
    }
    return { state, dispatch, submitForm }
}
export default useConfigForm