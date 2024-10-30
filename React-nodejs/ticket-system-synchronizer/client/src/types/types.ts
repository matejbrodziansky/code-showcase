import { Direction, TicketSystemsType, SyncConfiguration } from "./TicketSystems";

// Types for Configurations
export enum FormActionTypeEnum {
    SET_NAME = 'SET_NAME',
    SET_SYSTEM = 'SET_SYSTEM',
    SET_DIRECTION = 'SET_DIRECTION',
    SET_ERRORS = 'SET_ERRORS',
    SET_TOKEN = 'SET_TOKEN',
    SET_URL = 'SET_URL',
    SET_PROJECT_ID = 'SET_PROJECT_ID',
    SET_ENABLED = 'SET_ENABLED',
}

export type FormAction =
    | { type: FormActionTypeEnum.SET_NAME; payload: string }
    | { type: FormActionTypeEnum.SET_ENABLED; payload: boolean }
    | { type: FormActionTypeEnum.SET_DIRECTION; payload: Direction }
    | { type: FormActionTypeEnum.SET_ERRORS; payload: Record<string, string | undefined> }
    | { type: FormActionTypeEnum.SET_SYSTEM; index: number; payload: TicketSystemsType }
    | { type: FormActionTypeEnum.SET_PROJECT_ID; index: number; payload: string }
    | { type: FormActionTypeEnum.SET_TOKEN; index: number; payload: string }
    | { type: FormActionTypeEnum.SET_URL; index: number; payload: string };

export interface SystemFormGroupProps {
    systemLabel: string;
    dispatch: React.Dispatch<FormAction>;
    bootstrapBgColor: string;
    errors: Record<string, string | undefined>;
    index: number;
}

export type SystemState = {
    system: TicketSystemsType;
    projectId: string;
    token: string;
    url: string;
};


export type FormState = {
    name: string;
    systems: SystemState[];
    enabled: boolean;
    direction: Direction;
    errors: Record<string, string | undefined>;
};

export interface SystemFormProps {
    onCreateConfiguration?: (newConfig: SyncConfiguration) => void;
    onCreateConfigurationMessage?: (message: string) => void
    isModal?: boolean,
    isEditing?: boolean
    initialData?: SyncConfiguration | null
}
