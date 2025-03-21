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
            const list = document.getElementById('gimcanaList');
            list.innerHTML = '';
            gimcanas.forEach(gimcana => {
                const maxPlayers = gimcana.max_groups * gimcana.max_users_per_group
                const currentPlayers = gimcana.current_groups * gimcana.max_users_per_group
                const card = document.createElement('div')
                card.className = 'gimcana-card'
                card.innerHTML = `
                    <span>${gimcana.name}</span>
                    <span>${currentPlayers} / ${maxPlayers} <i class="fas fa-user icon"></i></span>
                `
                card.addEventListener('click', () => openGimcanaDetails(gimcana.id))
                list.appendChild(card)
            })
        })
        .catch(error => console.error('Error al cargar las gimcanas:', error))
}

function openGimcanaDetails(gimcanaId) {
    fetch(`/api/gimcanas/${gimcanaId}`)
        .then(response => response.json())
        .then(gimcana => {
            const modal = document.getElementById('gimcanaDetailsModal')
            const modalContent = document.getElementById('gimcanaDetailsContent')
            modalContent.innerHTML = `
                <h2 class="text-2xl font-bold mb-4">${gimcana.name}</h2>
                <p>Límite de usuarios por grupo: ${gimcana.max_users_per_group}</p>
                <h3 class="text-xl font-bold mt-4">Grupos:</h3>
                <ul>
                    ${gimcana.groups.map(group => `<li>${group.name}</li>`).join('')}
                </ul>
            `
            modal.classList.remove('hidden')
        })
        .catch(error => console.error('Error al cargar los detalles de la gimcana:', error))
}

document.getElementById('closeDetailsModal').addEventListener('click', () => {
    document.getElementById('gimcanaDetailsModal').classList.add('hidden')
})
