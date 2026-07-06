

// Mostrar mensaje al cargar la página
document.addEventListener("DOMContentLoaded", function () {

    console.log("WorkMatch AI cargado correctamente.");

});

// Confirmación para eliminar registros
const botonesEliminar = document.querySelectorAll(".btn-danger");

botonesEliminar.forEach(function (boton) {

    boton.addEventListener("click", function () {

        const eliminar = confirm("¿Desea eliminar este registro?");

        if (eliminar) {

            alert("Registro eliminado correctamente.");

        }

    });

});

// Botones Guardar
const botonesGuardar = document.querySelectorAll(".btn-success");

botonesGuardar.forEach(function (boton) {

    boton.addEventListener("click", function () {

        alert("Información guardada correctamente.");

    });

});

// Resaltar opción activa del menú
const enlaces = document.querySelectorAll(".sidebar a");

enlaces.forEach(function (link) {

    if (link.href === window.location.href) {

        link.style.backgroundColor = "#0d6efd";
        link.style.fontWeight = "bold";

    }

});