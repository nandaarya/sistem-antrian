const admin = JSON.parse(localStorage.getItem('admin'));
const adminDiv = document.getElementById('adminInfo');

if (admin) {
    adminDiv.innerHTML = `
        <p>Halo, ${admin.name}! Selamat datang di Admin Panel.</p>
    `;
} else {
    alert('Anda belum login! Silakan login terlebih dahulu.');
    window.location.href = 'index.html';
}

document.getElementById('logoutButton').addEventListener('click', function() {
    localStorage.removeItem('admin');
    window.location.href = 'index.html';
});
