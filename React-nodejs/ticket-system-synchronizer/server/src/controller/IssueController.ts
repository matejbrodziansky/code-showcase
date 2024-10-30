import express from 'express';
import { getConfigById } from '../db/config'


export const fetchIssueByConfig = async (req: express.Request, res: express.Response) => {
    const { system, configId } = req.query;

    if (typeof configId !== 'string') {
        res.status(400).json({ error: 'Invalid configId' });
        return;
    }
    const config = await getConfigById(configId);

    console.log(config);
    
    console.log("System:", system);
    console.log("Config ID:", configId);

    res.status(200).json({ message: `Fetched issue for system ${system} with config ${configId}`, system, configId });
};
