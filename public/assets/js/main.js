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
                                  action="${BASE_URL}productos/eliminacion/${prod.id_producto}"
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
                  action="${BASE_URL}medicamentos/eliminacion/${idMedicamento}"
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


// PEDIDOS
let ordenFecha = "ASC";

document.addEventListener("DOMContentLoaded", () => {

    // agarra el select para el filtrado segun estados
    const estadoSelect = document.getElementById("selectFiltradoEstados");
    // agarra el select para el filtrado segun servicios
    const servicioSelect = document.getElementById("selectFiltradoServicios");
    // escucha el boton para cambiar el orden de la tabla segun la fecha
    const btnOrden = document.getElementById("btnOrdenFecha");
    // es el icono para el cambio del orden de la tabla, es para cambiarlo dsp
    const icon = document.getElementById("iconOrdenFecha");

    // se fija cuando cambian esos selects, y en caso de que cambien, llama al metodo filtrar
    estadoSelect.addEventListener("change", filtrar);
    servicioSelect.addEventListener("change", filtrar);

    // escucha cuando se clickea sobre el boton para el orden
    btnOrden.addEventListener("click", () => {
        // si el orden no es ASC, le coloco DESC
        ordenFecha = (ordenFecha === "ASC") ? "DESC" : "ASC";

        // cambio el icono
        if (ordenFecha === "ASC") {
            icon.classList.remove("bi-caret-down-fill");
            icon.classList.add("bi-caret-up-fill");
        } else {
            icon.classList.remove("bi-caret-up-fill");
            icon.classList.add("bi-caret-down-fill");
        }
        // filtro la tabla
        filtrar();
    });

    function filtrar() {
        // define el estado y al servicio segun el ID que se obtiene al elegir una opcion de los select.
        const estado = estadoSelect.value;
        const servicio = servicioSelect.value;

        // envia los datos del estado, servicio  y el orden segun fecha a través de una query string, para trabajar con estos

        // en el fetch se hace lo siguiente:
        // 1- envia una request get con los filtros y le coloca el header:  "X-Requested-With": "XMLHttpRequest"
        fetch(`${BASE_URL}/filtrarPedidos/?idEstado=${estado}&idServicio=${servicio}&orden=${ordenFecha}`, {
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
            // este header se usa para que el if($this->request->isAJAX()) devuelva true
        })
        // agarro la respuesta del server y lo transformo en texto (para usarlo como html)
        .then(res => res.text())
        // ahora agarro el html recibido y lo encajo en el body de la tabla pedidos
        .then(html => {
            document.getElementById("tablaPedidos").innerHTML = html;
        })
        .catch(err => console.error("Error:", err));
    }
});




// Cantidad maxima de detalles que habra por pedido
const MAX_DETALLES = 10;
// Contador que se ira actualizando en base a las cartas
let contadorDetalles = 0;

document.addEventListener("DOMContentLoaded", () => {

    // Si el boton para agregar multiples cartas se clickea, llamo a la funcion esa
    document.getElementById("crearMultiplesCards")
        .addEventListener("click", crearMultiplesCards);

    // Si el boton para agregar una unica carta se clickea, llamo a la funcion esa
    document.getElementById("crearCardIndividual")
        .addEventListener("click", crearCard);

    
    document.getElementById("crearPedidoForm")
        .addEventListener("reset", resetearFormulario);

    // Creo una siempre
    crearCard();
});

// Metodo para crear una alerta del limite
function mostrarAlerta() {
    document.getElementById("alertaLimite").classList.remove("d-none");
}

// Metodo para ocultar la alerta del limite
function ocultarAlerta() {
    document.getElementById("alertaLimite").classList.add("d-none");
}

// Metodo para mostrar la alerta toast del html
function mostrarToast() {
    // Agarra el elemento toast
    const toastElement = document.getElementById("toastDuplicado");

    // Creo el toast
    const toast = new bootstrap.Toast(toastElement);

    // Lo muestra
    toast.show();
}

