var places = [];
var gimcanas = [];

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar el mapa
    const map = L.map('map').setView([40.4168, -3.7038], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Variables globales
    window.map = map; // Hacer el mapa accesible globalmente
    window.markers = [];
    window.activeTab = 'places';

    // Configurar los formularios
    setupPlaceForm();
    setupGimcanaForm();
    setupCheckpointForm();
    
    // Cargar todos los datos iniciales
    cargarDatosIniciales();
    
    // Mostrar la pestaña de lugares por defecto
    showTab('places');

    // Configurar pestañas
    const tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tab = this.getAttribute('data-tab');
            showTab(tab);
            // Si la pestaña es checkpoints, recargar los datos
            if (tab === 'checkpoints') {
                cargarDatosIniciales();
            }
        });
    });
});

function showTab(tabName) {
    // Ocultar todas las pestañas
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Mostrar la pestaña seleccionada
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    
    // Actualizar pestaña activa y botones
    activeTab = tabName;
    
    // Limpiar y recargar marcadores según la pestaña
    clearMarkers();
    
    if (tabName === 'places') {
        loadPlaces(map, markers);
    } else if (tabName === 'checkpoints') {
        loadCheckpoints();
    }
    
    // Actualizar botones
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-blue-500', 'text-blue-600');
        if (btn.dataset.tab === tabName) {
            btn.classList.add('border-blue-500', 'text-blue-600');
        }
    });
}

function addAnswer() {
    const container = document.getElementById('answers-container');
    const answerCount = container.children.length;
    
    if (answerCount >= 4) {
        alert('No puedes añadir más de 4 respuestas');
        return;
    }

    const answerDiv = document.createElement('div');
    answerDiv.className = 'answer-container';
    answerDiv.innerHTML = `
        <div class="mb-2">
            <div class="flex justify-between items-center">
                <label class="block text-gray-700">Respuesta ${answerCount + 1}</label>
                <button type="button" onclick="removeAnswer(this)" class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <input type="text" name="answers[${answerCount}][answer]" class="w-full px-4 py-2 border rounded-lg" required>
            <div class="mt-2">
                <label class="inline-flex items-center">
                    <input type="radio" name="correct_answer" value="${answerCount}" class="form-radio" required>
                    <span class="ml-2">Respuesta correcta</span>
                </label>
            </div>
        </div>
    `;
    
    container.appendChild(answerDiv);
}

function removeAnswer(button) {
    const container = button.closest('.answer-container');
    container.remove();
    
    // Reordenar las respuestas restantes
    const answers = document.querySelectorAll('.answer-container');
    answers.forEach((answer, index) => {
        const label = answer.querySelector('label');
        const input = answer.querySelector('input[type="text"]');
        const radio = answer.querySelector('input[type="radio"]');
        
        label.textContent = `Respuesta ${index + 1}`;
        input.name = `answers[${index}][answer]`;
        radio.value = index;
    });
}

function setupPlaceForm(map, markers) {
    const form = document.getElementById('placeForm');
    
    // Añadir marcador al hacer clic en el mapa
    map.on('click', function(e) {
        document.getElementById('latitude').value = e.latlng.lat;
        document.getElementById('longitude').value = e.latlng.lng;
    });
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = {
            name: formData.get('name'),
            address: formData.get('address'),
            latitude: formData.get('latitude'),
            longitude: formData.get('longitude'),
            icon: formData.get('icon') || 'default-icon'
        };
        
        fetch('/places', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Error al crear el lugar');
                });
            }
            return response.json();
        })
        .then(place => {
            Swal.fire({
                icon: 'success',
                title: 'Lugar creado con éxito',
                showConfirmButton: false,
                timer: 1500
            });
            form.reset();
            loadPlaces(map, markers);
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error al crear el lugar',
                text: error.message
            });
        });
    });
}

