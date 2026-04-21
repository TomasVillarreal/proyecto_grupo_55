document.addEventListener("DOMContentLoaded", function () {

  const showNavbar = (toggleId, navId, bodyId, headerId) => {
    const toggle = document.getElementById(toggleId);
    const nav = document.getElementById(navId);
    const bodypd = document.getElementById(bodyId);
    const headerpd = document.getElementById(headerId);

    if (toggle && nav && bodypd && headerpd) {
      toggle.addEventListener("click", () => {
        nav.classList.toggle("sidebarshow");
        toggle.classList.toggle("bx-x");
        bodypd.classList.toggle("body-pd");
        headerpd.classList.toggle("body-pd");
      });
    }
  };

  showNavbar("header-toggle", "nav-bar", "body-pd", "header");

  /* ACTIVE SEGÚN URL */
  const links = document.querySelectorAll(".nav_link");
  const currentUrl = window.location.href;

  links.forEach(link => {
    link.classList.remove("active");

    if (link.href === currentUrl) {
      link.classList.add("active");
    }
  });

});




document.addEventListener("DOMContentLoaded", function () {
  const select = document.getElementById("medSelectCreate");
  const input = document.getElementById("nameInputCreate");

  select.addEventListener("change", function () {
    const selectedOption = this.options[this.selectedIndex];

    if (this.value === "new") {
      input.disabled = false;
      input.required = true;
      input.value = "";
    } else {
      const nombre = selectedOption.getAttribute("data-nombre");
      input.disabled = true;
      input.required = false;
      input.value = nombre;
    }
  });
});


document.addEventListener("DOMContentLoaded", function () {

  // Agarro los componentes del form relacionados a medicamentos
  const medSelect = document.getElementById("medSelectUpdate");
  const nameInput = document.getElementById("nameInputUpdate");

  // Agarro los componentes del form relacionados a productos farmaceuticos
  const descriptionSelect = document.getElementById("descriptionSelectUpdate");
  const typeInput = document.getElementById("typeInputUpdate");
  const dosageInput = document.getElementById("dosageInputUpdate");
  const measurementInput = document.getElementById("measurementInputUpdate");
  const descripcionTextarea = document.getElementById("descripcionInputUpdate");

  // En un principio desabilito todo que no sea la seleccion de medicamentos
  nameInput.disabled = true;
  descriptionSelect.disabled = true;
  disableDescriptionFields();

  // Funcion para desabilitar todo lo relacionado a productos farmaceuticos
  function disableDescriptionFields() {
    typeInput.disabled = true;
    dosageInput.disabled = true;
    measurementInput.disabled = true;
    descripcionTextarea.disabled = true;

    typeInput.required = false;
    dosageInput.required = false;
    measurementInput.required = false;
  }

//Funcion para limpiar todo lo escrito que relacionado a producto farma (en caso de cambiar de producto farmaceutico)
  function clearDescriptionFields() {
    typeInput.value = "";
    dosageInput.value = "";
    measurementInput.value = "";
    descripcionTextarea.value = "";
  }

  // Funcion para arrancar de 0 la seleccion de producto farma., en caso de cambiar de medicamento
  function clearDescriptionSelect() {
    while (descriptionSelect.options.length > 1) {
      descriptionSelect.remove(1);
    }
    descriptionSelect.value = "-1";
  }

  // Cuando un medicamento es seleccionado (o cambia el contenido del select de medicamento)
  medSelect.addEventListener("change", async function () {
    // Agarramos la opcion elegida, su nombre e id
    const selectedOption = this.options[this.selectedIndex];
    const nombre = selectedOption.dataset.nombre || "";
    const idMedicamento = this.value;

    // Habilitamos el input del nombre del medicamento, lo hacemos obligatorio, y permitimos la seleccion de un prod. farma.
    nameInput.disabled = false;
    nameInput.required = true;
    nameInput.value = nombre;
    descriptionSelect.disabled = false;

    // Se reinicia de 0 todo lo hecho anteriormente
    disableDescriptionFields();
    clearDescriptionFields();
    clearDescriptionSelect();

    try {
      // intenta hacer una peticion get al backend
      const response = await fetch(`${BASE_URL}medicamentos/productos/${idMedicamento}`);

      // Convierte la respuesta en un vector de productos farmaceuticos
      const productos = await response.json();

      productos.forEach(prod => {
        // recorro todos los productos y voy creando opciones en base a estos
        const option = document.createElement("option");

        // tal que su valor es el id del prod. farma
        option.value = prod.id_producto;
        // su contenido es la dosis mas el tipo de medida (ej: 400 mg)
        option.textContent =
          prod.nombre_tipo_producto + " - " + prod.dosis_producto + " " + prod.nombre_medida;

        // y donde almaceno otros datos relacionados a esta opcion
        option.dataset.dosis = prod.dosis_producto;
        option.dataset.tipo = prod.id_tipo_producto;
        option.dataset.unidad = prod.id_medida_producto;
        option.dataset.descripcion = prod.descripcion_producto ?? "";
        
        // se agrega la opcion al select de las descripciones farmaceuticas
        descriptionSelect.appendChild(option);
      });

    } catch (error) {
      console.error("Error cargando productos:", error);
    }
  });

  
  // Cuando se selecciona un prod. farma.
  descriptionSelect.addEventListener("change", function () {
    // Agarro la opcion elegida
    const selectedOption = this.options[this.selectedIndex];

    // Si la opcion es la opcion por default, reinicio todo lo escrito anteriormente y lo deshabilito
    if (this.value === "-1") {
      disableDescriptionFields();
      clearDescriptionFields();
      return;
    }

    // Habilito todo lo que se pueda editar de prod farma, y lo hago todo obligatorio menos el comentario
    typeInput.disabled = false;
    dosageInput.disabled = false;
    measurementInput.disabled = false;
    descripcionTextarea.disabled = false;

    typeInput.required = true;
    dosageInput.required = true;
    measurementInput.required = true;

    // Relleno todos estos datos con los datos del prod. farma. elegido
    dosageInput.value = selectedOption.dataset.dosis || "";
    typeInput.value = selectedOption.dataset.tipo || "";
    measurementInput.value = selectedOption.dataset.unidad || "";
    descripcionTextarea.value = selectedOption.dataset.descripcion || "";
  });
});



