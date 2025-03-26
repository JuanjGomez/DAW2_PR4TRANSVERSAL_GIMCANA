// Inicializar el mapa con opciones de caché
const map = L.map('map', {
    zoomControl: false,
    attributionControl: false,
    maxZoom: 19,
    minZoom: 3,
    maxBounds: [[-90, -180], [90, 180]],
    maxBoundsViscosity: 1.0,
    preferCanvas: true, // Mejor rendimiento en móviles
    renderer: L.canvas({
        padding: 0.5,
        tolerance: 3,
        className: '',
        pane: 'overlayPane',
        attribution: null,
        zoomAnimation: true,
        markerZoomAnimation: true,
        fadeAnimation: true,
        trackResize: true,
        updateWhenIdle: 'ifNotMoving',
        updateWhenZooming: false,
        updateInterval: 25,
        zIndex: 0,
        maxZoom: null,
        maxNativeZoom: null,
        minNativeZoom: null,
        maxBounds: null,
        maxBoundsViscosity: null,
        preferCanvas: true,
        renderer: null,
        rendererOptions: null,
        rendererPane: 'overlayPane',
        rendererAttribution: null,
        rendererZoomAnimation: true,
        rendererMarkerZoomAnimation: true,
        rendererFadeAnimation: true,
        rendererTrackResize: true,
        rendererUpdateWhenIdle: 'ifNotMoving',
        rendererUpdateWhenZooming: false,
        rendererUpdateInterval: 25,
        rendererZIndex: 0,
        rendererMaxZoom: null,
        rendererMaxNativeZoom: null,
        rendererMinNativeZoom: null,
        rendererMaxBounds: null,
        rendererMaxBoundsViscosity: null,
        rendererPreferCanvas: true
    })
}).setView([41.3851, 2.1734], 13);

// Añadir capa de OpenStreetMap con opciones de caché
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: ' OpenStreetMap contributors',
    maxZoom: 19,
    minZoom: 3,
    maxNativeZoom: 19,
    minNativeZoom: 0,
    maxBounds: [[-90, -180], [90, 180]],
    maxBoundsViscosity: 1.0,
    preferCanvas: true,
    renderer: L.canvas({
        padding: 0.5,
        tolerance: 3,
        className: '',
        pane: 'overlayPane',
        attribution: null,
        zoomAnimation: true,
        markerZoomAnimation: true,
        fadeAnimation: true,
        trackResize: true,
        updateWhenIdle: 'ifNotMoving',
        updateWhenZooming: false,
        updateInterval: 25,
        zIndex: 0,
        maxZoom: null,
        maxNativeZoom: null,
        minNativeZoom: null,
        maxBounds: null,
        maxBoundsViscosity: null,
        preferCanvas: true
    })
}).addTo(map);

// Añadir controles de zoom en la esquina inferior derecha
L.control.zoom({
    position: 'bottomright'
}).addTo(map);

// Marcador del usuario
let userMarker;

// Variables globales
let places = []; // Almacenará todos los places
let markers = []; // Almacenará los marcadores del mapa
let selectedTags = new Set(); // Almacenará los tags seleccionados
let favoritePlaces = new Set(); // Almacenará los IDs de los lugares favoritos
let userPosition = null;
let maxDistance = 5; // Distancia máxima en kilómetros
let isFilteringByDistance = false;
let currentGroupId = null;
let currentGimcanaId = null;

// Función para calcular la distancia entre dos puntos en kilómetros
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Radio de la Tierra en km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a =
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

