// components/ParticlesCanvas.tsx
import React, { useRef, useEffect } from 'react';

const ParticlesCanvas: React.FC = () => {
    const canvasRef = useRef<HTMLCanvasElement | null>(null);

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas?.getContext('2d');

        if (!canvas || !ctx) return;

        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const particlesArray: any[] = [];
        const numberOfParticles = 200;

        // Mouse position
        const mouse = {
            x: null as number | null,
            y: null as number | null,
            radius: 100
        };

        // Handle mouse movement
        const handleMouseMove = (event: MouseEvent) => {
            mouse.x = event.x;
            mouse.y = event.y;
        };

        window.addEventListener('mousemove', handleMouseMove);

        // Create particles
        class Particle {
            x: number;
            y: number;
            directionX: number;
            directionY: number;
            size: number;
            color: string;

            constructor(x: number, y: number, directionX: number, directionY: number, size: number, color: string) {
                this.x = x;
                this.y = y;
                this.directionX = directionX;
                this.directionY = directionY;
                this.size = size;
                this.color = color;
            }

            // Draw particle
            draw() {
                if (canvas && ctx) {
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2, false);
                    ctx.fillStyle = '#ffffff';
                    ctx.fill();
                }
            }

            // Update particle position
            update() {
                if (canvas) {
                    if (this.x > canvas.width || this.x < 0) {
                        this.directionX = -this.directionX;
                    }
                    if (this.y > canvas.height || this.y < 0) {
                        this.directionY = -this.directionY;
                    }

                    // Move particle
                    this.x += this.directionX;
                    this.y += this.directionY;

                    // Check for mouse interaction
                    if (mouse.x !== null && mouse.y !== null) {
                        const dx = mouse.x - this.x;
                        const dy = mouse.y - this.y;
                        const distance = Math.sqrt(dx * dx + dy * dy);
                        if (distance < mouse.radius + this.size) {
                            if (mouse.x < this.x && this.x < canvas.width - this.size * 10) {
                                this.x += 10;
                            }
                            if (mouse.x > this.x && this.x > this.size * 10) {
                                this.x -= 10;
                            }
                            if (mouse.y < this.y && this.y < canvas.height - this.size * 10) {
                                this.y += 10;
                            }
                            if (mouse.y > this.y && this.y > this.size * 10) {
                                this.y -= 10;
                            }
                        }
                    }

                    // Draw particle
                    this.draw();
                }
            }
        }

        // Create particle array
        function init() {
            particlesArray.length = 0;
            for (let i = 0; i < numberOfParticles; i++) {
                const size = Math.random() * 5;
                const x = Math.random() * (window.innerWidth - size * 2);
                const y = Math.random() * (window.innerHeight - size * 2);
                const directionX = (Math.random() * 0.4) - 0.2;
                const directionY = (Math.random() * 0.4) - 0.2;
                const color = '#ffffff';

                particlesArray.push(new Particle(x, y, directionX, directionY, size, color));
            }
        }

        // Animate particles
        function animate() {
            requestAnimationFrame(animate);
            if (ctx) {
                ctx.clearRect(0, 0, window.innerWidth, window.innerHeight);

                for (let i = 0; i < particlesArray.length; i++) {
                    particlesArray[i].update();
                }

                connect();
            }
        }

        // Connect particles with lines
        function connect() {
            let opacityValue = 1;
            if (canvas && ctx) {
                for (let a = 0; a < particlesArray.length; a++) {
                    for (let b = a; b < particlesArray.length; b++) {
                        const distance = ((particlesArray[a].x - particlesArray[b].x) * (particlesArray[a].x - particlesArray[b].x))
                            + ((particlesArray[a].y - particlesArray[b].y) * (particlesArray[a].y - particlesArray[b].y));

                        if (distance < (canvas.width / 7) * (canvas.height / 7)) {
                            opacityValue = 1 - (distance / 20000);
                            ctx.strokeStyle = 'rgba(255,255,255,' + opacityValue + ')';
                            ctx.lineWidth = 1;
                            ctx.beginPath();
                            ctx.moveTo(particlesArray[a].x, particlesArray[a].y);
                            ctx.lineTo(particlesArray[b].x, particlesArray[b].y);
                            ctx.stroke();
                        }
                    }
                }
            }
        }

        window.addEventListener('resize', () => {
            if (canvas) {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
                init();
            }
        });

        init();
        animate();

        return () => {
            window.removeEventListener('mousemove', handleMouseMove);
        };
    }, []);

    return <canvas ref={canvasRef} style={{ position: 'absolute', top: 0, left: 0, zIndex: -1 }} />;
};

export default ParticlesCanvas;
