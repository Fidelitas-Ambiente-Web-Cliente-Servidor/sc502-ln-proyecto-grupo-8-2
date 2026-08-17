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
});
