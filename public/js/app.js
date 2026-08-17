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

document.addEventListener('DOMContentLoaded', function () {
    const modalidad = document.getElementById('filterModalidad');
    const contrato = document.getElementById('filterContrato');
    const busqueda = document.getElementById('vacancySearch');
    function filtrarVacantes(){
        document.querySelectorAll('.vacancy-item').forEach(function(item){
            const texto=(item.querySelector('.vacancy-card')?.dataset.search||'');
            const okTexto=!busqueda || texto.includes(busqueda.value.toLowerCase().trim());
            const okModalidad=!modalidad || !modalidad.value || item.dataset.modalidad===modalidad.value;
            const okContrato=!contrato || !contrato.value || item.dataset.contrato===contrato.value;
            item.classList.toggle('d-none',!(okTexto&&okModalidad&&okContrato));
        });
    }
    [busqueda,modalidad,contrato].forEach(function(el){ if(el) el.addEventListener(el.tagName==='INPUT'?'input':'change',filtrarVacantes); });

    document.querySelectorAll('.apply-btn').forEach(function(btn){ btn.addEventListener('click',function(){
        const id=document.getElementById('applyVacancyId'); const title=document.getElementById('applyTitle');
        if(id) id.value=btn.dataset.id; if(title) title.textContent=btn.dataset.puesto;
    }); });

    document.querySelectorAll('.edit-vacancy').forEach(function(btn){ btn.addEventListener('click',function(){
        try{ const d=JSON.parse(btn.dataset.vacante); const modal=document.getElementById('modalEditar'); if(!modal) return;
            document.getElementById('edit_id').value=d.id;
            ['puesto','area','ubicacion','modalidad','tipo_contrato','salario','descripcion','requisitos'].forEach(function(k){ const el=modal.querySelector('[name="'+k+'"]'); if(el) el.value=d[k]||''; });
        }catch(e){}
    }); });

    const candidateSearch=document.getElementById('candidateSearch');
    if(candidateSearch) candidateSearch.addEventListener('input',function(){ const q=candidateSearch.value.toLowerCase().trim(); document.querySelectorAll('.candidate-item').forEach(function(item){ item.classList.toggle('d-none',!(item.dataset.search||'').includes(q)); }); });
});