// Función para cargar los places
async function loadPlaces() {
    try {
        let response;
        if (isFilteringByDistance && userPosition) {
            response = await fetch(`/api/places/distance?latitude=${userPosition.lat}&longitude=${userPosition.lng}&distance=${maxDistance}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
        } else {
            response = await fetch('/api/places', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
        }

        if (!response.ok) {
            if (response.status === 401) {
                window.location.href = '/login';
                return;
            }
            throw new Error('Error al cargar los lugares');
        }

        const data = await response.json();
        places = data;
        places = places.map(place => ({
            ...place,
            tags: place.tags || []
        }));
        updateMapMarkers();
    } catch (error) {
        console.error('Error cargando places:', error);
        if (error.message.includes('Unexpected token')) {
            window.location.href = '/login';
        }
    }
}

// Función para cargar los tags
async function loadTags() {
    console.log('Cargando tags...');
    try {
        const response = await fetch('/api/tags', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            if (response.status === 401) {
                window.location.href = '/login';
                return;
            }
            throw new Error('Error al cargar los tags');
        }

        const tags = await response.json();
        console.log('Tags cargados:', tags);
        const tagsList = document.getElementById('tagsList');
        if (tagsList) {
            // Limpiar el contenido actual antes de añadir los nuevos tags
            tagsList.innerHTML = '';
            // Limpiar los tags seleccionados
            selectedTags.clear();

            // Crear un Set para evitar duplicados
            const uniqueTags = new Set();

            // Ordenar los tags por ID para mantener un orden consistente
            const sortedTags = [...tags].sort((a, b) => a.id - b.id);

            sortedTags.forEach(tag => {
                if (!uniqueTags.has(tag.id)) {
                    uniqueTags.add(tag.id);
                    const tagElement = document.createElement('div');
                    tagElement.className = `tag-chip ${selectedTags.has(tag.id) ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'}
                        px-4 py-2 rounded-full cursor-pointer transition-colors duration-200`;
                    tagElement.dataset.id = tag.id;
                    tagElement.textContent = tag.name;
                    tagElement.onclick = () => toggleTag(tag.id);
                    tagsList.appendChild(tagElement);
                }
            });
        }
    } catch (error) {
        console.error('Error cargando tags:', error);
        if (error.message.includes('Unexpected token')) {
            window.location.href = '/login';
        }
    }
}

// Función para alternar la selección de un tag
function toggleTag(tagId) {
    if (selectedTags.has(tagId)) {
        selectedTags.delete(tagId);
    } else {
        selectedTags.add(tagId);
    }
    // Actualizar la apariencia del chip
    const tagChip = document.querySelector(`.tag-chip[data-id="${tagId}"]`);
    if (tagChip) {
        tagChip.classList.toggle('bg-blue-500');
        tagChip.classList.toggle('text-white');
        tagChip.classList.toggle('bg-gray-200');
        tagChip.classList.toggle('text-gray-700');
    }
}

// Función para actualizar los marcadores en el mapa
function updateMapMarkers() {
    // Limpiar marcadores existentes
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];

    // Filtrar places según los tags seleccionados
    const filteredPlaces = places.filter(place => {
        if (selectedTags.size === 0) return true;
        return place.tags && place.tags.some(tag => selectedTags.has(tag.id));
    });

    // Añadir marcadores al mapa
    filteredPlaces.forEach(place => {
        const marker = L.marker([place.latitude, place.longitude]).addTo(map);
        marker.bindPopup(`<b>${place.name}</b><br>${place.address}`);

        marker.on('click', () => {
            showPlaceDetails(place);
        });

        markers.push(marker);
    });
}

// Función para mostrar los lugares favoritos
function showFavorites() {
    // Filtrar los lugares favoritos
    const favoritePlacesList = places.filter(place => favoritePlaces.has(place.id));

    // Limpiar marcadores existentes
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];

    // Añadir solo los marcadores de lugares favoritos
    favoritePlacesList.forEach(place => {
        const marker = L.marker([place.latitude, place.longitude]).addTo(map);
        marker.bindPopup(`<b>${place.name}</b><br>${place.address}`);

        // Agregar evento click al marcador
        marker.on('click', () => {
            showPlaceDetails(place, true); // true indica que es un lugar favorito
        });

        markers.push(marker);
    });

    // Actualizar el estado del botón de favoritos
    const favoritesBtn = document.getElementById('favoritesBtn');
    if (favoritesBtn) {
        favoritesBtn.classList.toggle('bg-blue-500');
        favoritesBtn.classList.toggle('bg-gray-700');
    }
}

// Función para mostrar los detalles del lugar
function showPlaceDetails(place, isFavorite = false) {
    console.log('Mostrando detalles del lugar:', place);

    // Actualizar el contenido del modal
    document.getElementById('placeName').textContent = place.name;
    document.getElementById('placeAddress').textContent = place.address;
    document.getElementById('placeDescription').textContent = place.description || 'Sin descripción';

    // Obtener los botones del modal
    const closeBtn = document.getElementById('closePlaceModal');
    const actionBtn = document.getElementById('addToFavorites');

    if (isFavorite) {
        // Si es un lugar favorito, cambiar el botón a "Eliminar"
        actionBtn.textContent = 'Eliminar de favoritos';
        actionBtn.classList.remove('bg-blue-500', 'hover:bg-blue-600');
        actionBtn.classList.add('bg-red-500', 'hover:bg-red-600');
        actionBtn.onclick = () => removeFromFavorites(place.id);
    } else {
        // Si no es favorito, mostrar el botón de añadir a favoritos
        actionBtn.textContent = favoritePlaces.has(place.id) ? 'En favoritos' : 'Añadir a favoritos';
        actionBtn.classList.remove('bg-red-500', 'hover:bg-red-600');
        actionBtn.classList.add('bg-blue-500', 'hover:bg-blue-600');
        actionBtn.disabled = favoritePlaces.has(place.id);
        actionBtn.onclick = () => addPlaceToFavorites(place.id);
    }

    // Mostrar el modal
    const modal = document.getElementById('placeDetailsModal');
    modal.classList.remove('hidden');
}

// Función para cargar los lugares favoritos
async function loadFavoritePlaces() {
    try {
        const response = await fetch('/api/favorite-places', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            if (response.status === 401) {
                window.location.href = '/login';
                return;
            }
            throw new Error('Error al cargar los lugares favoritos');
        }

        const data = await response.json();
        favoritePlaces = new Set(data.map(place => place.id));
        updateFavoritesButton();
    } catch (error) {
        console.error('Error cargando lugares favoritos:', error);
    }
}

// Función para actualizar el botón de favoritos
function updateFavoritesButton() {
    const favoritesBtn = document.getElementById('favoritesBtn');
    if (favoritesBtn) {
        favoritesBtn.innerHTML = `Favoritos (${favoritePlaces.size})`;
    }
}

// Función para añadir un lugar a favoritos
async function addPlaceToFavorites(placeId) {
    try {
        const response = await fetch('/api/favorite-places', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ place_id: placeId })
        });

        if (!response.ok) {
            throw new Error('Error al añadir a favoritos');
        }

        favoritePlaces.add(placeId);
        updateFavoritesButton();

        Swal.fire({
            icon: 'success',
            title: '¡Añadido a favoritos!',
            showConfirmButton: false,
            timer: 1500
        });

        // Actualizar el botón en el modal
        const addToFavoritesBtn = document.getElementById('addToFavorites');
        if (addToFavoritesBtn) {
            addToFavoritesBtn.disabled = true;
            addToFavoritesBtn.textContent = 'En favoritos';
            addToFavoritesBtn.classList.remove('bg-blue-500', 'hover:bg-blue-600');
            addToFavoritesBtn.classList.add('bg-green-500', 'cursor-not-allowed');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo añadir a favoritos'
        });
    }
}

// Función para eliminar un lugar de favoritos
async function removeFromFavorites(placeId) {
    try {
        const response = await fetch(`/api/favorite-places/${placeId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!response.ok) {
            throw new Error('Error al eliminar de favoritos');
        }

        favoritePlaces.delete(placeId);
        updateFavoritesButton();

        // Cerrar el modal
        document.getElementById('placeDetailsModal').classList.add('hidden');

        // Actualizar los marcadores si estamos en la vista de favoritos
        const favoritesBtn = document.getElementById('favoritesBtn');
        if (favoritesBtn && favoritesBtn.classList.contains('bg-blue-500')) {
            showFavorites();
        }

        Swal.fire({
            icon: 'success',
            title: '¡Eliminado de favoritos!',
            showConfirmButton: false,
            timer: 1500
        });
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo eliminar de favoritos'
        });
    }
}

// Eventos para los modales
document.getElementById('closePlaceModal').addEventListener('click', () => {
    document.getElementById('placeDetailsModal').classList.add('hidden');
});

document.getElementById('closeFavoritesModal').addEventListener('click', () => {
    document.getElementById('favoritesModal').classList.add('hidden');
});

// Evento para abrir/cerrar la sección de filtros
document.getElementById('filtrosBtn').addEventListener('click', () => {
    const filtersSection = document.getElementById('filtersSection');
    const isHidden = filtersSection.classList.contains('hidden');
    filtersSection.classList.toggle('hidden');

    if (!isHidden) {
        // Si estamos cerrando los filtros, limpiar los tags seleccionados
        selectedTags.clear();
        updateMapMarkers();
    } else {
        // Si estamos abriendo los filtros, cargar los tags
        loadTags();
    }
});

// Evento para aplicar los filtros
document.getElementById('applyFilters').addEventListener('click', () => {
    updateMapMarkers();
});

// Evento para el control deslizante de distancia
document.getElementById('distanceSlider').addEventListener('input', function(e) {
    maxDistance = parseFloat(e.target.value);
    document.getElementById('distanceValue').textContent = maxDistance;
    isFilteringByDistance = true;
    loadPlaces();
});

// Obtener y mostrar la ubicación del usuario
if (navigator.geolocation) {
    navigator.geolocation.watchPosition(
        (position) => {
            const { latitude, longitude } = position.coords;
            userPosition = { lat: latitude, lng: longitude };

            // Si ya existe un marcador, actualizar su posición
            if (userMarker) {
                userMarker.setLatLng([latitude, longitude]);
            } else {
                // Crear nuevo marcador circular
                userMarker = L.circleMarker([latitude, longitude], {
                    radius: 10,
                    fillColor: '#4285F4',
                    color: '#ffffff',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 1
                }).addTo(map);
                map.setView([latitude, longitude], 15);
            }

            // Si estamos filtrando por distancia, actualizar los lugares
            if (isFilteringByDistance) {
                loadPlaces();
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

// Función para limpiar la caché
function clearCache() {
    // Limpiar la caché de lugares
    places = [];
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];

    // Recargar los lugares
    loadPlaces();

    // Limpiar la caché de tags
    selectedTags.clear();
    loadTags();

    // Limpiar la caché de lugares favoritos
    favoritePlaces.clear();
    loadFavoritePlaces();
}

// Limpiar la caché cada 5 minutos
setInterval(clearCache, 5 * 60 * 1000);

// Limpiar la caché cuando se cambia de pestaña
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        clearCache();
    }
});

// Cargar lugares favoritos al iniciar
loadFavoritePlaces();

// Cargar los places al iniciar
loadPlaces();

// Función para limpiar el filtro de distancia
function clearDistanceFilter() {
    maxDistance = 5;
    isFilteringByDistance = false;
    document.getElementById('distanceSlider').value = maxDistance;
    document.getElementById('distanceValue').textContent = maxDistance;
    loadPlaces();
}

// Evento para el botón de limpiar filtro de distancia
document.getElementById('clearDistanceFilter').addEventListener('click', clearDistanceFilter);

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
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({group_id: grupoId})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentGroupId = grupoId;
            currentGimcanaId = data.group.gimcana_id;
            const gimcanaModal = document.getElementById('gimcanaDetailsModal');
            const groupModal = document.getElementById('groupDetailsModal');
            const groupContent = document.getElementById('groupDetailsContent');

            if (data.group && data.group.name) {
                groupContent.innerHTML = `
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold">Grupo: ${data.group.name}</h2>
                    </div>
                    <h3 class="text-xl font-bold mt-4">Miembros:</h3>
                    <ul>
                        ${data.group.members.map(member => `<li>${member.name}</li>`).join('')}
                    </ul>
                `;
                gimcanaModal.classList.add('hidden');
                groupModal.classList.remove('hidden');

                // Asegúrate de que el botón existe antes de agregar el evento
                const closeButton = document.getElementById('closeGroupDetailsModal');
                if (closeButton) {
                    closeButton.addEventListener('click', () => {
                        groupModal.classList.add('hidden');
                    });
                } else {
                    console.error('El botón closeGroupDetailsModal no existe');
                }
            } else {
                console.error('El objeto group no está definido o no tiene la propiedad name');
            }
        } else {
            Swal.fire({
                title: 'Error al unirte al grupo',
                text: data.message,
                icon: 'error'
            });
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

document.getElementById('closeGroupDetailsModal').addEventListener('click', () => {
    document.getElementById('groupDetailsModal').classList.add('hidden');
});

function updateGroupDetails(groupId) {
    fetch(`/api/group/${groupId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(group => {
            const groupContent = document.getElementById('groupDetailsContent');
            groupContent.innerHTML = `
                <h2 class="text-2xl font-bold">Grupo: ${group.name}</h2>
                <h3 class="text-xl font-bold mt-4">Miembros:</h3>
                <ul>
                    ${group.members.map(member => `<li>${member.name}</li>`).join('')}
                </ul>
            `;
        })
        .catch(error => console.error('Error al actualizar los detalles del grupo:', error));
}

setInterval(() => {
    if (currentGroupId) {
        updateGroupDetails(currentGroupId);
    }
}, 5000); // Actualiza cada 5 segundos

function checkIfGimcanaReady(gimcanaId) {
    fetch(`/api/gimcanas/${gimcanaId}/ready`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.ready) {
            // Guardar el ID de la gimcana antes de redirigir
            localStorage.setItem('currentGimcanaId', gimcanaId);
            window.location.href = '/map/juego';
        }
    })
    .catch(error => console.error('Error al verificar si la gimcana está lista:', error));
}

// Llama a esta función periódicamente
setInterval(() => {
    if (currentGimcanaId) {
        checkIfGimcanaReady(currentGimcanaId);
    }
}, 5000); // Verifica cada 5 segundos
