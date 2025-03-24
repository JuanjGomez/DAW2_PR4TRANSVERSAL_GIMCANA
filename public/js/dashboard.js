document.addEventListener('DOMContentLoaded', function() {
    // Inicializar el mapa
    const map = L.map('map').setView([41.390205, 2.154007], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Variables globales
    let markers = [];
    let activeTab = 'places';
    let answerCount = 1;

    // Configurar los formularios
    setupPlaceForm(map, markers);
    setupGimcanaForm();
    setupCheckpointForm();

    // Cargar datos iniciales
    loadPlaces(map, markers);
    loadGimcanas();
    loadCheckpoints();

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

function loadPlaces(map, markers) {
    // Limpiar marcadores existentes
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];
    
    fetch('/places', {
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
        const placesList = document.getElementById('placesList');
        placesList.innerHTML = '';
        
        // Actualizar el select de lugares en el formulario de checkpoints
        const placeSelect = document.getElementById('cp-place');
        placeSelect.innerHTML = '<option value="">Selecciona un lugar</option>';
        
        places.forEach(place => {
            // Añadir al mapa
            const marker = L.marker([place.latitude, place.longitude])
                .addTo(map)
                .bindPopup(place.name);
            markers.push(marker);
            
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
            
            // Añadir al select
            const option = document.createElement('option');
            option.value = place.id;
            option.textContent = place.name;
            placeSelect.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cargar los lugares');
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
        gimcanasList.innerHTML = '';
        
        // Actualizar el select de gimcanas en el formulario de checkpoints
        updateGimcanasSelect(gimcanas);
        
        gimcanas.forEach(gimcana => {
            const gimcanaElement = document.createElement('div');
            gimcanaElement.className = 'p-4 border rounded-lg hover:bg-gray-50';
            gimcanaElement.innerHTML = `
                <h3 class="font-bold">${gimcana.name}</h3>
                <p class="text-gray-600">${gimcana.description}</p>
            `;
            gimcanasList.appendChild(gimcanaElement);
        });
    })
    .catch(error => {
        console.error('Error:', error);
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
