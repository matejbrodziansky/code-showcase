import React from 'react';
import { Link } from 'react-router-dom';
import 'bootstrap/dist/css/bootstrap.min.css';
import { FaHome } from "react-icons/fa";

import { Container, Nav, Navbar as BootstrapNavbar } from 'react-bootstrap';
interface Routes {
    routes: { name: string, path: string }[];
}


const Navbar: React.FC<Routes> = props => {
    return (
        <BootstrapNavbar bg="dark" expand="lg">
            <Container>
                <BootstrapNavbar.Brand as={Link} to="/" className='text-light'>
                    <FaHome />
                </BootstrapNavbar.Brand>
                <BootstrapNavbar.Toggle aria-controls="basic-navbar-nav"
                    className="bg-white text-dark border-0"
                />
                <BootstrapNavbar.Collapse id="basic-navbar-nav">
                    <Nav className="mr-auto">
                        {props.routes.map(route => (
                            <Nav.Link as={Link} to={route.path} key={route.path} className='text-light'>
                                {route.name}
                            </Nav.Link>
                        ))}
                    </Nav>
                </BootstrapNavbar.Collapse>
            </Container>
        </BootstrapNavbar>
    );
};

export default Navbar;