import React, { useEffect } from 'react';
import { useSelector } from 'react-redux';
import { RootState } from '../../redux/store';
import { useParams } from 'react-router-dom';
import { selectConfig, setConfigs } from '../../redux/slices/configSlice';
import { useDispatch } from 'react-redux';
import { TicketSystemsType, Systems } from '../../types/TicketSystems';
import Button from 'react-bootstrap/Button';


const ConfigShow: React.FC = () => {
    const { id } = useParams<{ id: string }>();
    const selectedConfig = useSelector((state: RootState) => state.configs.selectedConfig);
    const apiUrl = process.env.REACT_APP_API_URL; //TODO: NEJAK DEFAULT aj pre create config ?
    const dispatch = useDispatch();


    const handleGetIssue = async (system: TicketSystemsType, configId: string) => {
        try {
            const issue = await getIssue(system, configId)
            console.log(issue);

        } catch (error) {

        }
    }

    const getIssue = async (system: TicketSystemsType, configId: string, issueId?: string) => {
        const requestOptions = {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' },
        };

        const queryParams = new URLSearchParams({ system, configId });
        if (issueId) queryParams.append('issueId', issueId);

        const issue = await fetch(`${apiUrl}/issue/by-config?${queryParams.toString()}`, requestOptions);
        return issue;
    }


    useEffect(() => {
        if (!selectedConfig || selectedConfig._id !== id) {
            fetch(apiUrl + `/config/${id}`)
                .then(response => response.json())
                .then(data => {
                    dispatch(selectConfig(data));
                })

        }
    }, [selectedConfig, id, apiUrl, dispatch]);

    if (!selectedConfig || selectedConfig._id !== id) {
        return <div>Loading...</div>;
    }

    return (
        <div className='config-show'>
            <h2>{selectedConfig.name}</h2>
            <p>Direction: {selectedConfig.direction}</p>
            <p>System 1: {selectedConfig.systems[0].system}</p>
            <p>Project ID 1: {selectedConfig.systems[0].projectId}</p>
            <p>System 2: {selectedConfig.systems[1].system}</p>
            <p>Project ID 2: {selectedConfig.systems[1].projectId}</p>
            {/* <p>URL: {selectedConfig.url}</p> */}
            <p>Enabled: {selectedConfig.enabled ? 'Yes' : 'No'}</p>



            <Button
                size='sm'
                onClick={() => handleGetIssue(selectedConfig.systems[0].system, selectedConfig._id!)}
            >
                {selectedConfig.systems[0].system}
            </Button>

            <Button
                size='sm'
                onClick={() => handleGetIssue(selectedConfig.systems[1].system, selectedConfig._id!)}
            >
                {selectedConfig.systems[1].system}
            </Button>

            {/* {Systems.map((system, index) => (
                <div key={index}>
                    <Button
                        size='sm'
                        onClick={() => handleGetIssue(system, selectedConfig._id!)}
                    >
                        {system}
                    </Button>
                </div>
            ))} */}
        </div>
    );
};

export default ConfigShow;
