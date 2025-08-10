async function loadServiceQueues(serviceId) {
    const targetDiv = document.getElementById(`service${serviceId}Queues`);
    const isAdmin = localStorage.getItem('admin') !== null;
    const startButton = document.getElementById(`startButton${serviceId}`);
    const currentQueueDiv = document.getElementById(`current${serviceId}`);
    
    try {
        const response = await fetch(`http://127.0.0.1:8000/queues?service_id=${serviceId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });

        const queues = await response.json();

        if (response.ok) {
            let listHTML = '<h4>Daftar Antrian:</h4><ul>';
            queues.forEach(queue => {
                listHTML += `<li>${queue.queue_number} - ${queue.status}</li>`;
            });
            listHTML += '</ul>';

            targetDiv.innerHTML = listHTML;

            if (isAdmin && startButton) {
                if (queues.some(q => q.status === 'called')) {
                    startButton.style.display = 'none';
                } else {
                    startButton.style.display = 'block';
                }
            } else {
                const currentQueue = queues
                    .filter(q => q.service_id === serviceId)
                    .filter(q => q.status === 'called')
                    .sort((a, b) => new Date(b.called_at) - new Date(a.called_at))[0];

                if (currentQueue) {
                    currentQueueDiv.textContent = currentQueue.queue_number;
                } else {
                    currentQueueDiv.textContent = '-';
                }
            }

        } else {
            targetDiv.innerHTML = '<p>Gagal mengambil data antrian.</p>';
        }
    } catch (error) {
        targetDiv.innerHTML = '<p>Terjadi error: ' + error.message + '</p>';
    }
}

loadServiceQueues(1);
loadServiceQueues(2);
loadServiceQueues(3);

document.getElementById('takeQueueForm').addEventListener('submit', async function(event) {
    event.preventDefault();
    const serviceId = document.getElementById('serviceId').value;
    const resultDiv = document.getElementById('queueResult');

    try {
        const response = await fetch('http://127.0.0.1:8000/queues/take', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ service_id: serviceId })
        });

        const result = await response.json();
        if (response.ok) {
            resultDiv.innerHTML = `Berhasil mengambil antrian: ${result.queue.queue_number}`;
            loadServiceQueues(serviceId);
        } else {
            resultDiv.innerHTML = 'Gagal mengambil antrian: ' + result.message;
        }
    } catch (error) {
        resultDiv.innerHTML = 'Terjadi error: ' + error.message;
    }
});

async function startQueue(serviceId) {
    const adminId = JSON.parse(localStorage.getItem('admin')).id;
    const currentQueueDiv = document.getElementById(`current${serviceId}`);
    try {
        const response = await fetch('http://127.0.0.1:8000/start-queue', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ service_id: serviceId, admin_id: adminId })
        });

        const result = await response.json();
        if (response.ok) {
            currentQueueDiv.textContent = result.queue.queue_number;
            loadServiceQueues(serviceId);
        } else {
            alert(result.message);
        }
    } catch (error) {
        alert('Terjadi error: ' + error.message);
    }
}

async function nextQueue(serviceId) {
    const adminId = JSON.parse(localStorage.getItem('admin')).id;
    try {
        const response = await fetch('http://127.0.0.1:8000/queues/next', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                service_id: serviceId,
                admin_id: adminId
            })
        });

        const result = await response.json();
        if (response.ok) {
            document.getElementById(`current${serviceId}`).textContent = result.queue_number;
            loadServiceQueues(serviceId);
        } else {
            alert(result.message);
        }
    } catch (error) {
        alert('Terjadi error: ' + error.message);
    }
}

async function prevQueue(serviceId) {
    const adminId = JSON.parse(localStorage.getItem('admin')).id;
    try {
        const response = await fetch('http://127.0.0.1:8000/queues/prev', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                service_id: serviceId,
                admin_id: adminId
            })
        });

        const result = await response.json();
        if (response.ok) {
            document.getElementById(`current${serviceId}`).textContent = result.queue_number;
            loadServiceQueues(serviceId);
        } else {
            alert(result.message);
        }
    } catch (error) {
        alert('Terjadi error: ' + error.message);
    }
}