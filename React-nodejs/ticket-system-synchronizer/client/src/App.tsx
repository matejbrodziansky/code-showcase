import React from 'react';
import './index.css';
import Navbar from './components/Navbar';
import CreateConfig from './components/configurations/ConfigForm';
import ListConfigs from './components/configurations/ConfigList';
import { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import ParticlesCanvas from './components/ParticlesCanvas';
import ShowConfig from './components/configurations/ConfigShow';


const App: React.FC = () => {
  const routes = [
    { name: 'Home', path: '/' },
    { name: 'TestApi', path: '/test/api' },
    { name: 'All configs', path: '/config/list' },
    { name: 'New Configuration', path: '/config/create' },
  ];
  return (
    <Router>
      <ParticlesCanvas />
      <Navbar routes={routes} />
      <Routes>
        <Route path="/config/create" element={<CreateConfig />} />
        <Route path="/config/list" element={<ListConfigs />} />
        <Route path="/config/show/:id" element={<ShowConfig />} />
      </Routes>
    </Router>
  );
}

export default App;