// Metodo que actualiza la vista en caso de que se haya alcanzado el limite de detalles
function actualizarVistaLimitesAlcanzados() {
    const disabled = contadorDetalles >= MAX_DETALLES;

    // Activo o desactivo los botones en base a la cantidad de detalles que hay
    document.getElementById("crearCardIndividual").disabled = disabled;
    document.getElementById("crearMultiplesCards").disabled = disabled;

    // Muestro o desactivo la alerta de limite en base a la cantidad de detalles
    if (disabled) mostrarAlerta();
    else ocultarAlerta();
}

// Metodo para crear multiples cartas
function crearMultiplesCards() {

    // Agarro el valor del input que establece cuantos detalles se desea crear de una
    const cantidadInput = document.getElementById("cantidadCards");
    let cantidad = parseInt(cantidadInput.value);

    // Si el valor es null, o menor a uno, no creo nada
    if (isNaN(cantidad) || cantidad < 1) return;

    // Calculo la cantidad de detalles que puedo crear
    const disponibles = MAX_DETALLES - contadorDetalles;


    // Si la cantidad a crear es mayor que los disponibles, ajusto esta cantidad para crear unicamente los detalles que puedo
    if (cantidad > disponibles) {
        cantidad = disponibles;
    }

    // Creo las cartas
    for (let i = 0; i < cantidad; i++) {
        crearCard();
    }

    // limpio el input de cantidad de cartas
    cantidadInput.value = "";

    actualizarVistaLimitesAlcanzados();
}

// Funcion para crear una carta individual
function crearCard() {
    // Sumo uno al contador de detalles 
    contadorDetalles++;

    // Agarro el template establecido en el html de las cartas y lo clono
    const template = document.getElementById("detalleCardTemplate");
    const clone = template.content.cloneNode(true);

    // Agarro al contenedor donde estan las cartas
    const wrapper = clone.querySelector(".col-md-6");

    // Sustituyo los atributos que posee el template por el valor de la carta
    wrapper.innerHTML = wrapper.innerHTML.replace(/INDEX/g, contadorDetalles);

    // Actualizo el numero de la carta que es visible
    wrapper.querySelector(".detalleNumero").textContent = contadorDetalles;

    // Agrego la carta
    document.getElementById("cardsContainer").appendChild(wrapper);

    // Agrego las funcionalidades a la carta
    configurarEventosCard(wrapper);

    actualizarVistaLimitesAlcanzados();
}

function configurarEventosCard(card) {

    // Agarro el select del medicamento, del producto, el boton de eliminar de la carta, y el cambio del stock
    const medSelect = card.querySelector(".medicamento-select");
    const productSelect = card.querySelector(".producto-select");
    const btnEliminar = card.querySelector(".btn-eliminar-card");
    const cantidadInput = card.querySelector("[name*='cantidad_medicamento']");

    // Si el select del medicamento cambia
    medSelect.addEventListener("change", async function () {
        // Agarro el id del medicamento seleccionado
        const id = this.value;
        // Si no se agarra nada, vuelvo para atras
        if (!id) return;

        // Pongo temporalmente la opcion de "Cargando" en el select del producto y lo desabilito
        productSelect.innerHTML = `<option>Cargando...</option>`;
        productSelect.disabled = true;
        // De ahi pruebo el fetch
        try {
            // Hago un pedido al backend para obtener los productos de ese medicamento y espero su respuesta
            const res = await fetch(`${BASE_URL}medicamentos/productos/${id}`);
            const productos = await res.json();

            // Agrego la opcion placeholder del select 
            productSelect.innerHTML = `<option disabled selected>Seleccione...</option>`;

            // Voy agregando las opciones del producto farmaceutico
            productos.forEach(p => {
                const opt = document.createElement("option");
                opt.value = p.id_producto;
                opt.textContent = `${p.nombre_tipo_producto} - ${p.dosis_producto} ${p.nombre_medida}`;
                productSelect.appendChild(opt);
            });

            // Habilito la seleccion de los productos
            productSelect.disabled = false;

        } catch {
            productSelect.innerHTML = `<option>Error</option>`;
        }
    });

    // Si se elimina una carta, la elimino y reordeno las restantes
    btnEliminar.addEventListener("click", () => {
        card.remove();
        reordenar();
    });

    // Verifica duplicados recien cuando cambia la cantidad
    cantidadInput.addEventListener("blur", () => verificarDuplicados(card));
}

