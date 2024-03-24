// JavaScript pour la gestion de la recherche
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('.search-input');
    const searchButton = document.querySelector('.search-button');

    searchButton.addEventListener('click', function () {
        const username = searchInput.value;
        performSearch(username);
    });

    function performSearch(username) {

    }
});

const container = document.getElementById("minecraft-skin-3d");
const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
camera.position.z = 2;

const renderer = new THREE.WebGLRenderer();
renderer.setSize(container.clientWidth, container.clientHeight);
container.appendChild(renderer.domElement);

const loader = new THREE.JSONLoader();
loader.load("../../storage/geometry/isaucyy3427/isaucyy3427_CustomSlim6f1d93d6-d649-3db0-80bb-e5a169526778.json", (geometry, materials) => {
    const material = new THREE.MeshFaceMaterial(materials);
    const mesh = new THREE.Mesh(geometry, material);
    scene.add(mesh);

    const animate = () => {
        requestAnimationFrame(animate);
        mesh.rotation.x += 0.01;
        mesh.rotation.y += 0.01;
        renderer.render(scene, camera);
    };

    animate();
});
