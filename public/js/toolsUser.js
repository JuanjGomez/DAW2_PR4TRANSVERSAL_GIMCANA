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
                const maxPlayers = gimcana.max_groups * gimcana.max_users_per_group
                const currentPlayers = gimcana.current_players
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
                            ${group.members && group.members.length >= gimcana.max_users_per_group ? '<p>Grupo lleno</p>' : '<button class="join-group">Unirse</button>'}
                        </div>
                    `).join('')}
                </div>
            `
            modal.classList.remove('hidden')

            // Add event listener to close button
            document.getElementById('closeDetailsModal').addEventListener('click', () => {
                modal.classList.add('hidden')
            })

            // Add event listeners to join buttons
            document.querySelectorAll('.join-group').forEach(button => {
                button.addEventListener('click', () => {
                    Swal.fire({
                        title: 'Unirse al grupo',
                        text: '¿Estás seguro de querer unirte a este grupo?',
                        icon: 'warning',
                    })
                })
            })
        })
        .catch(error => console.error('Error al cargar los detalles de la gimcana:', error))
}
