import { Request, Response } from 'express';
import { fetchAllIssues} from '../controller/jiraController'

const Router = require('express')
const router = Router()

router.get('/jira/issues-all', fetchAllIssues)

export default router;