import React, { ChangeEvent } from 'react';
import { FormAction, FormActionTypeEnum, FormState } from '../../../types/types'
import { ConfigFormInput } from './ConfigFormInput';


interface ConfigTokenControlProps {
    dispatch: React.Dispatch<FormAction>;
    errors: Record<string, string | undefined>;
    index: number;
    state: FormState
}

export const ConfigTokenControl: React.FC<ConfigTokenControlProps> = ({ state, dispatch, errors, index }) => {
    return (

        <ConfigFormInput
            label={'Token'}
            value={state.systems[index].token}
            onChange={(e: ChangeEvent<HTMLInputElement>) => dispatch({
                type: FormActionTypeEnum.SET_TOKEN,
                payload: e.target.value as string,
                index
            })}
            isInvalid={!!errors[`systems.${index}.projectId`]}
            errorMessage={errors[`systems.${index}.projectId`]}
        />
    );
};

export default ConfigTokenControl;