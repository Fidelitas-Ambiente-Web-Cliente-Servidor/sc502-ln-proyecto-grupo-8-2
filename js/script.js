document.addEventListener("DOMContentLoaded", function(){


    console.log("WorkMatch AI cargado correctamente.");






    const enlaces = document.querySelectorAll(".sidebar a");


    enlaces.forEach(function(link){


        if(link.href === window.location.href){


            link.style.backgroundColor="#0d6efd";
            link.style.fontWeight="bold";


        }


    });



    const botonesEliminar =
    document.querySelectorAll(".btn-danger");



    botonesEliminar.forEach(function(boton){



        boton.addEventListener("click",function(){



            let confirmar =
            confirm("¿Desea eliminar este registro?");



            if(confirmar){


                alert(
                    "Registro eliminado correctamente."
                );


            }



        });



    });








    const botonesGuardar =
    document.querySelectorAll(".btn-success");



    botonesGuardar.forEach(function(boton){



        boton.addEventListener("click",function(){



            alert(
                "Información guardada correctamente."
            );



        });



    });





    const buscador =
    document.getElementById("buscarVacante");



    const tabla =
    document.getElementById("tablaVacantes");



    if(buscador && tabla){



        buscador.addEventListener("keyup",function(){



            let texto =
            buscador.value.toLowerCase();



            let filas =
            tabla.querySelectorAll("tr");



            filas.forEach(function(fila){



                let contenido =
                fila.textContent.toLowerCase();



                if(contenido.includes(texto)){


                    fila.style.display="";


                }else{


                    fila.style.display="none";


                }



            });



        });



    }




    const formularios =
    document.querySelectorAll("form");



    formularios.forEach(function(form){



        form.addEventListener("submit",function(e){



            e.preventDefault();



            alert(
                "Datos guardados correctamente."
            );



        });



    });





});