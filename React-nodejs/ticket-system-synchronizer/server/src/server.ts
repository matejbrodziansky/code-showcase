import configRoutes from './routes/config'
import issueRoutes from './routes/issueRoutes'
import jiraRoutes from './routes/jira'
import express from 'express';
import cors from 'cors';
import mongoose, { mongo } from "mongoose";
import dotenv from 'dotenv';
import path from 'path';

const app = express()

app.use(cors());

app.use(express.json());

app.use(configRoutes)
app.use(issueRoutes)

// app.get('/issue/by-config', (req, res) => {
//     res.json({ "users": ["userOne", "userTwo", "userThree"] })
// })

// app.get('/api', (req, res) => {
//     res.json({ "users": ["userOne", "userTwo", "userThree"] })
// })

const PORT = 5001

app.listen(PORT, () => {
    console.log(`Server started on port ${PORT}`);
})


dotenv.config({ path: path.resolve(__dirname, '../.env') });

const MONGO_URL = process.env.mongodbUrl;

if (!MONGO_URL) {
    console.error('MongoDB URL not defined in .env file!');
    process.exit(1);
}

mongoose.Promise = Promise;
mongoose.connect(MONGO_URL)
    .then(() => console.log('Connected to MongoDB'))
    .catch((error: Error) => console.log('Error connecting to MongoDB:', error));

mongoose.connection.on('error', (error: Error) => console.log('MongoDB Connection Error:', error));