//Para la vista "delete"
document.addEventListener("DOMContentLoaded", () => {
    // agarro el select del medicamento
    const select = document.getElementById("medSelectDelete");
    // agarro el div que contendra a las cartas
    const cards = document.getElementById("contenedorCards");
    // agarro al contenedor que permira el borrado de un medicamento entero
    const deleteMed = document.getElementById("contenedorDeleteMedicamento");

    // escucho el cambio en el select del medicamento
    select.addEventListener("change", async function () {

      // agarro el id del medicamento
        const idMedicamento = this.value;

        // pongo un valor nulo en ambos divs
        cards.innerHTML = "";
        deleteMed.innerHTML = "";


        try {
            // intenta hacer una peticion get al backend
            const response = await fetch(`${BASE_URL}medicamentos/productos/${idMedicamento}`);

            // transformo la respuesta en un vector de productos
            const productos = await response.json();

            // si no hay productos farma. para es medicamento indico que no existen.
            if (productos.length == 0) {

                cards.innerHTML = `
                    <p class="text-muted">
                        Este medicamento no tiene productos farmaceuticos.
                    </p>
                `;

            } else {
                //Sino creo una carta para cada producto farma., la cual cada una tendra su opcion de borrado
                productos.forEach(prod => {

                    cards.innerHTML += `
                        <div class="col-md-4">
                            <div class="card shadow-sm p-3 h-100">

                                <h6>
                                    ${prod.dosis_producto}
                                    ${prod.nombre_medida}
                                </h6>

                                <p class="text-muted mb-1">
                                    ${prod.nombre_tipo_producto}
                                </p>

                                <p class="small text-secondary">
                                    ${prod.comentario ?? ""}
                                </p>

                                <form
                                  method="POST"
                                  action="/descripciones/delete/${prod.id_producto}"
                                >
                                    <button
                                      type="submit"
                                      class="btn btn-outline-danger btn-sm w-100"
                                    >
                                        X
                                    </button>
                                </form>

                            </div>
                        </div>
                    `;
                });

            }

            // En caso contrario, en el div que permitira el borrado entero del medicamento, creo el form para borrarlo
            deleteMed.innerHTML = `
                <form
                  method="POST"
                  action="/medicamentos/delete/${idMedicamento}"
                >
                    <button
                      type="submit"
                      class="btn btn-danger"
                    >
                        🗑️ Eliminar medicamento completo
                    </button>
                </form>
            `;

        } catch (error) {

            cards.innerHTML = `
                <p class="text-danger">
                    Error al cargar descripciones.
                </p>
            `;

            console.error(error);
        }

    });

});