function setupGimcanaForm() {
    const form = document.getElementById('gimcanaForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = {
            name: formData.get('name'),
            description: formData.get('description')
        };
        
        fetch('/gimcanas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Error al crear la gimcana');
                });
            }
            return response.json();
        })
        .then(gimcana => {
            alert('Gimcana creada con éxito');
            form.reset();
            loadGimcanas();
            updateGimcanasSelect();
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'Error al crear la gimcana');
        });
    });
}

function setupCheckpointForm() {
    const checkpointForm = document.getElementById('checkpointForm');
    if (!checkpointForm) {
        console.error("Formulario de checkpoint no encontrado");
        return;
    }

    checkpointForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const placeId = document.getElementById('cp-place').value;
        const gimcanaId = document.getElementById('cp-gimcana').value;
        const challenge = document.getElementById('cp-challenge').value;
        const clue = document.getElementById('cp-clue').value;
        const order = document.getElementById('cp-order').value;
        
        // Validar que todos los campos obligatorios estén completos
        if (!placeId || !gimcanaId || !challenge || !clue || !order) {
            alert('Por favor completa todos los campos');
            return;
        }
        
        // Datos para enviar
        const formData = {
            place_id: placeId,
            gimcana_id: gimcanaId,
            challenge: challenge,
            clue: clue,
            order: order
        };
        
        // Enviar datos mediante AJAX
        fetch('/api/checkpoints', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(formData)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errData => {
                    throw new Error(errData.errors ? JSON.stringify(errData.errors) : 'Error al guardar el checkpoint');
                });
            }
            return response.json();
        })
        .then(data => {
            console.log("Checkpoint guardado:", data);
            
            // Añadir el nuevo checkpoint a la lista sin recargar todo
            addNewCheckpointToList(data);
            
            // Limpiar el formulario pero mantener los selects
            document.getElementById('cp-challenge').value = '';
            document.getElementById('cp-clue').value = '';
            document.getElementById('cp-order').value = '';
            
            // Mostrar mensaje de éxito
            alert('Punto de control añadido correctamente');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al guardar: ' + error.message);
        });
    });
}

// Función auxiliar para añadir un nuevo checkpoint a la lista sin recargar
function addNewCheckpointToList(checkpoint) {
    const checkpointsList = document.getElementById('checkpointsList');
    if (!checkpointsList) {
        console.error("Elemento checkpointsList no encontrado");
        return;
    }
    
    // Eliminar mensaje de "no hay checkpoints" si existe
    if (checkpointsList.innerHTML.includes('No hay puntos de control registrados')) {
        checkpointsList.innerHTML = '';
    }
    
    // Crear el elemento para el nuevo checkpoint
    const checkpointElement = document.createElement('div');
    checkpointElement.className = 'checkpoint-card p-4 border rounded-lg hover:bg-gray-50';
    
    const placeName = checkpoint.place ? checkpoint.place.name : 'Lugar desconocido';
    const gimcanaName = checkpoint.gimcana ? checkpoint.gimcana.name : 'Gimcana desconocida';
    
    checkpointElement.innerHTML = `
        <h3 class="font-bold">${placeName} (Orden: ${checkpoint.order})</h3>
        <p class="text-gray-600"><strong>Gimcana:</strong> ${gimcanaName}</p>
        <p class="text-gray-600"><strong>Reto:</strong> ${checkpoint.challenge}</p>
        <p class="text-gray-500"><strong>Pista:</strong> ${checkpoint.clue}</p>
        <div class="mt-2">
            <button onclick="deleteCheckpoint(${checkpoint.id})" class="text-red-500 hover:text-red-700">
                Eliminar
            </button>
        </div>
    `;
    
    // Insertar al principio de la lista
    checkpointsList.insertBefore(checkpointElement, checkpointsList.firstChild);
    
    // Si estamos en la pestaña de checkpoints, añadir también al mapa
    if (activeTab === 'checkpoints' && checkpoint.place) {
        try {
            const place = checkpoint.place;
            const marker = L.marker([place.latitude, place.longitude])
                .bindPopup(`<b>${place.name}</b><br>Orden: ${checkpoint.order}<br>${checkpoint.clue}`)
                .addTo(map);
            markers.push(marker);
        } catch (e) {
            console.error("Error al añadir marcador:", e);
        }
    }
}

