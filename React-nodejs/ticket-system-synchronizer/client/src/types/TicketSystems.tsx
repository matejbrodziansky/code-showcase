export enum TicketSystems {
    Jira = 'Jira',
    Mantis = 'Mantis',
}

export type TicketSystemsType = keyof typeof TicketSystems;


export enum Direction {
    TwoWay = 'two-way',
    JiraToMantis = 'jira-to-mantis',
    MantisToJira = 'mantis-to-jira',
}

export type SyncConfiguration = {
    _id?: string
    name: string;
    systems: [
        {
            system: TicketSystemsType;
            projectId: string;
            token: string;
            url: string
        },
        {
            system: TicketSystemsType;
            projectId: string;
            token: string;
            url: string
        }
    ];
    direction: Direction;
    enabled: boolean
};



export const Directions = Object.values(Direction);
export const Systems = Object.values(TicketSystems);
