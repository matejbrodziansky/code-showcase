import mongoose from "mongoose";
import { TicketSystems, Direction } from '../../../shared/types/syncConfig';


const SystemSchema = new mongoose.Schema({
    system: {
        type: String,
        required: true,
        enum: Object.keys(TicketSystems)
    },
    projectId: { type: String, required: true },
    token: { type: String, required: true },
    url: { type: String, required: true },
})

const SyncConfigurationSchema = new mongoose.Schema({
    name: { type: String, required: true },
    systems: {
        type: [SystemSchema],
        validate: {
            validator: (v: any) => v.length === 2,
            message: 'Systems must have 2 elements'
        },
    },
    direction: {
        type: String,
        required: true,
        enum: Object.values(Direction)
    },
    enabled: { type: Boolean, required: true }
},
    { collection: 'SyncConfigurations' }
)

export const SyncConfigurationModel = mongoose.model('SyncConfiguration', SyncConfigurationSchema);
export const getAllConfigurations = () => SyncConfigurationModel.find();
export const deleteConfigByid = (id: string) => SyncConfigurationModel.findByIdAndDelete({ _id: id })
export const updateConfigById = (id: string, values: Record<string, any>) =>
    SyncConfigurationModel.findByIdAndUpdate(id, values, { new: true }).exec();

export const getConfigById = (id: string) => SyncConfigurationModel.findById(id)
export const createConfig = (values: Record<string, any>) => new SyncConfigurationModel(values)
    .save()
    .then((config) => config.toObject());