function loadPlaces(map, markers) {
    // Limpiar marcadores existentes
    if (map && markers) {
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];
    }
    
    return fetch('/places', {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error al cargar los lugares');
        }
        return response.json();
    })
    .then(data => {
        // ESTO ES CRUCIAL: asignar a la variable global
        places = data;
        console.log("Lugares cargados:", places.length);
        
        // Actualizar la lista de lugares
        const placesList = document.getElementById('placesList');
        if (placesList) {
        placesList.innerHTML = '';
            
            places.forEach(place => {
            // Añadir a la lista
            const placeElement = document.createElement('div');
            placeElement.className = 'p-4 border rounded-lg hover:bg-gray-50';
            placeElement.innerHTML = `
                <h3 class="font-bold">${place.name}</h3>
                <p class="text-gray-600">${place.address}</p>
                <p class="text-sm text-gray-500">Lat: ${place.latitude}, Lng: ${place.longitude}</p>
                <div class="mt-2">
                    <button onclick="editPlace(${place.id})" class="bg-yellow-500 text-white py-1 px-3 rounded-lg hover:bg-yellow-600">Editar</button>
                    <button onclick="deletePlace(${place.id})" class="bg-red-500 text-white py-1 px-3 rounded-lg hover:bg-red-600">Eliminar</button>
                </div>
            `;
            placesList.appendChild(placeElement);
            });
        }
        
        // Actualizar el select de lugares
        const placeSelect = document.getElementById('cp-place');
        if (placeSelect) {
            placeSelect.innerHTML = '<option value="">Selecciona un lugar</option>';
            
            places.forEach(place => {
            const option = document.createElement('option');
            option.value = place.id;
            option.textContent = place.name;
            placeSelect.appendChild(option);
        });
        }
        
        // Añadir marcadores al mapa
        if (map) {
            places.forEach(place => {
                const marker = L.marker([place.latitude, place.longitude])
                    .addTo(map)
                    .bindPopup(place.name);
                markers.push(marker);
            });
        }
        
        return places; // Para encadenar
    });
}

function loadGimcanas() {
    return fetch('/gimcanas', {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        // ESTO ES CRUCIAL: asignar a la variable global
        gimcanas = data;
        console.log("Gimcanas cargadas:", gimcanas.length);
        
        // Actualizar el select de gimcanas
        const gimcanaSelect = document.getElementById('cp-gimcana');
        if (gimcanaSelect) {
            gimcanaSelect.innerHTML = '<option value="">Selecciona una gimcana</option>';
            
            gimcanas.forEach(gimcana => {
                const option = document.createElement('option');
                option.value = gimcana.id;
                option.textContent = gimcana.name;
                gimcanaSelect.appendChild(option);
            });
        }
        
        // Actualizar la lista de gimcanas
        const gimcanasList = document.getElementById('gimcanasList');
        if (gimcanasList) {
        gimcanasList.innerHTML = '';
        
        gimcanas.forEach(gimcana => {
            const gimcanaElement = document.createElement('div');
            gimcanaElement.className = 'p-4 border rounded-lg hover:bg-gray-50';
            gimcanaElement.innerHTML = `
                <h3 class="font-bold">${gimcana.name}</h3>
                <p class="text-gray-600">${gimcana.description}</p>
                <div class="mt-2">
                    <button onclick="deleteGimcana(${gimcana.id})" class="text-red-500 hover:text-red-700">
                        Eliminar
                    </button>
                    <button onclick="openEditGimcanaModal(${gimcana.id})" class="text-blue-500 hover:text-blue-700 ml-2">
                        Editar
                    </button>
                </div>
            `;
            gimcanasList.appendChild(gimcanaElement);
        });
        }
        
        return gimcanas; // Para encadenar
    });
}

