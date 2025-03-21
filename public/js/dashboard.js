document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    window.markers = [];
    window.activeTab = 'places';
    window.answerCount = 1;
    window.selectedTags = [];
    window.placeSelectedTags = [];
    window.allTags = [];

    // Inicializar el mapa
    const mapElement = document.getElementById('map');
    if (mapElement) {
        window.map = L.map('map').setView([41.390205, 2.154007], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(window.map);
    }

    // Configurar los formularios
    setupPlaceForm(window.map);
    setupGimcanaForm();
    setupCheckpointForm();

    // Cargar datos iniciales
    loadPlaces(window.map);
    loadGimcanas();
    loadCheckpoints();
    loadTags();

    // Mostrar la pestaña de lugares por defecto
    showTab('places');
});

function showTab(tabName) {
    // Ocultar todas las pestañas
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Mostrar la pestaña seleccionada
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    
    // Actualizar los botones
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

function setupPlaceForm(map) {
    const form = document.getElementById('place-form');
    if (!form) return;
    
    // Añadir marcador al hacer clic en el mapa
    map.on('click', function(e) {
        document.getElementById('latitude').value = e.latlng.lat;
        document.getElementById('longitude').value = e.latlng.lng;
    });
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = {
            name: formData.get('name'),
            address: formData.get('address'),
            latitude: formData.get('latitude'),
            longitude: formData.get('longitude'),
            icon: formData.get('icon') || 'default-icon',
            tags: window.placeSelectedTags
        };
        
        try {
            const response = await fetch('/places', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'Error al crear el lugar');
            }

            const place = await response.json();
            form.reset();
            window.placeSelectedTags = [];
            updatePlaceTagsUI();
            loadPlaces(map);
            showSuccess('Lugar creado exitosamente');
        } catch (error) {
            console.error('Error:', error);
            showError(error.message || 'Error al crear el lugar');
        }
    });
}

function setupGimcanaForm() {
    const form = document.getElementById('gimcanaForm');
    if (!form) return;
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const maxGroups = parseInt(formData.get('max_groups'));
        const maxUsersPerGroup = parseInt(formData.get('max_users_per_group'));

        // Validar que los valores son números válidos
        if (isNaN(maxGroups) || maxGroups <= 0) {
            showError('El número máximo de grupos debe ser un número positivo');
            return;
        }

        if (isNaN(maxUsersPerGroup) || maxUsersPerGroup <= 0) {
            showError('El número máximo de usuarios por grupo debe ser un número positivo');
            return;
        }

        const data = {
            name: formData.get('name'),
            description: formData.get('description'),
            max_groups: maxGroups,
            max_users_per_group: maxUsersPerGroup
        };
        
        try {
            const response = await fetch('/gimcanas', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'Error al crear la gimcana');
            }

            const gimcana = await response.json();
            form.reset();
            loadGimcanas();
            updateGimcanasSelect();
            showSuccess('Gimcana creada exitosamente');
        } catch (error) {
            console.error('Error:', error);
            showError(error.message || 'Error al crear la gimcana');
        }
    });
}

function setupCheckpointForm() {
    const form = document.getElementById('checkpointForm');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const checkpointData = {
            place_id: formData.get('place_id'),
            gimcana_id: formData.get('gimcana_id'),
            challenge: formData.get('challenge'),
            clue: formData.get('clue'),
            order: formData.get('order')
        };
        
        try {
            // Primero crear el checkpoint
            const checkpointResponse = await fetch('/checkpoints', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(checkpointData)
            });

            if (!checkpointResponse.ok) {
                const error = await checkpointResponse.json();
                throw new Error(error.message || 'Error al crear el punto de control');
            }

            const checkpoint = await checkpointResponse.json();

            // Luego crear las respuestas
            const answers = [];
            const correctAnswerIndex = formData.get('correct_answer');
            
            document.querySelectorAll('.answer-container').forEach((container, index) => {
                const answerText = container.querySelector('input[type="text"]').value;
                answers.push({
                    answer: answerText,
                    is_correct: index.toString() === correctAnswerIndex
                });
            });

            const answersResponse = await fetch('/challenge-answers', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    checkpoint_id: checkpoint.id,
                    answers: answers
                })
            });

            if (!answersResponse.ok) {
                const error = await answersResponse.json();
                throw new Error(error.message || 'Error al crear las respuestas');
            }

            alert('Punto de control y respuestas creados con éxito');
            form.reset();
            document.getElementById('answers-container').innerHTML = `
                <div class="answer-container">
                    <div class="mb-2">
                        <label class="block text-gray-700">Respuesta 1</label>
                        <input type="text" name="answers[0][answer]" class="w-full px-4 py-2 border rounded-lg" required>
                        <div class="mt-2">
                            <label class="inline-flex items-center">
                                <input type="radio" name="correct_answer" value="0" class="form-radio" required>
                                <span class="ml-2">Respuesta correcta</span>
                            </label>
                        </div>
                    </div>
                </div>
            `;
            loadCheckpoints();

        } catch (error) {
            console.error('Error:', error);
            alert(error.message || 'Error al crear el punto de control y sus respuestas');
        }
    });
}

