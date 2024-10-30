import React, { ChangeEvent } from 'react';
import { FormAction, FormActionTypeEnum, FormState } from '../../../types/types'
import { ConfigFormInput } from './ConfigFormInput';


interface ConfigProjectIdControlProps {
    state: FormState
    dispatch: React.Dispatch<FormAction>;
    errors: Record<string, string | undefined>;
    index: number;
}

export const ConfigProjectIdControl: React.FC<ConfigProjectIdControlProps> = ({ state, dispatch, errors, index }) => {
    return (

        <ConfigFormInput
            label={'Project ID/Key'}
            value={state.systems[index].projectId}
            onChange={(e: ChangeEvent<HTMLInputElement>) => dispatch({
                type: FormActionTypeEnum.SET_PROJECT_ID,
                payload: e.target.value as string,
                index
            })}
            isInvalid={!!errors[`systems.${index}.projectId`]}
            errorMessage={errors[`systems.${index}.projectId`]}
        />
    );
};

export default ConfigProjectIdControl;