// Funcion para la reordenacion de las cartas en caso de su eliminacion
function reordenar() {
    // Agarro el div que posee todas las cartas
    const cards = document.querySelectorAll("#cardsContainer .col-md-6");

    // Voy recorriendo una a una las cartas
    cards.forEach((card, i) => {
        // Creo un valor nuevo
        const n = i + 1;
        // Asigno ese valor al numero visible, y lo agrego como id invisible de la carta
        card.querySelector(".detalleNumero").textContent = n;

        /* Busca todos los inputs cuyo atributo name empieza con `detalles` y corrijo sus nombres.  
        Ejemplo: si antes era detalles[3][campo], lo reemplaza por detalles[1][campo] segun la nueva posicion.*/
        card.querySelectorAll("[name^='detalles']").forEach(input => {
            input.name = input.name.replace(/detalles\[\d+\]/, `detalles[${n}]`);
        });
    });

    contadorDetalles = cards.length;
    actualizarVistaLimitesAlcanzados();
}

// Metodo para borrar todas las cartas, en caso de que se apriete el boton de reset
function resetearFormulario() {
    setTimeout(() => {
        // Borro todas las cards
        document.getElementById("cardsContainer").innerHTML = "";
        // Reinicio el contador de cartas
        contadorDetalles = 0;
        // En caso de haber estado activada, oculto la alerta de limite de cartas
        ocultarAlerta();
        // Reactivo los controles, por las dudas, en caso de que hayan estado desactivados
        actualizarVistaLimitesAlcanzados();
        // Creo una card vacía inicial
        crearCard();
    }, 0);
}

// Funcion para evitar medicamentos/productos duplicados
function verificarDuplicados(cardActual) {

    // Obtengo los valores de la carta nueva
    const medicamentoActual = cardActual.querySelector(".medicamento-select").value;
    const productoActual = cardActual.querySelector(".producto-select").value;

    // Si los datos no estan completos, vuelvo
    if (!medicamentoActual || !productoActual) return;

    // Agarro la cantidad deseada de la nueva carta
    const cantidadActualInput = cardActual.querySelector("[name*='cantidad_medicamento']");
    const cantidadActual = parseInt(cantidadActualInput.value) || 0;

    // Recorro todas las cards
    const cards = document.querySelectorAll("#cardsContainer .col-md-6");
    cards.forEach(card => {

        // Si la carta es la actual, vuelvo
        if (card === cardActual) return;

        // Sino, agarro el producto y medicamento de la carta sobre la cual estoy
        const medicamento = card.querySelector(".medicamento-select").value;
        const producto = card.querySelector(".producto-select").value;

        // Si el medicamento y el producto son iguales que la de la carta actual
        if (
            medicamento === medicamentoActual &&
            producto === productoActual
        ) {

            // Sumo las cantidades a pedir
            const cantidadExistenteInput =
                card.querySelector("[name*='cantidad_medicamento']");

            const cantidadExistente =
                parseInt(cantidadExistenteInput.value) || 0;

            cantidadExistenteInput.value =
                cantidadExistente + cantidadActual;

            // Agarro la carta vieja
            const cardVisual = card.querySelector(".card"); 
            // Le agrego la clase css de "card-destacada" para resaltar la carta vieja
            cardVisual.classList.add("card-destacada");

            // Elimino la card repetida (la actual)
            cardActual.remove();

            // Reordeno
            reordenar();

            // Espero un toque para que el DOM se actualice
            setTimeout(() => {

                // Le hago un scroll hasta la carta vieja
                cardVisual.scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });

            }, 100);

            // Quito el resaltado despues de un tiempo
            setTimeout(() => {
                cardVisual.classList.remove("card-destacada");
            }, 2500);

            // Muestro el toast
            mostrarToast("Se combinaron los detalles repetidos");

            // Corto el recorrido
            return;
        }
    });
}