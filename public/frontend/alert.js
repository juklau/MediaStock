document.getElementById('loginForm').addEventListener('submit', async (e) => {
e.preventDefault();
const formData = new FormData(e.target);

const res = await fetch('../login.php', { method: 'POST', body: formData });
const data = await res.json();

const username = document.getElementById('username').value.trim();
const password = document.getElementById('password').value.trim();

if (username === '' || password === '') {
    e.preventDefault();
    Swal.fire({
        icon: 'error',
        title: data.title,
        text: data.message,
        confirmButtonColor: '#FF9994'
    });
}else if (data.success) {
    Swal.fire({
        icon: 'success',
        title: data.title,
        text: data.message,
        confirmButtonColor: '#4CAF50'
    }).then(() => {
        window.location.href = '../frontend/index.html';
    });
} else {
    Swal.fire({
        icon: 'error',
        title: data.title,
        text: data.message,
        confirmButtonColor: '#FF9994'
    });
}

});
