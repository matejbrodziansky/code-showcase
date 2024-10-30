import { z } from 'zod';
import { Direction, TicketSystems } from '../../types/TicketSystems';

export const syncConfigurationSchema = z.object({
    name: z.string().min(1, { message: "Name is required" }),
    systems: z.array(z.object({
        system: z.nativeEnum(TicketSystems),
        projectId: z.string().min(1, { message: "Project ID is required" }),
        url: z.string().url({ message: "Invalid URL" }),
        token: z.string().min(1, { message: "Token is required" }),
    })),
    enabled: z.boolean(),
    direction: z.nativeEnum(Direction),
});