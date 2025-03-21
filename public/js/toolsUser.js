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
                const listItem = document.createElement('li')
                listItem.textContent = gimcana.name
                listItem.addEventListener('click', () => {
                    joinGimcana(gimcana.id)
                })
                list.appendChild(listItem)
            })
        })
        .catch(error => console.error('Error al cargar las gimcanas:', error))
}

function joinGimcana(gimcanaId) {
    fetch(`/gimcana/join`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ id: gimcanaId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Te has unido a la gimcana correctamente!')
            document.getElementById('gimcanaModal').classList.add('hidden')
        } else {
            alert('Error al unirse a la gimcana.')
        }
    })
    .catch(error => console.error('Error al unirse a la gimcana:', error))
}