import React from 'react';
import Modal from 'react-bootstrap/Modal';
import Button from 'react-bootstrap/Button';

interface ModalComponentProps {
    show: boolean;
    onHide: () => void;
    children?: React.ReactNode;
    title?: string
    footer?: string
}

const ModalComponent: React.FC<ModalComponentProps> = (props) => {
    return (
        <Modal
            show={props.show}
            onHide={props.onHide}
            size="lg"
            aria-labelledby="contained-modal-title-vcenter"
            centered
            className='text-light'
        >
            {props.title && (
                <Modal.Header closeButton>
                    <Modal.Title id="contained-modal-title-vcenter">
                        {props.title}
                    </Modal.Title>
                </Modal.Header>
            )}

            <Modal.Body className="modal-body-transparent">
                {props.children}
            </Modal.Body>

            {props.footer && (
                <Modal.Footer>
                    <Button onClick={props.onHide}>Close</Button>
                </Modal.Footer>
            )}

        </Modal>
    );
}

export default ModalComponent;
