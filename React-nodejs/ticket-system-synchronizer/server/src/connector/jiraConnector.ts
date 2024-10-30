import axios, { AxiosInstance } from 'axios';

interface JiraConnectorOptions {
    baseUrl: string;
    projectId: string;
    apiToken: string;
    username: string;
}

export class JiraConnector {
    private axiosInstance: AxiosInstance;
    private projectId: string;

    constructor(options: JiraConnectorOptions) {
        this.projectId = options.projectId;

        console.log(this.projectId);
        

        this.axiosInstance = axios.create({
            baseURL: options.baseUrl,
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Basic ${Buffer.from(`${options.username}:${options.apiToken}`).toString('base64')}`
            }
        });
    }

    async getIssues(): Promise<any> {
        try {
            const response = await this.axiosInstance.get(`/rest/api/3/search`, {
                params: {
                    jql: `project=${this.projectId}`, // Jira Query Language pre filtrovanie podľa projektu
                    maxResults: 10                   // Počet vrátených issues
                }
            });

            return response.data.issues;
        } catch (error) {
            console.error('Error fetching issues:', error);
            throw new Error('Failed to fetch issues from Jira');
        }
    }
}