function updateGimcanasSelect(gimcanas) {
    const gimcanaSelect = document.getElementById('cp-gimcana');
    gimcanaSelect.innerHTML = '<option value="">Selecciona una gimcana</option>';
    
    if (!gimcanas) {
        fetch('/gimcanas', {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            data.forEach(gimcana => {
                const option = document.createElement('option');
                option.value = gimcana.id;
                option.textContent = gimcana.name;
                gimcanaSelect.appendChild(option);
            });
        });
    } else {
        gimcanas.forEach(gimcana => {
            const option = document.createElement('option');
            option.value = gimcana.id;
            option.textContent = gimcana.name;
            gimcanaSelect.appendChild(option);
        });
    }
}

function loadCheckpoints() {
    // Verificar que tenemos datos
    if (!places || places.length === 0 || !gimcanas || gimcanas.length === 0) {
        console.log("Sin datos de lugares o gimcanas para cargar checkpoints");
        console.log("Places:", places);
        console.log("Gimcanas:", gimcanas);
        return;
    }
    
    console.log("Cargando checkpoints...");
    
    fetch('/api/checkpoints')
        .then(response => response.json())
        .then(checkpoints => {
            console.log("Checkpoints recibidos:", checkpoints);
            
            const checkpointsList = document.getElementById('checkpointsList');
            if (!checkpointsList) {
                console.error("Elemento checkpointsList no encontrado");
                return;
            }
            
            checkpointsList.innerHTML = '';
            
            if (checkpoints.length === 0) {
                checkpointsList.innerHTML = '<p class="text-gray-500">No hay puntos de control registrados.</p>';
                return;
            }
        
        checkpoints.forEach(checkpoint => {
                // Usar los datos incluidos en la respuesta API
            const checkpointElement = document.createElement('div');
                checkpointElement.className = 'checkpoint-card p-4 border rounded-lg hover:bg-gray-50';
                
                const placeName = checkpoint.place ? checkpoint.place.name : 'Lugar desconocido';
                const gimcanaName = checkpoint.gimcana ? checkpoint.gimcana.name : 'Gimcana desconocida';

            checkpointElement.innerHTML = `
                    <h3 class="font-bold">${placeName} (Orden: ${checkpoint.order})</h3>
                    <p class="text-gray-600"><strong>Gimcana:</strong> ${gimcanaName}</p>
                <p class="text-gray-600"><strong>Reto:</strong> ${checkpoint.challenge}</p>
                    <p class="text-gray-500"><strong>Pista:</strong> ${checkpoint.clue}</p>
                <div class="mt-2">
                        <button onclick="deleteCheckpoint(${checkpoint.id})" class="text-red-500 hover:text-red-700">
                            Eliminar
                        </button>
                </div>
            `;
                
            checkpointsList.appendChild(checkpointElement);
        });
        })
        .catch(error => {
            console.error("Error cargando checkpoints:", error);
            const checkpointsList = document.getElementById('checkpointsList');
            if (checkpointsList) {
                checkpointsList.innerHTML = '<p class="text-red-500">Error al cargar los puntos de control: ' + error.message + '</p>';
            }
        });
}

