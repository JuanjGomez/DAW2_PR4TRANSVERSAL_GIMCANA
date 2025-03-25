// Inicializar el mapa
const map = L.map('map').setView([41.3851, 2.1734], 13);

// Añadir capa de OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: ' OpenStreetMap contributors'
}).addTo(map);

// Marcador del usuario
let userMarker;

// Obtener y mostrar la ubicación del usuario
if (navigator.geolocation) {
    navigator.geolocation.watchPosition(
        (position) => {
            const { latitude, longitude } = position.coords;

            // Si ya existe un marcador, actualizar su posición
            if (userMarker) {
                userMarker.setLatLng([latitude, longitude]);
            } else {
                // Crear nuevo marcador circular
                userMarker = L.circleMarker([latitude, longitude], {
                    radius: 10,
                    fillColor: '#4285F4', // Color azul de Google Maps
                    color: '#ffffff',     // Borde blanco
                    weight: 2,            // Grosor del borde
                    opacity: 1,
                    fillOpacity: 1
                }).addTo(map);
                map.setView([latitude, longitude], 15);
            }
        },
        (error) => {
            console.error('Error al obtener la ubicación:', error);
        },
        {
            enableHighAccuracy: true,
            maximumAge: 30000,
            timeout: 27000
        }
    );
}

// Eventos para los botones
document.getElementById('filtrosBtn').addEventListener('click', () => {
    // Aquí irá la lógica para abrir el modal de Filtros
    console.log('Abrir modal de Filtros')
})

document.getElementById('lobbiesBtn').addEventListener('click', () => {
    document.getElementById('gimcanaModal').classList.remove('hidden')
    loadGimcanas()
})

document.getElementById('closeModal').addEventListener('click', () => {
    document.getElementById('gimcanaModal').classList.add('hidden')
})

document.getElementById('gimcanaModal').addEventListener('click', (event) => {
    if (event.target === event.currentTarget) {
        document.getElementById('gimcanaModal').classList.add('hidden')
    }
})

function loadGimcanas() {
    fetch('/api/gimcanas')
        .then(response => response.json())
        .then(gimcanas => {
            const list = document.getElementById('gimcanaList')
            list.innerHTML = ''
            gimcanas.forEach(gimcana => {
                console.log(gimcana)
                const maxPlayers = gimcana.max_groups * gimcana.max_users_per_group
                const currentPlayers = gimcana.current_players
                console.log(currentPlayers)
                const card = document.createElement('div')
                card.className = 'gimcana-card'
                card.innerHTML = `
                    <span>${gimcana.name}</span>
                    <span>${currentPlayers} / ${maxPlayers} <i class="fas fa-user icon"></i></span>
                `
                card.addEventListener('click', () => {
                    document.getElementById('gimcanaModal').classList.add('hidden')
                    openGimcanaDetails(gimcana.id)
                })
                list.appendChild(card)
            })
        })
        .catch(error => console.error('Error al cargar las gimcanas:', error))
}

function openGimcanaDetails(gimcanaId) {
    fetch(`/api/gimcanas/${gimcanaId}`)
        .then(response => response.json())
        .then(gimcana => {
            const modal = document.getElementById('gimcanaDetailsModal');
            const modalContent = document.getElementById('gimcanaDetailsContent');
            modalContent.innerHTML = `
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold">${gimcana.name}</h2>
                    <button id="closeDetailsModal" class="text-red-500 hover:text-red-700 text-2xl font-bold">&times;</button>
                </div>
                <h3 class="text-xl font-bold mt-4">Grupos:</h3>
                <div class="group-list">
                    ${gimcana.groups.map(group => `
                        <div class="group-container">
                            <p>${group.name || 'Sin nombre'}</p>
                            <p>${group.members ? group.members.length : 0} / ${gimcana.max_users_per_group} <i class="fas fa-user icon"></i></p>
                            ${group.members && group.members.length >= gimcana.max_users_per_group ? '<p>Grupo lleno</p>' : `<button class="join-group" data-group-id="${group.id}">Unirse</button>`}
                        </div>
                    `).join('')}
                </div>
            `
            modal.classList.remove('hidden')

            document.getElementById('closeDetailsModal').addEventListener('click', () => {
                modal.classList.add('hidden')
            })

            document.querySelectorAll('.join-group').forEach(button => {
                button.addEventListener('click', (event) => {
                    const groupId = event.target.dataset.groupId;
                    joinGroup(groupId);
                });
            })
        })
        .catch(error => console.error('Error al cargar los detalles de la gimcana:', error))
}

function joinGroup(grupoId) {
    fetch(`/api/group/join`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({group_id: grupoId})
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            Swal.fire({
                title: 'Ya estás en el grupo',
                icon: 'info',
                confirmButtonText: 'OK'
            }).then(() => {
                const modalContent = document.getElementById('gimcanaDetailsContent')
                modalContent.innerHTML = `
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold">Grupo: ${data.group.name}</h2>
                        <button id="closeMembersModal" class="text-red-500 hover:text-red-700 text-2xl font-bold">&times;</button>
                    </div>
                    <h3 class="text-xl font-bold mt-4">Miembros:</h3>
                    <ul>
                        ${data.group.members.map(member => `<li>${member.name}</li>`).join('')}
                    </ul>
                `;
                document.getElementById('gimcanaDetailsModal').classList.remove('hidden')

                document.getElementById('closeMembersModal').addEventListener('click', function() {
                    Swal.fire({
                        title: '¿Estás seguro de salir del grupo?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, salir',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            leaveGroup(grupoId) // Asegúrate de tener el grupoId disponible
                        }
                    })
                })
            })
        } else {
            Swal.fire({
                title: 'Error al unirte al grupo',
                text: data.message,
                icon: 'error'
            })
        }
    })
    .catch(error => console.error('Error al unirte al grupo:', error))
}

function checkUserGroupStatus(gimcanaId) {
    fetch(`/api/user/group-status/${gimcanaId}`)
        .then(response => response.json())
        .then(data => {
            if (data.inGroup) {
                // Mostrar el grupo actual del usuario
                openGimcanaDetails(data.groupId)
            } else {
                // Permitir al usuario unirse a un grupo
                loadGimcanas()
            }
        })
        .catch(error => console.error('Error al verificar el estado del grupo del usuario:', error))
}

function leaveGroup(grupoId) {
    fetch(`/api/group/leave`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({group_id: grupoId})
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            Swal.fire({
                title: 'Has salido del grupo',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                document.getElementById('gimcanaDetailsModal').classList.add('hidden')
            });
        } else {
            Swal.fire({
                title: 'Error al salir del grupo',
                text: data.message,
                icon: 'error'
            })
        }
    })
    .catch(error => console.error('Error al salir del grupo:', error))
}
