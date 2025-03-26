// Variables globales
let currentCheckpointIndex = 0;
let checkpoints = [];
let userMarker = null;
let map = null;

// Inicializar cuando el documento esté listo
document.addEventListener('DOMContentLoaded', () => {
    // Verificar si tenemos una gimcana activa
    const gimcanaId = localStorage.getItem('currentGimcanaId');
    if (!gimcanaId) {
        Swal.fire({
            title: 'Error',
            text: 'No se encontró una gimcana activa',
            icon: 'error'
        }).then(() => {
            // Redirigir al usuario a la página principal
            window.location.href = '/map';
        });
        return;
    }

    // Inicializar el mapa y cargar los checkpoints
    initializeMap();
    loadCheckpoints();
});

function initializeMap() {
    map = L.map('map').setView([41.3851, 2.1734], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map);
}

async function loadCheckpoints() {
    try {
        // Obtener el ID de la gimcana actual
        const gimcanaId = localStorage.getItem('currentGimcanaId');

        if (!gimcanaId) {
            console.error('No hay una gimcana activa');
            return;
        }

        // Modificar la URL para incluir el filtro por gimcana_id
        const response = await fetch(`/api/checkpoints?gimcana_id=${gimcanaId}`);
        const data = await response.json();

        // Asegurarnos de que solo obtenemos los checkpoints de esta gimcana
        checkpoints = data.filter(checkpoint => checkpoint.gimcana_id == gimcanaId);

        // Ordenar checkpoints por orden
        checkpoints.sort((a, b) => a.order - b.order);

        // Mostrar la pista del primer checkpoint
        if (checkpoints.length > 0) {
            showClue(checkpoints[0]);
        } else {
            console.error('No hay checkpoints para esta gimcana');
        }
    } catch (error) {
        console.error('Error al cargar los checkpoints:', error);
    }
}

function showClue(checkpoint) {
    Swal.fire({
        title: 'Nueva pista',
        text: checkpoint.clue,
        icon: 'info',
        confirmButtonText: 'Entendido'
    });
}

function showChallenge(checkpoint) {
    if (!checkpoint.challenge) {
        Swal.fire({
            title: '¡Felicidades!',
            text: 'Has llegado al último checkpoint',
            icon: 'success'
        });
        return;
    }

    // Obtener las respuestas del checkpoint
    fetch(`/api/checkpoints/${checkpoint.id}/answers`)
        .then(response => response.json())
        .then(answers => {
            const answerButtons = answers.map(answer => ({
                text: answer.answer,
                value: answer.id
            }));

            Swal.fire({
                title: checkpoint.challenge,
                input: 'radio',
                inputOptions: Object.fromEntries(
                    answerButtons.map(btn => [btn.value, btn.text])
                ),
                showCancelButton: true,
                confirmButtonText: 'Confirmar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    verifyAnswer(checkpoint.id, result.value);
                }
            });
        });
}

function verifyAnswer(checkpointId, answerId) {
    const groupId = localStorage.getItem('currentGroupId');

    fetch('/api/challenge-answers/verify', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            checkpoint_id: checkpointId,
            answer_id: answerId,
            group_id: groupId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.correct) {
            // Registrar el checkpoint como completado
            registerCompletedCheckpoint(checkpointId);

            // Verificar si todos los miembros del grupo han completado el checkpoint
            checkGroupProgress(checkpointId);
        } else {
            Swal.fire({
                title: 'Respuesta incorrecta',
                text: 'Inténtalo de nuevo',
                icon: 'error'
            });
        }
    });
}

function registerCompletedCheckpoint(checkpointId) {
    const userId = localStorage.getItem('userId');
    const groupId = localStorage.getItem('currentGroupId');

    fetch('/api/user-checkpoints', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            user_id: userId,
            group_id: groupId,
            checkpoint_id: checkpointId,
            completed: true
        })
    });
}

function checkGroupProgress(checkpointId) {
    const groupId = localStorage.getItem('currentGroupId');

    fetch(`/api/group/${groupId}/checkpoint/${checkpointId}/progress`)
        .then(response => response.json())
        .then(data => {
            if (data.allCompleted) {
                currentCheckpointIndex++;
                if (currentCheckpointIndex < checkpoints.length) {
                    // Mostrar la pista del siguiente checkpoint
                    showClue(checkpoints[currentCheckpointIndex]);
                }
            } else {
                Swal.fire({
                    title: '¡Bien hecho!',
                    text: 'Esperando a que tus compañeros completen el reto',
                    icon: 'success'
                });
            }
        });
}

// Función de geolocalización
navigator.geolocation.watchPosition((position) => {
    const { latitude, longitude } = position.coords;

    if (!userMarker) {
        map.setView([latitude, longitude], 15);
        userMarker = L.circleMarker([latitude, longitude], {
            radius: 8,
            fillColor: '#007bff',
            color: '#fff',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.8
        }).addTo(map);
    } else {
        userMarker.setLatLng([latitude, longitude]);
    }

    // Solo verificar el checkpoint actual
    const currentCheckpoint = checkpoints[currentCheckpointIndex];
    if (currentCheckpoint && checkProximity(latitude, longitude, currentCheckpoint.place.latitude, currentCheckpoint.place.longitude)) {
        showChallenge(currentCheckpoint);
    }
});

// Función para verificar la proximidad entre dos puntos
function checkProximity(lat1, lon1, lat2, lon2) {
    // Radio de la Tierra en metros
    const R = 6371e3;

    // Convertir coordenadas a radianes
    const φ1 = lat1 * Math.PI/180;
    const φ2 = lat2 * Math.PI/180;
    const Δφ = (lat2-lat1) * Math.PI/180;
    const Δλ = (lon2-lon1) * Math.PI/180;

    // Fórmula haversine
    const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
              Math.cos(φ1) * Math.cos(φ2) *
              Math.sin(Δλ/2) * Math.sin(Δλ/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    const distance = R * c;

    // Retorna true si la distancia es menor a 50 metros
    return distance < 50;
}