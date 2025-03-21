document.addEventListener('DOMContentLoaded', function() {
    // Mostrar la pestaña de gimcanas por defecto
    showTab('gimcanas');
    
    // Configurar el formulario de gimcanas
    setupGimcanaForm();
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

function setupGimcanaForm() {
    const form = document.getElementById('gimcanaForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Obtener los datos del formulario
        const formData = new FormData(this);
        const data = {
            name: formData.get('name'),
            description: formData.get('description')
        };
        
        // Enviar la petición al servidor
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
            // Mostrar mensaje de éxito
            alert('Gimcana creada con éxito');
            
            // Limpiar el formulario
            form.reset();
            
            // Actualizar la lista de gimcanas
            updateGimcanasList();
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'Error al crear la gimcana');
        });
    });
}

function updateGimcanasList() {
    // Obtener la lista de gimcanas
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
