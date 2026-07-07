// Confirmación de carga

document.addEventListener("DOMContentLoaded", function () {

    console.log("WorkMatch AI cargado correctamente");

});




// Mensaje al guardar formularios

const formularios = document.querySelectorAll("form");


formularios.forEach(function(formulario){


    formulario.addEventListener("submit", function(event){


        event.preventDefault();


        alert("Información guardada correctamente");


    });


});





// Confirmación para eliminar vacantes

const botonesEliminar = document.querySelectorAll(".btn-danger");


botonesEliminar.forEach(function(boton){


    boton.addEventListener("click", function(){


        let confirmar = confirm(
            "¿Está seguro que desea eliminar esta vacante?"
        );


        if(confirmar){

            alert("Vacante eliminada correctamente");

        }


    });


});





// Efecto simple en botones

const botones = document.querySelectorAll(".btn");


botones.forEach(function(boton){


    boton.addEventListener("mouseenter", function(){

        boton.style.opacity = "0.85";

    });



    boton.addEventListener("mouseleave", function(){

        boton.style.opacity = "1";

    });



});