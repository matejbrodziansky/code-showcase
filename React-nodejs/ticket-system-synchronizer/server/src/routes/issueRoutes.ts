import { fetchIssueByConfig} from '../controller/IssueController'
import { getConfigById } from '../db/config'


const Router = require('express')
const router = Router()

router.get('/issue/by-config', fetchIssueByConfig)
// router.get('/issue/by-config/:system/:configId', fetchIssueByConfig);


export default router;