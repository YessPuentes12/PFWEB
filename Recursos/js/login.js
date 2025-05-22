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

signUpForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const name = signUpForm.querySelector('input[type="text"]').value;
    const email = signUpForm.querySelector('input[type="email"]').value;
    const password = signUpForm.querySelector('input[type="password"]').value;
    
    try {
        // TODO: Backend endpoint for registration
        // POST request to: /api/auth/register
        // Request body: { name, email, password }
        // Expected response: { success: true, user: {...} }
        
        const response = await fetch('/api/auth/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ name, email, password })
        });

        if (response.ok) {
            // TODO: Handle successful registration
            // Redirect to dashboard or show success message
        } else {
            // TODO: Handle registration errors
            // Show error message to user
        }
    } catch (error) {
        console.error('Registration error:', error);
    }
});

signInForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = signInForm.querySelector('input[type="email"]').value;
    const password = signInForm.querySelector('input[type="password"]').value;
    
    try {
        // TODO: Backend endpoint for login
        // POST request to: /api/auth/login
        // Request body: { email, password }
        // Expected response: { success: true, token: "jwt_token", user: {...} }
        
        const response = await fetch('/api/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });

        if (response.ok) {
            const data = await response.json();
            // TODO: Store JWT token
            // localStorage.setItem('token', data.token);
            // Redirect to dashboard
        } else {
            // TODO: Handle login errors
            // Show error message to user
        }
    } catch (error) {
        console.error('Login error:', error);
    }
});