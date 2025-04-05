// assets/js/login.js

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("btnLogin").addEventListener("click", login);
    document.getElementById("btnRegister").addEventListener("click", register);
    document.getElementById("btnCancelRegister").addEventListener("click", showLogin);
    document.getElementById("btnShowRegister").addEventListener("click", showRegister);
});

function login() {
    let email = document.getElementById("login-email").value.trim();
    let password = document.getElementById("login-password").value.trim();
    let rol = document.getElementById("login-rol").value;

    if (!email || !password) {
        showError("login-error", "Por favor, completa todos los campos.");
        return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showError("login-error", "Por favor, ingresa un correo electrónico válido.");
        return;
    }

    fetch("../controller/AuthController.php", {
        method: "POST",
        body: new URLSearchParams({ action: "login", email, password, rol })
    })
    .then(response => {
        console.log("Respuesta cruda del servidor (login):", response);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(text => {
        console.log("Texto recibido (login):", text);
        if (!text) {
            throw new Error("Respuesta vacía del servidor");
        }
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            throw new Error("Error al analizar JSON: " + e.message + " - Texto recibido: " + text);
        }
        return data;
    })
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            showError("login-error", data.message);
        }
    })
    .catch(error => {
        console.error("Error en login:", error);
        showError("login-error", "Error al iniciar sesión: " + error.message);
    });
}

function register() {
    let nombre = document.getElementById("register-name").value.trim();
    let email = document.getElementById("register-email").value.trim();
    let telefono = document.getElementById("register-phone").value.trim();
    let password = document.getElementById("register-password").value.trim();

    if (!nombre || !email || !telefono || !password) {
        showError("register-error", "Por favor, completa todos los campos.");
        return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showError("register-error", "Por favor, ingresa un correo electrónico válido.");
        return;
    }

    if (password.length < 6) {
        showError("register-error", "La contraseña debe tener al menos 6 caracteres.");
        return;
    }

    let formData = new URLSearchParams({
        action: "register",
        nombre: nombre,
        email: email,
        telefono: telefono,
        password: password
    });

    fetch("../controller/AuthController.php", {
        method: "POST",
        body: formData
    })
    .then(response => {
        console.log("Respuesta cruda del servidor (register):", response);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(text => {
        console.log("Texto recibido (register):", text);
        if (!text) {
            throw new Error("Respuesta vacía del servidor");
        }
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            throw new Error("Error al analizar JSON: " + e.message + " - Texto recibido: " + text);
        }
        return data;
    })
    .then(data => {
        if (data.success) {
            alert(data.message);
            showLogin();
            document.getElementById("register-name").value = "";
            document.getElementById("register-email").value = "";
            document.getElementById("register-phone").value = "";
            document.getElementById("register-password").value = "";
        } else {
            showError("register-error", data.message);
        }
    })
    .catch(error => {
        console.error("Error en register:", error);
        showError("register-error", "Error al registrar: " + error.message);
    });
}

function showRegister() {
    document.querySelector(".login-form").style.display = "none";
    document.querySelector(".register-form").style.display = "block";
    document.getElementById("login-error").style.display = "none";
}

function showLogin() {
    document.querySelector(".register-form").style.display = "none";
    document.querySelector(".login-form").style.display = "block";
    document.getElementById("register-error").style.display = "none";
}

function showError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    errorElement.textContent = message;
    errorElement.style.display = "block";
}