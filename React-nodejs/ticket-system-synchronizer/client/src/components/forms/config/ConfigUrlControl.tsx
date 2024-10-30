import React, { ChangeEvent } from 'react';
import { FormAction, FormActionTypeEnum, FormState } from '../../../types/types'
import { ConfigFormInput } from './ConfigFormInput';


interface ConfigUrlControlProps {
    dispatch: React.Dispatch<FormAction>;
    errors: Record<string, string | undefined>;
    index: number;
    state: FormState
}

export const ConfigUrlControl: React.FC<ConfigUrlControlProps> = ({ state, dispatch, errors, index }) => {
    return (

        <ConfigFormInput
            label={'Url'}
            value={state.systems[index].url}
            onChange={(e: ChangeEvent<HTMLInputElement>) => dispatch({
                type: FormActionTypeEnum.SET_URL,
                payload: e.target.value as string,
                index
            })}
            isInvalid={!!errors[`systems.${index}.url`]}
            errorMessage={errors[`systems.${index}.url`]}
        />
    );
};

export default ConfigUrlControl;