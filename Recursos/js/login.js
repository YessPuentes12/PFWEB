const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');

registerBtn.addEventListener('click', () => {
    container.classList.add("active");
});

loginBtn.addEventListener('click', () => {
    container.classList.remove("active");
});

const signUpForm = document.querySelector('.sign-up form');
const signInForm = document.querySelector('.sign-in form');

// Registro
signUpForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const name = signUpForm.querySelector('input[type="text"]').value;
    const email = signUpForm.querySelector('input[type="email"]').value;
    const password = signUpForm.querySelector('input[type="password"]').value;
    
    try {
        const response = await fetch('../php/register.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ name, email, password })
        });

        const data = await response.json();
        if (data.success) {
            alert('Registro exitoso. Ahora puedes iniciar sesión.');
            container.classList.remove("active");
            signUpForm.reset();
        } else {
            alert(data.message || 'Error en el registro');
        }
    } catch (error) {
        console.error('Registration error:', error);
        alert('Error de conexión con el servidor');
    }
});

// Login
signInForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = signInForm.querySelector('input[type="email"]').value;
    const password = signInForm.querySelector('input[type="password"]').value;
    
    try {
        const response = await fetch('../php/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();
        sessionStorage.setItem('isLoggedIn', 'true');
        if (data.success) {
            sessionStorage.setItem('isLoggedIn', 'true');
            sessionStorage.setItem('userType', data.tipo); // "doctor" o "paciente"
            sessionStorage.setItem('userId', data.user_id); // Debes devolver el id en el login.php
            if (data.tipo === "doctor") {
                window.location.href = "dashboard.html";
            } else if (data.tipo === "paciente") {
                window.location.href = "dashboard.html";
            }
        } else {
            alert(data.message || "Usuario o contraseña incorrectos");
            signInForm.reset();
        }
    } catch (error) {
        console.error('Login error:', error);
        alert('Error de conexión con el servidor');
    }
});