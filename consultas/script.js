document.addEventListener("DOMContentLoaded", () => {


    // ==========================
    // MENU MOVIL
    // ==========================

    const menuBtn = document.getElementById("menuBtn");
    const sidebar = document.getElementById("sidebar");


    if(menuBtn && sidebar){

        menuBtn.addEventListener("click", () => {

            sidebar.classList.toggle("show");

        });

    }



    // ==========================
    // BUSCADOR DE TABLAS
    // ==========================

    function activarBusqueda(inputId, tablaId){

        const input = document.getElementById(inputId);
        const tabla = document.getElementById(tablaId);


        if(input && tabla){

            input.addEventListener("keyup",()=>{


                let filtro = input.value.toLowerCase();


                tabla.querySelectorAll("tr").forEach(fila=>{


                    fila.style.display =
                    fila.textContent.toLowerCase().includes(filtro)
                    ? ""
                    : "none";


                });


            });

        }

    }


    activarBusqueda("buscarVacante","tablaVacantes");
    activarBusqueda("buscarEliminar","tablaEliminar");




    // ==========================
    // ELIMINAR REGISTROS
    // ==========================


    document.querySelectorAll(".btnEliminar")
    .forEach(boton=>{


        boton.addEventListener("click",()=>{


            let fila = boton.closest("tr");


            if(confirm("¿Desea eliminar este registro?")){


                fila.remove();

                mostrarMensaje("Registro eliminado correctamente");


            }


        });


    });





    // ==========================
    // FORMULARIOS
    // ==========================


    document.querySelectorAll("form")
    .forEach(form=>{


        form.addEventListener("submit",(e)=>{


            e.preventDefault();


            mostrarMensaje("Información guardada correctamente");


            form.reset();


        });


    });



});




// ==========================
// NOTIFICACIÓN
// ==========================


function mostrarMensaje(texto){


    const alerta = document.getElementById("notification");


    if(alerta){


        alerta.textContent = texto;

        alerta.classList.add("mostrar");



        setTimeout(()=>{


            alerta.classList.remove("mostrar");


        },3000);



    }


}