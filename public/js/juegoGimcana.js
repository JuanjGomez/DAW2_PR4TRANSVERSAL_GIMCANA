const map = L.map('map').setView([0, 0], 13) // Ajusta la vista inicial

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map)

        // Marcador interactivo del usuario
        let userMarker

        // Función para mostrar el reto
        function showChallenge(challenge) {
            const modal = document.getElementById('challengeModal')
            modal.innerHTML = `<p>${challenge}</p>`
            modal.classList.remove('hidden')
        }

        let checkpoints = [] // Declarar fuera de la función

        // Obtener checkpoints desde la base de datos
        async function loadCheckpoints() {
            try {
                const response = await fetch('/api/checkpoints')
                checkpoints = await response.json() // Asignar a la variable global

                checkpoints.forEach(checkpoint => {
                    if (checkpoint.lat !== undefined && checkpoint.lng !== undefined) {
                        L.marker([checkpoint.lat, checkpoint.lng])
                            .bindPopup(`<b>${checkpoint.clue}</b>`)
                            .addTo(map)
                    } else {
                        console.error('Checkpoint con coordenadas inválidas:', checkpoint)
                    }
                })
            } catch (error) {
                console.error('Error al cargar los checkpoints:', error)
            }
        }

        // Simulación de geolocalización
        navigator.geolocation.watchPosition((position) => {
            const { latitude, longitude } = position.coords
            map.setView([latitude, longitude], 15)

            // Actualizar o crear el marcador interactivo del usuario
            if (userMarker) {
                userMarker.setLatLng([latitude, longitude])
            } else {
                userMarker = L.circleMarker([latitude, longitude], {
                    radius: 8,
                    fillColor: '#007bff',
                    color: '#fff',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.8
                }).addTo(map)
            }

            checkpoints.forEach((checkpoint, index) => {
                if (checkProximity(latitude, longitude, checkpoint.lat, checkpoint.lng)) {
                    showChallenge(checkpoints[index + 1]?.challenge || '¡Has completado todos los retos!')
                }
            })
        })

        // Cargar checkpoints al iniciar
        loadCheckpoints()

        function checkProximity(userLat, userLng, checkpointLat, checkpointLng) {
            const distance = map.distance([userLat, userLng], [checkpointLat, checkpointLng]);
            return distance <= 50 // 50 metros
        }