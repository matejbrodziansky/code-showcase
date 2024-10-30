import { createSlice, PayloadAction } from '@reduxjs/toolkit';
import { SyncConfiguration } from '../../types/TicketSystems';

interface ConfigState {
    configs: SyncConfiguration[];
    selectedConfig: SyncConfiguration | null;
}

const initialState: ConfigState = {
    configs: [],
    selectedConfig: null,
};

const configSlice = createSlice({
    name: 'config',
    initialState,
    reducers: {
        setConfigs: (state, action: PayloadAction<SyncConfiguration[]>) => {
            state.configs = action.payload;
        },
        selectConfig: (state, action: PayloadAction<SyncConfiguration>) => {
            state.selectedConfig = action.payload;
        },
        clearSelectedConfig: (state) => {
            state.selectedConfig = null;
        },
    },
});

export const { setConfigs, selectConfig, clearSelectedConfig } = configSlice.actions;
export default configSlice.reducer;
