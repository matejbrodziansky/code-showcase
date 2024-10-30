import { deleteConfig, createConfig, fetchAllConfigs, updateConfig, getConfig } from '../controller/ConfigController'
import { getConfigById } from '../db/config'


const Router = require('express')
const router = Router()

router.get('/config/all', fetchAllConfigs)
router.get('/config/:id', getConfig)
router.post('/config/create', createConfig)
router.delete('/config/delete/:id', deleteConfig)
router.patch('/config/update/:id', updateConfig)

export default router;