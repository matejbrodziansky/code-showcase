import React, { useState, useEffect } from 'react';
import { SyncConfiguration } from '../../types/TicketSystems';
import Table from 'react-bootstrap/Table';
import ButtonGroup from 'react-bootstrap/ButtonGroup';
import Button from 'react-bootstrap/Button';
import Form from 'react-bootstrap/Form';
import CreateConfig from './ConfigForm';
import ModalComponent from '../Modal'
import { useLocation, useNavigate } from 'react-router-dom';
import { useCallback } from 'react';
import { useDispatch } from 'react-redux';
import { selectConfig } from '../../redux/slices/configSlice';

interface TableHeader {
    key: keyof SyncConfiguration | string;
    label: string;
}

const tableHeaders: TableHeader[] = [
    { key: 'name', label: 'Configuration Name' },
    { key: 'direction', label: 'Direction' },
    { key: 'systems[0].system', label: 'System 1' },
    { key: 'Project 1', label: 'Project-Id-1' },
    { key: 'url 1', label: 'Url' },
    { key: 'systems[1].system', label: 'System 2' },
    { key: 'Project 2', label: 'Project-Id-2' },
    { key: 'url 2', label: 'Url' },
    { key: 'enabled', label: 'Enabled' },
    { key: 'action', label: 'Actions' }
];

const ConfigList: React.FC = () => {
    const [configs, setConfigs] = useState<SyncConfiguration[]>([])
    const [edit, setEdit] = useState(false)
    const [editedConfig, setEditedConfig] = useState<SyncConfiguration | null>(null)
    const [createModalShow, setCreateModalShow] = useState(false)
    const location = useLocation();
    const [configMessage, setConfigMessage] = useState(location.state?.message || '');
    const [showNotification, setShowNotification] = useState<boolean>(!!configMessage);
    const apiUrl = process.env.REACT_APP_API_URL; //TODO: NEJAK DEFAULT aj pre create config ?
    const dispatch = useDispatch();
    const navigate = useNavigate();



    const getAllConfigs = useCallback((): void => {
        fetch(`${apiUrl}/config/all`, {
            method: 'GET'
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json()
            })
            .then(data => {
                setConfigs(data);
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
            });
    }, [apiUrl])

    const addNewConfig = (newConfig: SyncConfiguration) => {

        if (!edit) {
            setConfigs(prevConfigs => {
                return [...prevConfigs, newConfig]
            })
        } else {
            setConfigs((prevConfigs) =>
                prevConfigs.map((config) =>
                    config._id === newConfig._id ? newConfig : config
                )
            )
        }
        setShowNotification(true);
        setCreateModalShow(!createModalShow)
    }

    const deleteConfigHandler = (configId: string) => {
        fetch(`${apiUrl}/config/delete/${configId}`, {
            method: 'DELETE'
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                setConfigs(prevConfigs => {
                    return prevConfigs.filter(config => config._id !== configId);
                });
            })
            .catch(error => {
                console.error('Error during deletion:', error);
            });
    };

    useEffect(() => {
        getAllConfigs();
    }, [getAllConfigs]);

    useEffect(() => {
        if (showNotification) {
            const timer = setTimeout(() => {
                setShowNotification(false);
            }, 3000);

            const newLocation = { ...location, state: {} };
            window.history.replaceState(newLocation.state, '');

            return () => clearTimeout(timer);
        }
    }, [showNotification, location]);

    const renderTableHeader = () => {
        return (
            <thead className="bg-primary text-white">
                <tr>
                    {tableHeaders.map((header) => (
                        <th key={header.key}>{header.label}</th>
                    ))}
                </tr>
            </thead>
        )
    }

    const handleShowConfig = (config: SyncConfiguration) => {
        dispatch(selectConfig(config));
        navigate(`/config/show/${config._id}`);

    };

    const toggleConfigEnabled = (configId: string) => {
        setConfigs((prevConfigs) =>
            prevConfigs.map((config) =>
                config._id === configId
                    ? { ...config, enabled: !config.enabled }
                    : config
            )
        )
    }

    const renderTableBody = () => {
        return (
            <tbody>
                { }
                {configs.map((config: SyncConfiguration) =>
                    <tr key={config._id}>
                        <td>{config.name}</td>
                        <td>{config.direction}</td>
                        <td>{config.systems[0].system}</td>
                        <td>{config.systems[0].projectId}</td>
                        <td>{config.systems[0].url}</td>
                        <td>{config.systems[1].system}</td>
                        <td>{config.systems[1].projectId}</td>
                        <td>{config.systems[1].url}</td>
                        <td>
                            <Form>
                                <Form.Check
                                    checked={!!config.enabled}
                                    type="switch"
                                    onChange={() => toggleConfigEnabled(config._id!)}
                                />
                            </Form>
                        </td>
                        <td>
                            <ButtonGroup aria-label="Basic example" size='sm'>
                                <Button
                                    onClick={() => handleShowConfig(config)}
                                    variant="primary"
                                >
                                    Show
                                </Button>                                <Button
                                    onClick={() => {
                                        setEditedConfig(config);
                                        setCreateModalShow(true);
                                        setEdit(true)
                                    }}
                                    variant="warning"
                                >
                                    Edit
                                </Button>
                                <Button
                                    onClick={deleteConfigHandler.bind(false, config._id!)}
                                    variant="danger"
                                >
                                    Delete
                                </Button>
                            </ButtonGroup>
                        </td>
                    </tr>
                )}
            </tbody>
        )
    }

    return (
        <div className='m-5 bg-white p-2 rounded bg-opacity-25'>

            {showNotification &&
                <ModalComponent
                    show={showNotification}
                    onHide={() => setShowNotification(false)}
                    title="Created Configuration"
                    children={configMessage}
                    footer="true" />}

            <h2
                className='text-white'
            >Configurations {configs.length}
            </h2>
            <>
                <Button
                    size='sm'
                    className='my-3'
                    variant="primary"
                    onClick={() => setCreateModalShow(true)}
                >
                    Create configuration
                </Button>

                <ModalComponent
                    show={createModalShow}
                    onHide={() => setCreateModalShow(false)}
                >
                    <CreateConfig
                        isModal={true}
                        onCreateConfiguration={addNewConfig}
                        onCreateConfigurationMessage={setConfigMessage}
                        isEditing={edit}
                        initialData={editedConfig}
                    />
                </ModalComponent>
            </>
            <Table
                striped hover responsive className="tablepress">
                {renderTableHeader()}
                {renderTableBody()}
            </Table>
        </div>
    );
};
export default ConfigList;
