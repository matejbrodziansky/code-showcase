import express from 'express';
import { deleteConfigByid, createConfig as createNewConfig, getAllConfigurations, updateConfigById, getConfigById } from '../db/config'

export const fetchAllConfigs = async (req: express.Request, res: express.Response) => {

    try {
        const configs = await getAllConfigurations();
        return res.status(200).json(configs);
    } catch (error) {
        console.log(error);
        return res.sendStatus(400)
    }
}


export const getConfig = async (req: express.Request, res: express.Response) => {

    try {
        const { id } = req.params
        const config = await getConfigById(id)

        return res.json(config)

    } catch (error) {
        console.log(error);
        return res.sendStatus(400)

    }
}

export const createConfig = async (req: express.Request, res: express.Response) => {

    try {
        const newConfig = await createNewConfig(req.body)

        const configWithStringId = {
            ...newConfig,
            _id: newConfig._id.toString()
        };

        res.status(201).send({
            message: 'Configuration created successfully',
            data: configWithStringId
        })
    } catch (error) {
        console.error('Error saving configuration:', error);
        res.status(500).send({ error: 'Failed to create configuration' });
    }
}


export const deleteConfig = async (req: express.Request, res: express.Response) => {
    try {
        const { id } = req.params;

        const deletedConfig = await deleteConfigByid(id);
        return res.json(deletedConfig);

    } catch (error) {
        let errorMessage = 'An error occurred';
        if (error instanceof Error) {
            errorMessage = error.message;
        }
        return res.status(500).json({ message: errorMessage });
    }
}

export const updateConfig = async (req: express.Request, res: express.Response) => {
    try {
        const { id } = req.params;
        const updatedValues = req.body;

        if (!id || !updatedValues) {
            return res.status(400).send({ error: 'Invalid request. ID or update values are missing.' });
        }

        const updatedConfig = await updateConfigById(id, updatedValues);

        if (!updatedConfig) {
            return res.status(404).send({ message: 'Configuration not found' });
        }

        res.status(200).send({
            message: 'Configuration updated successfully',
            data: updatedConfig
        });
    } catch (error: unknown) {
        let errorMessage = 'An error occurred';
        if (error instanceof Error) {
            errorMessage = error.message;
        }
        return res.status(500).json({ message: errorMessage });
    }

};
