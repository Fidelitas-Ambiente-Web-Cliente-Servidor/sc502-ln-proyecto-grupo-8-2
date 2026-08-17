document.addEventListener('DOMContentLoaded', function () {
    const tipoCuenta = document.getElementById('tipoCuenta');
    const sitioWebBox = document.getElementById('sitioWebBox');
    const labelNombre = document.getElementById('labelNombre');
    const labelIdentificacion = document.getElementById('labelIdentificacion');

    if (tipoCuenta) {
        function actualizarRegistro() {
            const empresa = tipoCuenta.value === 'empresa';
            sitioWebBox.classList.toggle('d-none', !empresa);
            labelNombre.textContent = empresa ? 'Nombre de la Empresa' : 'Nombre Completo';
            labelIdentificacion.textContent = empresa ? 'Cédula Jurídica' : 'Cédula o Identificación';
        }

        tipoCuenta.addEventListener('change', actualizarRegistro);
        actualizarRegistro();
    }

    document.querySelectorAll('.password-toggle').forEach(function (boton) {
        boton.addEventListener('click', function () {
            const input = document.getElementById(boton.dataset.target);
            if (!input) return;

            const visible = input.type === 'text';
            input.type = visible ? 'password' : 'text';
            boton.innerHTML = visible ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
        });
    });

    const reloj = document.querySelector('#liveClock span');
    if (reloj) {
        function actualizarReloj() {
            const ahora = new Date();
            reloj.textContent = ahora.toLocaleTimeString('es-CR', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        actualizarReloj();
        setInterval(actualizarReloj, 30000);
    }

    document.querySelectorAll('.stat-number').forEach(function (numero) {
        const total = Number(numero.dataset.count || 0);
        let actual = 0;
        const pasos = Math.max(total, 1);
        const incremento = Math.max(1, Math.ceil(total / 20));

        const animacion = setInterval(function () {
            actual += incremento;
            if (actual >= total) {
                actual = total;
                clearInterval(animacion);
            }
            numero.textContent = actual;
        }, Math.max(25, 400 / pasos));
    });

    const buscador = document.getElementById('vacancySearch');
    const contador = document.getElementById('vacancyCount');
    if (buscador) {
        buscador.addEventListener('input', function () {
            const texto = buscador.value.toLowerCase().trim();
            let visibles = 0;

            document.querySelectorAll('.vacancy-item').forEach(function (item) {
                const card = item.querySelector('.vacancy-card');
                const contenido = card ? card.dataset.search : '';
                const mostrar = contenido.includes(texto);
                item.classList.toggle('d-none', !mostrar);
                if (mostrar) visibles++;
            });

            if (contador) contador.textContent = visibles;
        });
    }

    document.querySelectorAll('.confirm-action').forEach(function (boton) {
        boton.addEventListener('click', function (evento) {
            const mensaje = boton.dataset.confirm || '¿Deseas continuar?';
            if (!confirm(mensaje)) {
                evento.preventDefault();
            }
        });
    });

    const elementos = document.querySelectorAll('.reveal, .stat-card, .vacancy-card');
    if ('IntersectionObserver' in window) {
        const observador = new IntersectionObserver(function (entradas) {
            entradas.forEach(function (entrada) {
                if (entrada.isIntersecting) {
                    entrada.target.classList.add('visible');
                    observador.unobserve(entrada.target);
                }
            });
        }, { threshold: 0.08 });

        elementos.forEach(function (elemento) {
            elemento.classList.add('reveal');
            observador.observe(elemento);
        });
    } else {
        elementos.forEach(function (elemento) {
            elemento.classList.add('visible');
        });
    }

    document.querySelectorAll('.alert').forEach(function (alerta) {
        setTimeout(function () {
            alerta.classList.add('auto-hide');
            setTimeout(function () {
                alerta.remove();
            }, 400);
        }, 4500);
    });
});
