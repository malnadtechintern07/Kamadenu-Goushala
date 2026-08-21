/**
 * KAMADENU GOUSHALA - 3D LIVE ANIMATED COW (THREE.JS & CANVAS FALLBACK)
 */

class KamadenuCow3D {
    constructor(canvasId) {
        this.canvas = document.getElementById(canvasId);
        if (!this.canvas) return;

        this.container = this.canvas.parentElement;
        this.width = this.container.clientWidth;
        this.height = this.container.clientHeight;

        this.init();
    }

    init() {
        if (window.THREE && this.isWebGLAvailable()) {
            this.initThreeJS();
        } else {
            this.initCanvasFallback();
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
        this.scene.background = null; // Transparent

        this.camera = new THREE.PerspectiveCamera(45, this.width / this.height, 0.1, 1000);
        this.camera.position.set(0, 1.2, 4.5);

        this.renderer = new THREE.WebGLRenderer({ canvas: this.canvas, antialias: true, alpha: true });
        this.renderer.setSize(this.width, this.height);
        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        this.renderer.shadowMap.enabled = true;
        this.renderer.shadowMap.type = THREE.PCFSoftShadowMap;

        // Lighting
        const ambientLight = new THREE.AmbientLight(0xfff5ea, 0.8);
        this.scene.add(ambientLight);

        const dirLight = new THREE.DirectionalLight(0xffeedd, 1.2);
        dirLight.position.set(5, 8, 5);
        dirLight.castShadow = true;
        dirLight.shadow.mapSize.width = 1024;
        dirLight.shadow.mapSize.height = 1024;
        this.scene.add(dirLight);

        const pointLight = new THREE.PointLight(0xe67e22, 0.5, 10);
        pointLight.position.set(-3, 2, 2);
        this.scene.add(pointLight);

        // Procedural Sacred Gir Cow Mesh Group
        this.cowGroup = new THREE.Group();
        this.buildProceduralCow(THREE);
        this.scene.add(this.cowGroup);

        // Raycasting for Mouse Interaction
        this.raycaster = new THREE.Raycaster();
        this.mouse = new THREE.Vector2();
        this.isHovered = false;

        this.setupEvents();
        this.animateThreeJS();
    }

    buildProceduralCow(THREE) {
        // Materials
        const bodyMat = new THREE.MeshStandardMaterial({ color: 0x9e4312, roughness: 0.6 }); // Gir Reddish Brown
        const whiteMat = new THREE.MeshStandardMaterial({ color: 0xfbf9f5, roughness: 0.5 }); // White Patches
        const hornMat = new THREE.MeshStandardMaterial({ color: 0x2c1810, roughness: 0.3 }); // Dark Horns
        const eyeMat = new THREE.MeshStandardMaterial({ color: 0x111111, roughness: 0.1 });
        const muzzleMat = new THREE.MeshStandardMaterial({ color: 0xd4a373, roughness: 0.7 });

        // Body Torso
        const bodyGeo = new THREE.CylinderGeometry(0.7, 0.65, 1.8, 16);
        bodyGeo.rotateZ(Math.PI / 2);
        const bodyMesh = new THREE.Mesh(bodyGeo, bodyMat);
        bodyMesh.castShadow = true;
        bodyMesh.receiveShadow = true;
        this.cowGroup.add(bodyMesh);

        // Indigenous Cow Hump (Gou-Kukud)
        const humpGeo = new THREE.SphereGeometry(0.38, 16, 16);
        humpGeo.scale(1, 1.3, 0.8);
        const humpMesh = new THREE.Mesh(humpGeo, bodyMat);
        humpMesh.position.set(-0.4, 0.7, 0);
        humpMesh.castShadow = true;
        this.cowGroup.add(humpMesh);

        // Head Group (for swaying animation)
        this.headGroup = new THREE.Group();
        this.headGroup.position.set(-0.95, 0.5, 0);

        // Head Skull
        const headGeo = new THREE.BoxGeometry(0.55, 0.45, 0.45);
        const headMesh = new THREE.Mesh(headGeo, bodyMat);
        this.headGroup.add(headMesh);

        // Muzzle / Snout
        const muzzleGeo = new THREE.BoxGeometry(0.35, 0.3, 0.4);
        const muzzleMesh = new THREE.Mesh(muzzleGeo, muzzleMat);
        muzzleMesh.position.set(-0.35, -0.1, 0);
        this.headGroup.add(muzzleMesh);

        // Ears (Long Pendulous Gir Ears)
        const earGeo = new THREE.ConeGeometry(0.12, 0.6, 12);
        earGeo.rotateZ(-Math.PI / 3);
        
        this.leftEar = new THREE.Mesh(earGeo, bodyMat);
        this.leftEar.position.set(0, -0.1, 0.35);
        this.headGroup.add(this.leftEar);

        this.rightEar = new THREE.Mesh(earGeo, bodyMat);
        this.rightEar.position.set(0, -0.1, -0.35);
        this.rightEar.rotation.x = Math.PI;
        this.headGroup.add(this.rightEar);

        // Majestic Curved Horns
        const hornGeo = new THREE.TorusGeometry(0.25, 0.05, 8, 16, Math.PI * 0.8);
        const leftHorn = new THREE.Mesh(hornGeo, hornMat);
        leftHorn.position.set(0.1, 0.25, 0.18);
        leftHorn.rotation.set(0, Math.PI / 4, Math.PI / 2);
        this.headGroup.add(leftHorn);

        const rightHorn = new THREE.Mesh(hornGeo, hornMat);
        rightHorn.position.set(0.1, 0.25, -0.18);
        rightHorn.rotation.set(0, -Math.PI / 4, -Math.PI / 2);
        this.headGroup.add(rightHorn);

        // Eyes
        const eyeGeo = new THREE.SphereGeometry(0.06, 8, 8);
        const leftEye = new THREE.Mesh(eyeGeo, eyeMat);
        leftEye.position.set(-0.15, 0.1, 0.23);
        this.headGroup.add(leftEye);

        const rightEye = new THREE.Mesh(eyeGeo, eyeMat);
        rightEye.position.set(-0.15, 0.1, -0.23);
        this.headGroup.add(rightEye);

        this.cowGroup.add(this.headGroup);

        // Legs (4 legs)
        const legGeo = new THREE.CylinderGeometry(0.1, 0.08, 1.1, 12);
        const legPositions = [
            [-0.6, -0.8, 0.35],
            [-0.6, -0.8, -0.35],
            [0.6, -0.8, 0.35],
            [0.6, -0.8, -0.35]
        ];

        legPositions.forEach(pos => {
            const leg = new THREE.Mesh(legGeo, whiteMat);
            leg.position.set(...pos);
            leg.castShadow = true;
            this.cowGroup.add(leg);
        });

        // Tail
        this.tailGroup = new THREE.Group();
        this.tailGroup.position.set(0.9, 0.2, 0);
        
        const tailGeo = new THREE.CylinderGeometry(0.03, 0.02, 0.9, 8);
        tailGeo.rotateZ(-Math.PI / 8);
        const tailMesh = new THREE.Mesh(tailGeo, bodyMat);
        this.tailGroup.add(tailMesh);

        const tuftGeo = new THREE.ConeGeometry(0.08, 0.25, 8);
        const tuftMesh = new THREE.Mesh(tuftGeo, hornMat);
        tuftMesh.position.set(0.35, -0.45, 0);
        this.tailGroup.add(tuftMesh);

        this.cowGroup.add(this.tailGroup);

        // Rotate cow slightly towards camera
        this.cowGroup.rotation.y = Math.PI / 4;
        this.cowGroup.position.y = 0.2;
    }

    setupEvents() {
        window.addEventListener('resize', () => {
            this.width = this.container.clientWidth;
            this.height = this.container.clientHeight;
            if (this.renderer && this.camera) {
                this.camera.aspect = this.width / this.height;
                this.camera.updateProjectionMatrix();
                this.renderer.setSize(this.width, this.height);
            }
        });

        this.canvas.addEventListener('mousemove', (e) => {
            const rect = this.canvas.getBoundingClientRect();
            this.mouse.x = ((e.clientX - rect.left) / this.width) * 2 - 1;
            this.mouse.y = -((e.clientY - rect.top) / this.height) * 2 + 1;
        });

        this.canvas.addEventListener('click', () => {
            this.triggerReact();
        });
    }

    triggerReact() {
        if (!this.cowGroup) return;
        // Visual reaction: Jump & Nod Head
        const startY = this.cowGroup.position.y;
        let time = 0;
        const jumpInterval = setInterval(() => {
            time += 0.15;
            this.cowGroup.position.y = startY + Math.sin(time) * 0.25;
            this.headGroup.rotation.z = Math.sin(time * 2) * 0.2;
            this.tailGroup.rotation.z = Math.cos(time * 3) * 0.4;
            if (time >= Math.PI) {
                clearInterval(jumpInterval);
                this.cowGroup.position.y = startY;
                this.headGroup.rotation.z = 0;
            }
        }, 30);
    }

    animateThreeJS() {
        requestAnimationFrame(() => this.animateThreeJS());

        const time = Date.now() * 0.002;

        // Breathing animation
        const breath = Math.sin(time * 1.5) * 0.02;
        this.cowGroup.scale.set(1 + breath, 1 + breath, 1 + breath);

        // Head swaying
        this.headGroup.rotation.y = Math.sin(time) * 0.1;
        this.headGroup.rotation.z = Math.cos(time * 0.8) * 0.05;

        // Tail swishing
        this.tailGroup.rotation.z = Math.sin(time * 2.5) * 0.25;

        // Ear twitches
        this.leftEar.rotation.x = Math.sin(time * 4) * 0.1;

        // Mouse follow rotation
        if (this.mouse) {
            this.cowGroup.rotation.y = (Math.PI / 4) + (this.mouse.x * 0.3);
        }

        this.renderer.render(this.scene, this.camera);
    }

    initCanvasFallback() {
        const ctx = this.canvas.getContext('2d');
        this.canvas.width = this.width;
        this.canvas.height = this.height;

        let time = 0;

        const drawFallbackCow = () => {
            ctx.clearRect(0, 0, this.width, this.height);

            time += 0.04;
            const breath = Math.sin(time) * 4;
            const headY = Math.cos(time * 0.8) * 6;

            const cx = this.width / 2;
            const cy = this.height / 2 + 20;

            // Background Sacred Aura
            const grad = ctx.createRadialGradient(cx, cy, 10, cx, cy, 180);
            grad.addColorStop(0, 'rgba(230, 126, 34, 0.2)');
            grad.addColorStop(1, 'transparent');
            ctx.fillStyle = grad;
            ctx.beginPath();
            ctx.arc(cx, cy, 180, 0, Math.PI * 2);
            ctx.fill();

            // Cow Body Torso
            ctx.fillStyle = '#9e4312';
            ctx.beginPath();
            ctx.ellipse(cx, cy + breath, 110, 70, 0, 0, Math.PI * 2);
            ctx.fill();

            // Indigenous Hump
            ctx.beginPath();
            ctx.ellipse(cx - 70, cy - 65 + breath, 35, 45, -0.2, 0, Math.PI * 2);
            ctx.fill();

            // Head
            ctx.beginPath();
            ctx.ellipse(cx - 110, cy - 30 + headY, 40, 30, -0.3, 0, Math.PI * 2);
            ctx.fill();

            // Snout
            ctx.fillStyle = '#d4a373';
            ctx.beginPath();
            ctx.ellipse(cx - 145, cy - 25 + headY, 20, 16, 0, 0, Math.PI * 2);
            ctx.fill();

            // Eye
            ctx.fillStyle = '#111';
            ctx.beginPath();
            ctx.arc(cx - 120, cy - 38 + headY, 5, 0, Math.PI * 2);
            ctx.fill();

            // Horns
            ctx.strokeStyle = '#2c1810';
            ctx.lineWidth = 8;
            ctx.beginPath();
            ctx.arc(cx - 100, cy - 60 + headY, 25, Math.PI * 0.8, Math.PI * 1.8);
            ctx.stroke();

            // Caption
            ctx.font = '700 16px "Noto Serif Kannada", serif';
            ctx.fillStyle = '#D4AF37';
            ctx.textAlign = 'center';
            ctx.fillText('“ಗೋ ಮಾತಾ ಕಿ ಜೈ”', cx, cy + 130);

            requestAnimationFrame(drawFallbackCow);
        };

        drawFallbackCow();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new KamadenuCow3D('cow3d-canvas');
});
