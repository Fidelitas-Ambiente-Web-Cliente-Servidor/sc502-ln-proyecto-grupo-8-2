
document.addEventListener("DOMContentLoaded", function () {
    console.log("WorkMatch AI cargado correctamente.");
    resaltarMenuActivo();
});


document.addEventListener("click", function (event) {
    

    if (event.target.closest(".btn-danger")) {
        const confirmar = confirm("¿Desea eliminar este registro?");
        if (confirmar) {

            alert("Registro eliminado correctamente.");
        }
    }

    if (event.target.closest(".btn-success")) {
        
        const contenedorForm = event.target.closest("form") || event.target.closest(".modal-content") || event.target.closest(".card-body");
        
        if (contenedorForm) {

            const campos = contenedorForm.querySelectorAll("input, textarea, select");
            let formularioValido = true;

            campos.forEach(campo => {

                if (campo.hasAttribute('required') && campo.value.trim() === "") {
                    formularioValido = false;
                    campo.classList.add("is-invalid"); 
                } else {
                    campo.classList.remove("is-invalid"); 
                }
            });

        
        }

    }
});


function resaltarMenuActivo() {
    const enlaces = document.querySelectorAll(".sidebar a");
    const urlActual = window.location.href;

    enlaces.forEach(function (link) {
        if (link.href === urlActual) {
            link.style.backgroundColor = "#0d6efd"; 
            link.style.fontWeight = "bold";        
            link.style.paddingLeft = "35px";       
        }
    });
}