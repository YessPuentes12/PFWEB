let doctoresData = [];
let especialidadesData = [];

const specialtySelect = document.getElementById("specialty");
const doctorSelect = document.getElementById("doctor");
const timeSelect = document.getElementById("time");

Promise.all([
    fetch("../php/obtener_especialidades.php").then(res => res.json()),
    fetch("../php/obtener_doctores.php").then(res => res.json())
]).then(([especialidades, doctores]) => {
    especialidadesData = especialidades;
    doctoresData = doctores;

    specialtySelect.innerHTML = '<option value="">Seleccione una especialidad</option>';
    especialidades.forEach(esp => {
        const option = document.createElement("option");
        option.value = esp.id;
        option.textContent = esp.nombre;
        specialtySelect.appendChild(option);
    });

    specialtySelect.addEventListener("change", function() {
        const selectedSpecialtyId = specialtySelect.value;
        doctorSelect.innerHTML = '<option value="">Seleccione un doctor</option>';
        const filtrados = doctoresData.filter(doc => doc.id_especialidad == selectedSpecialtyId);
        filtrados.forEach(doc => {
            const option = document.createElement("option");
            option.value = doc.id;
            option.textContent = doc.nombre;
            doctorSelect.appendChild(option);
        });
        timeSelect.innerHTML = '<option value="">Seleccione un horario</option>';
    });

    doctorSelect.addEventListener("change", function() {
        const selectedId = this.value;
        const doctor = doctoresData.find(d => d.id == selectedId);
        timeSelect.innerHTML = '<option value="">Seleccione un horario</option>';
        if (doctor && doctor.hora_llegada && doctor.hora_salida) {
            let start = doctor.hora_llegada;
            let end = doctor.hora_salida;
            let [h, m] = start.split(':').map(Number);
            let [eh, em] = end.split(':').map(Number);
            while (h < eh || (h === eh && m < em)) {
                let hourStr = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}`;
                let option = document.createElement("option");
                option.value = hourStr;
                option.textContent = hourStr;
                timeSelect.appendChild(option);
                m += 30;
                if (m >= 60) { h++; m = 0; }
            }
        }
    });

const citaEditar = sessionStorage.getItem('citaEditar');
if (citaEditar) {
    const cita = JSON.parse(citaEditar);
    specialtySelect.value = String(cita.id_especialidad || '');
    specialtySelect.dispatchEvent(new Event('change'));

    const esperarDoctor = setInterval(() => {
        const opcionesDoctor = [...doctorSelect.options].filter(opt => opt.value !== "");
        if (opcionesDoctor.length > 0) {
            doctorSelect.value = String(cita.id_doctor || '');
            doctorSelect.dispatchEvent(new Event('change'));
            clearInterval(esperarDoctor);

            const esperarHora = setInterval(() => {
            const opcionesHora = [...timeSelect.options].filter(opt => opt.value !== "");
                if (opcionesHora.length > 0) {
                    let horaCita = String(cita.hora || '').slice(0, 5);
                    timeSelect.value = horaCita;
                    clearInterval(esperarHora);
                }
            }, 100);
        }
    }, 100);

    document.getElementById("date").value = cita.fecha || '';
    document.getElementById("reason").value = cita.motivo || '';
    document.querySelector('.action-button').textContent = "Modificar Cita";
}

});
    
    document.getElementById("citaForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const citaEditar = sessionStorage.getItem('citaEditar');
    const data = {
        usuario_id: sessionStorage.getItem('userId'),
        especialidad: document.getElementById("specialty").value,
        doctor_id: document.getElementById("doctor").value,
        fecha: document.getElementById("date").value,
        hora: document.getElementById("time").value,
        motivo: document.getElementById("reason").value
    };

    if (
        !data.usuario_id ||
        !data.especialidad ||
        !data.doctor_id ||
        !data.fecha ||
        !data.hora ||
        !data.motivo
    ) {
        alert("Por favor, completa todos los campos.");
        return;
    }

    if (citaEditar) {
        const cita = JSON.parse(citaEditar);
        data.cita_id = cita.id || cita.cita_id;
        fetch("../php/modificar_cita.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(response => {
            alert(response.message);
            sessionStorage.removeItem('citaEditar');
            window.location.href = "dashboard.html";
        });
    } else {
        fetch("../php/agendar_cita.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(response => alert(response.message));
    }
});

    const isLoggedIn = sessionStorage.getItem('isLoggedIn') === 'true';

    document.querySelectorAll('.menu .privado').forEach(link => {
        if (!isLoggedIn) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Debes iniciar sesión para acceder a esta sección.');
            });
            link.classList.add('disabled');
            link.style.pointerEvents = 'auto';
            link.style.opacity = '0.5';
        }
    });
const logoutBtn = document.getElementById('logoutBtn');
if (logoutBtn) {
    if (isLoggedIn) {
        logoutBtn.style.display = 'inline-block';
    } else {
        logoutBtn.style.display = 'none';
    }

    logoutBtn.addEventListener('click', function() {
        sessionStorage.removeItem('isLoggedIn');
        window.location.href = 'login.html';
    });
    
}