const form = document.getElementById('loginForm');
const resultDiv = document.getElementById('result');

form.addEventListener('submit', async function(event) {
    event.preventDefault();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    try {
        const response = await fetch('http://127.0.0.1:8000/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();
        if (response.ok) {
            localStorage.setItem('admin', JSON.stringify(data.admin));
            
            resultDiv.innerHTML = 'Login berhasil! Mengarahkan ke Admin Panel...';
            setTimeout(() => {
                window.location.href = 'admin.html';
            }, 1500);
        } else {
            resultDiv.innerHTML = 'Login gagal: ' + data.message;
        }
    } catch (error) {
        resultDiv.innerHTML = 'Terjadi error: ' + error.message;
    }
});
