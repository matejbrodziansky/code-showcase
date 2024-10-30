import express from 'express';
import { JiraConnector } from '../connector/jiraConnector';

export const fetchAllIssues = async (req: express.Request, res: express.Response) => {
    const config = {
        baseUrl: 'https://matejbrodziansky.atlassian.net',
        apiToken: 'AQgbr39zXCGQXhif9qTIB1CA',
        username: 'matesss18@gmail.com',
        projectId: 'P2',
    };

    const connector = new JiraConnector(config);

    try {
        const issues = await connector.getIssues();

        return res.status(200).json(issues);
    } catch (error) {
        console.error('Error fetching issues:', error);
        return res.status(500).json({ error: 'Failed to fetch issues from Jira' });
    }
};