function loadPlaces(map) {
    // Limpiar marcadores existentes si hay mapa
    if (map) {
        window.markers.forEach(marker => map.removeLayer(marker));
        window.markers = [];
    }
    
    let url = '/places';
    if (window.selectedTags.length > 0) {
        url += `?tag_id=${window.selectedTags[0]}`;
    }
    
    fetch(url, {
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
    .then(places => {
        // Actualizar la lista de lugares
        const placesList = document.getElementById('places-list');
        if (placesList) {
            placesList.innerHTML = '';
            
            places.forEach(place => {
                const placeElement = document.createElement('div');
                placeElement.className = 'bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow';
                placeElement.innerHTML = `
                    <h3 class="font-bold text-lg">${place.name}</h3>
                    <p class="text-gray-600">${place.address}</p>
                    <p class="text-sm text-gray-500">Lat: ${place.latitude}, Lng: ${place.longitude}</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        ${place.tags ? place.tags.map(tag => `<span class="tag">${tag.name}</span>`).join('') : ''}
                    </div>
                    <button onclick="deletePlace(${place.id})" class="mt-2 text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Eliminar
                    </button>
                `;
                placesList.appendChild(placeElement);
            });
        }
        
        // Actualizar el select de lugares en el formulario de checkpoints
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
        
        // Actualizar marcadores en el mapa si existe
        if (map) {
            places.forEach(place => {
                const marker = L.marker([place.latitude, place.longitude])
                    .addTo(map)
                    .bindPopup(place.name);
                window.markers.push(marker);
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Error al cargar los lugares');
    });
}

function loadGimcanas() {
    fetch('/gimcanas', {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error al cargar las gimcanas');
        }
        return response.json();
    })
    .then(gimcanas => {
        const gimcanasList = document.getElementById('gimcanasList');
        if (!gimcanasList) return;
        gimcanasList.innerHTML = '';
        
        // Actualizar el select de gimcanas en el formulario de checkpoints
        updateGimcanasSelect(gimcanas);
        
        gimcanas.forEach(gimcana => {
            const gimcanaElement = document.createElement('div');
            gimcanaElement.className = 'bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow';
            gimcanaElement.innerHTML = `
                <h3 class="font-bold text-lg">${gimcana.name}</h3>
                <p class="text-gray-600">${gimcana.description}</p>
                <div class="mt-2 text-sm text-gray-500">
                    <p>Máximo de grupos: ${gimcana.max_groups}</p>
                    <p>Máximo de usuarios por grupo: ${gimcana.max_users_per_group}</p>
                    <p>Estado: ${gimcana.status}</p>
                </div>
                <button onclick="deleteGimcana(${gimcana.id})" class="mt-2 text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Eliminar
                </button>
            `;
            gimcanasList.appendChild(gimcanaElement);
        });
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Error al cargar las gimcanas');
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

async function loadCheckpoints() {
    try {
        const response = await fetch('/checkpoints', {
            headers: {
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Error al cargar los puntos de control');
        }

        const checkpoints = await response.json();
        const checkpointsList = document.getElementById('checkpointsList');
        checkpointsList.innerHTML = '';
        
        for (const checkpoint of checkpoints) {
            // Cargar las respuestas para este checkpoint
            const answersResponse = await fetch(`/checkpoints/${checkpoint.id}/answers`, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            const answers = await answersResponse.json();
            
            const checkpointElement = document.createElement('div');
            checkpointElement.className = 'checkpoint-card p-4 rounded-lg bg-white shadow';
            
            let answersHtml = '<div class="mt-3"><strong>Respuestas:</strong><ul class="list-disc pl-5 mt-2">';
            answers.forEach(answer => {
                answersHtml += `
                    <li class="${answer.is_correct ? 'text-green-600 font-bold' : ''}">${answer.answer}</li>
                `;
            });
            answersHtml += '</ul></div>';

            checkpointElement.innerHTML = `
                <h3 class="font-bold">${checkpoint.place.name}</h3>
                <p class="text-sm text-gray-500">Gimcana: ${checkpoint.gimcana.name}</p>
                <p class="text-gray-600"><strong>Reto:</strong> ${checkpoint.challenge}</p>
                <p class="text-gray-600"><strong>Pista:</strong> ${checkpoint.clue}</p>
                <p class="text-sm text-gray-500">Orden: ${checkpoint.order}</p>
                ${answersHtml}
            `;
            checkpointsList.appendChild(checkpointElement);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Función para cargar todas las etiquetas
async function loadTags() {
    try {
        const response = await fetch('/tags');
        if (!response.ok) throw new Error('Error loading tags');
        window.allTags = await response.json();
        updateTagsUI();
        updateTagsList();
    } catch (error) {
        console.error('Error:', error);
        showError('Error al cargar las etiquetas');
    }
}

// Función para actualizar la UI de etiquetas en el filtro de lugares
function updateTagsUI() {
    const tagContainer = document.getElementById('tag-container');
    if (!tagContainer) return;

    tagContainer.innerHTML = '';
    window.allTags.forEach(tag => {
        const tagElement = document.createElement('div');
        tagElement.className = `tag ${window.selectedTags.includes(tag.id) ? 'selected' : ''}`;
        tagElement.textContent = tag.name;
        tagElement.onclick = () => toggleFilterTag(tag.id);
        tagContainer.appendChild(tagElement);
    });

    // Actualizar también las etiquetas disponibles para lugares
    updatePlaceTagsUI();
}

// Función para actualizar la UI de etiquetas en el formulario de lugares
function updatePlaceTagsUI() {
    const placeTagsContainer = document.getElementById('place-tags-container');
    if (!placeTagsContainer) return;

    placeTagsContainer.innerHTML = '';
    window.allTags.forEach(tag => {
        const tagElement = document.createElement('div');
        tagElement.className = `tag ${window.placeSelectedTags.includes(tag.id) ? 'selected' : ''}`;
        tagElement.textContent = tag.name;
        tagElement.onclick = () => togglePlaceTag(tag.id);
        placeTagsContainer.appendChild(tagElement);
    });
}

// Función para actualizar la lista de etiquetas en la pestaña de etiquetas
function updateTagsList() {
    const tagsList = document.getElementById('tags-list');
    if (!tagsList) return;

    tagsList.innerHTML = '';
    window.allTags.forEach(tag => {
        const tagElement = document.createElement('div');
        tagElement.className = 'p-4 bg-gray-50 rounded-lg';
        tagElement.innerHTML = `
            <div class="flex justify-between items-center">
                <div>
                    <h4 class="font-semibold">${tag.name}</h4>
                    <p class="text-sm text-gray-600">Lugares: ${tag.places_count || 0}</p>
                </div>
                <button onclick="deleteTag(${tag.id})" class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        tagsList.appendChild(tagElement);
    });
}

// Función para alternar etiquetas en el filtro
function toggleFilterTag(tagId) {
    const index = window.selectedTags.indexOf(tagId);
    if (index === -1) {
        window.selectedTags = [tagId]; // Solo permitimos una etiqueta para filtrar
    } else {
        window.selectedTags = [];
    }
    updateTagsUI();
    loadPlaces(); // Recargar lugares con el filtro de etiquetas
}

// Función para alternar etiquetas en el lugar
function togglePlaceTag(tagId) {
    const index = window.placeSelectedTags.indexOf(tagId);
    if (index === -1) {
        window.placeSelectedTags.push(tagId);
    } else {
        window.placeSelectedTags.splice(index, 1);
    }
    updatePlaceTagsUI();
}

// Función para crear nueva etiqueta
async function createNewTag() {
    const input = document.getElementById('new-tag-input');
    const name = input.value.trim();
    
    if (name) {
        try {
            const response = await fetch('/tags', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ name })
            });

            if (!response.ok) throw new Error('Error creating tag');
            const newTag = await response.json();
            window.allTags.push(newTag);
            updateTagsUI();
            updateTagsList();
            input.value = '';
            showSuccess('Etiqueta creada exitosamente');
        } catch (error) {
            console.error('Error:', error);
            showError('Error al crear la etiqueta');
        }
    } else {
        showError('El nombre de la etiqueta no puede estar vacío');
    }
}

// Función para eliminar una etiqueta
async function deleteTag(tagId) {
    if (!confirm('¿Estás seguro de que quieres eliminar esta etiqueta?')) return;

    try {
        const response = await fetch(`/tags/${tagId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!response.ok) throw new Error('Error deleting tag');
        
        window.allTags = window.allTags.filter(tag => tag.id !== tagId);
        window.selectedTags = window.selectedTags.filter(id => id !== tagId);
        window.placeSelectedTags = window.placeSelectedTags.filter(id => id !== tagId);
        
        updateTagsUI();
        updateTagsList();
        showSuccess('Etiqueta eliminada exitosamente');
    } catch (error) {
        console.error('Error:', error);
        showError('Error al eliminar la etiqueta');
    }
}

// Función para eliminar un lugar
async function deletePlace(placeId) {
    if (!confirm('¿Estás seguro de que quieres eliminar este lugar?')) return;

    try {
        const response = await fetch(`/places/${placeId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!response.ok) throw new Error('Error deleting place');
        
        loadPlaces(window.map);
        showSuccess('Lugar eliminado exitosamente');
    } catch (error) {
        console.error('Error:', error);
        showError('Error al eliminar el lugar');
    }
}

// Función para eliminar una gimcana
async function deleteGimcana(gimcanaId) {
    if (!confirm('¿Estás seguro de que quieres eliminar esta gimcana?')) return;

    try {
        const response = await fetch(`/gimcanas/${gimcanaId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!response.ok) throw new Error('Error deleting gimcana');
        
        loadGimcanas();
        showSuccess('Gimcana eliminada exitosamente');
    } catch (error) {
        console.error('Error:', error);
        showError('Error al eliminar la gimcana');
    }
}

// Funciones de notificación
function showSuccess(message) {
    // Por ahora usamos alert, pero podrías usar una librería de notificaciones más elegante
    alert('✅ ' + message);
}

function showError(message) {
    // Por ahora usamos alert, pero podrías usar una librería de notificaciones más elegante
    alert('❌ ' + message);
}
