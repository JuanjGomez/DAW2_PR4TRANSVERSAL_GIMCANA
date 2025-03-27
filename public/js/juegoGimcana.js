// Variables globales
let currentCheckpointIndex = 0;
let checkpoints = [];
let userMarker = null;
let map = null;
let completedCheckpoints = new Set();

// Inicializar cuando el documento esté listo
document.addEventListener('DOMContentLoaded', () => {
    // Verificar si tenemos una gimcana activa y los datos necesarios
    const gimcanaId = localStorage.getItem('currentGimcanaId');
    const userId = localStorage.getItem('userId');
    const groupId = localStorage.getItem('currentGroupId');

    if (!gimcanaId || !userId || !groupId) {
        Swal.fire({
            title: 'Error',
            text: 'Faltan datos necesarios para el juego',
            icon: 'error'
        }).then(() => {
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
        const gimcanaId = localStorage.getItem('currentGimcanaId');
        const userId = localStorage.getItem('userId');

        if (!gimcanaId || !userId) {
            console.error('No hay una gimcana activa o usuario');
            return;
        }

        // Cargar los checkpoints
        const response = await fetch(`/api/checkpoints?gimcana_id=${gimcanaId}`);
        const data = await response.json();

        // Asegurarnos de que solo obtenemos los checkpoints de esta gimcana
        checkpoints = data.filter(checkpoint => checkpoint.gimcana_id == gimcanaId);
        checkpoints.sort((a, b) => a.order - b.order);

        // Cargar los checkpoints completados por el usuario
        const completedResponse = await fetch(`/api/user-checkpoints/completed?user_id=${userId}`);
        const completedData = await completedResponse.json();

        // Actualizar el Set de checkpoints completados
        completedCheckpoints = new Set(completedData.map(cp => cp.checkpoint_id));

        // Mostrar la pista del primer checkpoint o el último no completado
        if (checkpoints.length > 0) {
            const currentCheckpoint = checkpoints[currentCheckpointIndex];
            if (!completedCheckpoints.has(currentCheckpoint.id)) {
                showClue(currentCheckpoint);
            }
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
    console.log('Intentando mostrar desafío para:', checkpoint); // Debug log

    if (!isNearCheckpoint(checkpoint)) {
        return;
    }

    if (completedCheckpoints.has(checkpoint.id)) {
        Swal.fire({
            title: 'Checkpoint Completado',
            text: 'Ya has completado este reto. Espera a tus compañeros.',
            icon: 'info'
        });
        return;
    }

    showChallengeContent(checkpoint);
}

function isNearCheckpoint(checkpoint) {
    if (!userMarker || !checkpoint.place) return false;

    const userLatLng = userMarker.getLatLng();
    const checkpointLatLng = L.latLng(checkpoint.place.latitude, checkpoint.place.longitude);
    const distance = userLatLng.distanceTo(checkpointLatLng);

    console.log('Distancia al checkpoint:', distance); // Debug log
    return distance <= 50; // 50 metros de radio
}

// Nueva función para mostrar el contenido del desafío
function showChallengeContent(checkpoint) {
    // Añadir logs para debugging
    console.log('Mostrando desafío para checkpoint:', checkpoint);

    fetch(`/api/checkpoints/${checkpoint.id}/answers`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al obtener las respuestas');
            }
            return response.json();
        })
        .then(answers => {
            console.log('Respuestas recibidas:', answers); // Debug log

            if (!Array.isArray(answers)) {
                throw new Error('Formato de respuestas inválido');
            }

            const answersHtml = answers.map(answer => {
                return `<button class="answer-button swal2-confirm swal2-styled"
                    onclick="verifyAnswer(${checkpoint.id}, ${answer.id})">
                    ${answer.answer}
                </button>`;
            }).join('');

            Swal.fire({
                title: checkpoint.challenge,
                html: `
                    <div class="answers-container" style="display: flex; flex-direction: column; gap: 10px;">
                        ${answersHtml}
                    </div>
                `,
                showConfirmButton: false,
                showCloseButton: true,
                allowOutsideClick: false
            });
        })
        .catch(error => {
            console.error('Error al cargar las respuestas:', error);
            Swal.fire({
                title: 'Error',
                text: 'Hubo un problema al cargar las respuestas',
                icon: 'error'
            });
        });
}

function verifyAnswer(checkpointId, answerId) {
    fetch('/api/challenge-answers/verify', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            checkpoint_id: checkpointId,
            answer_id: answerId  // Ahora enviamos el ID real de la respuesta
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la verificación');
        }
        return response.json();
    })
    .then(data => {
        if (data.correct) {
            Swal.fire({
                title: '¡Correcto!',
                text: 'Has completado el desafío.',
                icon: 'success'
            });
            registerCompletedCheckpoint(checkpointId);
            checkGroupProgress(checkpointId);
        } else {
            Swal.fire({
                title: 'Respuesta incorrecta',
                text: 'Inténtalo de nuevo',
                icon: 'error'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error',
            text: 'Hubo un problema al verificar la respuesta',
            icon: 'error'
        });
    });
}

function registerCompletedCheckpoint(checkpointId) {
    const userId = localStorage.getItem('userId');
    const groupId = localStorage.getItem('currentGroupId');

    if (!userId || !groupId) {
        console.error('Faltan datos de usuario o grupo');
        Swal.fire({
            title: 'Error',
            text: 'No se pudo registrar el progreso',
            icon: 'error'
        });
        return;
    }

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
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error al registrar progreso');
        }
        return response.json();
    })
    .then(() => {
        // Añadir el checkpoint a los completados
        completedCheckpoints.add(checkpointId);
        checkGroupProgress(checkpointId);
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error',
            text: 'No se pudo registrar el progreso',
            icon: 'error'
        });
    });
}