function editPlace(id) {
    fetch(`/places/${id}`)
        .then(response => response.json())
        .then(place => {
            document.getElementById('edit-id').value = place.id;
            document.getElementById('edit-name').value = place.name;
            document.getElementById('edit-address').value = place.address;
            document.getElementById('edit-latitude').value = place.latitude;
            document.getElementById('edit-longitude').value = place.longitude;
            document.getElementById('edit-icon').value = place.icon;

            // Limpiar los tags seleccionados
            const tagsContainer = document.getElementById('edit-tags-container');
            tagsContainer.innerHTML = '';

            // Añadir los tags asociados al lugar
            place.tags.forEach(tag => {
                const chip = document.createElement('div');
                chip.className = 'chip';
                chip.innerHTML = `
                    ${tag.name}
                    <span class="chip-remove" onclick="removeTag(${tag.id}, 'edit')">×</span>
                `;
                chip.dataset.id = tag.id;
                tagsContainer.appendChild(chip);
            });

            // Cargar todos los tags disponibles
            fetch('/api/tags')
                .then(response => response.json())
                .then(tags => {
                    const tagsDropdown = document.getElementById('edit-tags-dropdown');
                    tagsDropdown.innerHTML = '';

                    tags.forEach(tag => {
                        const option = document.createElement('div');
                        option.className = 'tag-option';
                        option.textContent = tag.name;
                        option.dataset.id = tag.id;
                        option.addEventListener('click', () => addTag(tag.id, tag.name, 'edit'));
                        tagsDropdown.appendChild(option);
                    });
                });

            document.getElementById('editModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar el lugar');
        });
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

document.getElementById('editPlaceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const tags = Array.from(document.querySelectorAll('#edit-tags-container .chip')).map(chip => parseInt(chip.dataset.id));

    const data = {
        id: formData.get('id'),
        name: formData.get('name'),
        address: formData.get('address'),
        latitude: formData.get('latitude'),
        longitude: formData.get('longitude'),
        icon: formData.get('icon'),
        tags: tags
    };

    fetch(`/places/${data.id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || 'Error al actualizar el lugar');
            });
        }
        return response.json();
    })
    .then(place => {
        Swal.fire({
            icon: 'success',
            title: 'Lugar actualizado con éxito',
            showConfirmButton: false,
            timer: 1500
        });
        closeEditModal();
        loadPlaces(map, markers);
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error al actualizar el lugar',
            text: error.message
        });
    });
});

function addTag(id, name, context = 'create') {
    const tagsContainer = document.getElementById(`${context}-tags-container`);
    const chip = document.createElement('div');
    chip.className = 'chip';
    chip.innerHTML = `
        ${name}
        <span class="chip-remove" onclick="removeTag(${id}, '${context}')">×</span>
    `;
    chip.dataset.id = id;
    tagsContainer.appendChild(chip);
}

function removeTag(id, context = 'create') {
    const chip = document.querySelector(`#${context}-tags-container .chip[data-id="${id}"]`);
    if (chip) {
        chip.remove();
    }
}

document.getElementById('edit-tags-input').addEventListener('focus', function() {
    document.getElementById('edit-tags-dropdown').classList.remove('hidden');
});

document.getElementById('edit-tags-input').addEventListener('blur', function() {
    setTimeout(() => {
        document.getElementById('edit-tags-dropdown').classList.add('hidden');
    }, 200);
});

function deleteGimcana(gimcanaId) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/gimcanas/${gimcanaId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.error || 'Error al eliminar la gimcana');
                    });
                }
                return response.json();
            })
            .then(() => {
                Swal.fire(
                    '¡Eliminado!',
                    'La gimcana ha sido eliminada.',
                    'success'
                );
                loadGimcanas();
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al eliminar la gimcana',
                    text: error.message
                });
            });
        }
    });
}

function openEditGimcanaModal(id) {
    fetch(`/gimcanas/${id}`)
        .then(response => response.json())
        .then(gimcana => {
            document.getElementById('edit-gimcana-id').value = gimcana.id;
            document.getElementById('edit-gimcana-name').value = gimcana.name;
            document.getElementById('edit-gimcana-description').value = gimcana.description;
            document.getElementById('edit-gimcana-max-groups').value = gimcana.max_groups;
            document.getElementById('edit-gimcana-max-users-per-group').value = gimcana.max_users_per_group;

            document.getElementById('editGimcanaModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar la gimcana');
        });
}

function deleteCheckpoint(id) {
    if (confirm('¿Estás seguro de que quieres eliminar este punto de control?')) {
        fetch(`/checkpoints/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (response.ok) {
                loadCheckpoints();
            } else {
                throw new Error('Error al eliminar el punto de control');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el punto de control');
        });
    }
}
