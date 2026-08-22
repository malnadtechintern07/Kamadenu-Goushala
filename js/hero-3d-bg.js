/**
 * KAMADENU GOUSHALA - LIVE 3D HERO BACKGROUND TRANSITION (THREE.JS)
 */

class Hero3DBackground {
    constructor(canvasId) {
        this.canvas = document.getElementById(canvasId);
        if (!this.canvas) return;

        this.container = this.canvas.parentElement;
        this.width = this.container.clientWidth || window.innerWidth;
        this.height = this.container.clientHeight || window.innerHeight;

        this.mouse = { x: 0, y: 0, targetX: 0, targetY: 0 };
        this.init();
    }

    init() {
        if (window.THREE && this.isWebGLAvailable()) {
            this.initThreeJS();
        } else {
            this.initFallback();
        }
    }

    isWebGLAvailable() {
        try {
            const canvas = document.createElement('canvas');
            return !!(window.WebGLRenderingContext && (canvas.getContext('webgl') || canvas.getContext('experimental-webgl')));
        } catch (e) {
            return false;
        }
    }

    initThreeJS() {
        const THREE = window.THREE;

        // Scene, Camera, Renderer
        this.scene = new THREE.Scene();
        this.camera = new THREE.PerspectiveCamera(60, this.width / this.height, 0.1, 1000);
        this.camera.position.set(0, 0, 8);

        this.renderer = new THREE.WebGLRenderer({ canvas: this.canvas, antialias: true, alpha: true });
        this.renderer.setSize(this.width, this.height);
        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

        // Dusky Twilight 3D Point Lights
        const ambientLight = new THREE.AmbientLight(0xffecd6, 1.1);
        this.scene.add(ambientLight);

        const pointLight1 = new THREE.PointLight(0xE86A33, 3.5, 22);
        pointLight1.position.set(6, 6, 6);
        this.scene.add(pointLight1);

        const pointLight2 = new THREE.PointLight(0x7E22CE, 2.2, 20);
        pointLight2.position.set(-6, -4, 4);
        this.scene.add(pointLight2);

        const pointLight3 = new THREE.PointLight(0xF59E0B, 2.8, 18);
        pointLight3.position.set(0, 5, -3);
        this.scene.add(pointLight3);

        // Group for 3D Background Objects
        this.bgGroup = new THREE.Group();

        // 1. Floating 3D Dusky Sunset Orbs
        const orbGeo = new THREE.SphereGeometry(0.35, 24, 24);
        const duskyGoldMat = new THREE.MeshStandardMaterial({
            color: 0xE86A33,
            metalness: 0.85,
            roughness: 0.2,
            emissive: 0x4a1800,
            emissiveIntensity: 0.4
        });


        this.orbs = [];
        for (let i = 0; i < 22; i++) {
            const orb = new THREE.Mesh(orbGeo, goldMat);
            orb.position.set(
                (Math.random() - 0.5) * 16,
                (Math.random() - 0.5) * 10,
                (Math.random() - 0.5) * 8
            );
            const scale = Math.random() * 0.8 + 0.4;
            orb.scale.set(scale, scale, scale);
            orb.userData = {
                speedY: Math.random() * 0.008 + 0.004,
                rotSpeed: Math.random() * 0.02 + 0.005,
                initialY: orb.position.y
            };
            this.bgGroup.add(orb);
            this.orbs.push(orb);
        }

        // 2. 3D Sacred Geometry Mandala Rings
        const ringGeo = new THREE.TorusGeometry(2.8, 0.04, 16, 64);
        const ringMat = new THREE.MeshStandardMaterial({
            color: 0xF59E0B,
            metalness: 0.9,
            roughness: 0.1,
            transparent: true,
            opacity: 0.35
        });

        this.ring1 = new THREE.Mesh(ringGeo, ringMat);
        this.ring1.position.set(-2, 0, -2);
        this.bgGroup.add(this.ring1);

        this.ring2 = new THREE.Mesh(new THREE.TorusGeometry(1.9, 0.03, 16, 64), ringMat);
        this.ring2.position.set(-2, 0, -2);
        this.bgGroup.add(this.ring2);

        // 3. 3D Particle Cloud
        const particleGeo = new THREE.BufferGeometry();
        const particleCount = 180;
        const posArray = new Float32Array(particleCount * 3);

        for (let i = 0; i < particleCount * 3; i += 3) {
            posArray[i] = (Math.random() - 0.5) * 20;
            posArray[i + 1] = (Math.random() - 0.5) * 12;
            posArray[i + 2] = (Math.random() - 0.5) * 10;
        }

        particleGeo.setAttribute('position', new THREE.BufferAttribute(posArray, 3));

        const particleMat = new THREE.PointsMaterial({
            size: 0.08,
            color: 0xF59E0B,
            transparent: true,
            opacity: 0.65
        });

        this.particles = new THREE.Points(particleGeo, particleMat);
        this.bgGroup.add(this.particles);

        this.scene.add(this.bgGroup);

        this.setupEvents();
        this.animateThreeJS();
    }

    setupEvents() {
        window.addEventListener('resize', () => {
            if (!this.container) return;
            this.width = this.container.clientWidth || window.innerWidth;
            this.height = this.container.clientHeight || window.innerHeight;
            if (this.renderer && this.camera) {
                this.camera.aspect = this.width / this.height;
                this.camera.updateProjectionMatrix();
                this.renderer.setSize(this.width, this.height);
            }
        });

        window.addEventListener('mousemove', (e) => {
            this.mouse.targetX = (e.clientX / (window.innerWidth || 1000)) * 2 - 1;
            this.mouse.targetY = -(e.clientY / (window.innerHeight || 800)) * 2 + 1;
        });
    }

    animateThreeJS() {
        requestAnimationFrame(() => this.animateThreeJS());

        const time = Date.now() * 0.001;

        // Smooth Mouse Interpolation
        this.mouse.x += (this.mouse.targetX - this.mouse.x) * 0.05;
        this.mouse.y += (this.mouse.targetY - this.mouse.y) * 0.05;

        // Live 3D Group Rotation & Camera Parallax Shift
        if (this.bgGroup) {
            this.bgGroup.rotation.y = time * 0.05 + (this.mouse.x * 0.3);
            this.bgGroup.rotation.x = (this.mouse.y * 0.2);
        }

        if (this.ring1 && this.ring2) {
            this.ring1.rotation.z = time * 0.1;
            this.ring1.rotation.x = Math.sin(time * 0.2) * 0.5;
            this.ring2.rotation.z = -time * 0.15;
        }

        // Animate Floating Orbs
        if (this.orbs) {
            this.orbs.forEach(orb => {
                orb.position.y += orb.userData.speedY;
                orb.rotation.x += orb.userData.rotSpeed;
                orb.rotation.y += orb.userData.rotSpeed;

                if (orb.position.y > 6) {
                    orb.position.y = -6;
                }
            });
        }

        if (this.particles) {
            this.particles.rotation.y = -time * 0.03;
        }

        this.renderer.render(this.scene, this.camera);
    }

    initFallback() {
        // Fallback handled gracefully
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new Hero3DBackground('hero-3d-bg-canvas');
});