function checkGroupProgress(checkpointId) {
    const groupId = localStorage.getItem('currentGroupId');
    const gimcanaId = localStorage.getItem('currentGimcanaId');

    if (!groupId) {
        console.error('No se encontró el ID del grupo');
        return;
    }

    fetch(`/api/group/${groupId}/checkpoint/${checkpointId}/progress`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al verificar progreso del grupo');
            }
            return response.json();
        })
        .then(data => {
            console.log('Progreso del grupo:', data);

            if (data.allCompleted) {
                // Encontrar el índice del siguiente checkpoint
                let nextCheckpointIndex = checkpoints.findIndex(cp => cp.id === checkpointId) + 1;

                if (nextCheckpointIndex < checkpoints.length) {
                    // Si hay más checkpoints, mostrar la siguiente pista
                    showClue(checkpoints[nextCheckpointIndex]);
                } else {
                    // Si es el último checkpoint, finalizar la gimcana
                    finishGimcana(gimcanaId, groupId);
                }
            } else {
                // Mostrar mensaje de espera con el progreso
                Swal.fire({
                    title: 'Esperando al grupo',
                    text: `${data.completed} de ${data.total} miembros han completado este checkpoint`,
                    icon: 'info'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error',
                text: 'Error al verificar el progreso del grupo',
                icon: 'error'
            });
        });
}

// Nueva función para finalizar la gimcana
function finishGimcana(gimcanaId, groupId) {
    fetch('/api/gimcana/finish', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            gimcana_id: gimcanaId,
            group_id: groupId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: '¡Felicitaciones!',
                text: '¡Han completado todos los checkpoints! La gimcana ha terminado.',
                icon: 'success'
            }).then(() => {
                // Limpiar localStorage
                localStorage.removeItem('currentGroupId');
                localStorage.removeItem('currentGimcanaId');
                localStorage.removeItem('userId');

                // Redireccionar al index
                window.location.href = '/map';
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error',
            text: 'Error al finalizar la gimcana',
            icon: 'error'
        });
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