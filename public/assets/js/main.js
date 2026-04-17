document.addEventListener("DOMContentLoaded", function (event) {
  const showNavbar = (toggleId, navId, bodyId, headerId) => {
    const toggle = document.getElementById(toggleId),
      nav = document.getElementById(navId),
      bodypd = document.getElementById(bodyId),
      headerpd = document.getElementById(headerId);
    // Validate that all variables exist
    if (toggle && nav && bodypd && headerpd) {
      toggle.addEventListener("click", () => {
        // show navbar
        nav.classList.toggle("sidebarshow");
        // change icon
        toggle.classList.toggle("bx-x");
        // add padding to body
        bodypd.classList.toggle("body-pd");
        // add padding to header
        headerpd.classList.toggle("body-pd");
      });
    }
  };
  showNavbar("header-toggle", "nav-bar", "body-pd", "header");
  /*===== LINK ACTIVE =====*/
  const linkColor = document.querySelectorAll(".nav_link");

  function colorLink() {
    if (linkColor) {
      linkColor.forEach((l) => l.classList.remove("active"));
      this.classList.add("active");
    }
  }
  linkColor.forEach((l) => l.addEventListener("click", colorLink));
  // Your code to run since DOM is loaded and ready
});


document.addEventListener("DOMContentLoaded", function () {
  const select = document.getElementById("medSelectCreate");
  const input = document.getElementById("nameInputCreate");

  select.addEventListener("change", function () {
    const selectedOption = this.options[this.selectedIndex];

    if (this.value === "new") {
      input.disabled = false;
      input.required = true;
    } else {
      const nombre = selectedOption.getAttribute("data-nombre");
      input.disabled = true;
      input.required = false;
      input.value = nombre;
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
    } else {
      const nombre = selectedOption.getAttribute("data-nombre");
      input.disabled = true;
      input.required = false;
      input.value = nombre;
    }
  });
});


document.addEventListener("DOMContentLoaded", function () {
  // Agarro las cosas relacionadas a los medicamentos
  const medSelect = document.getElementById("medSelectUpdate");
  const nameInput = document.getElementById("nameInputUpdate");

  // Agarro las cosas relacionadas a las descripciones
  const descriptionSelect = document.getElementById("descriptionSelectUpdate");
  const typeInput = document.getElementById("typeInputUpdate");
  const dosageInput = document.getElementById("dosageInputUpdate");
  const measurementInput = document.getElementById("measurementInputUpdate");
  const descripcionTextarea = document.getElementById("descripcionInputCreate");

  nameInput.disabled = true;
  descriptionSelect.disabled = true;

  disableDescriptionFields();

  // Función para deshabilitar las cosas de la descripción
  function disableDescriptionFields() {
    typeInput.disabled = true;
    dosageInput.disabled = true;
    measurementInput.disabled = true;
    descripcionTextarea.disabled = true;

    typeInput.required = false;
    dosageInput.required = false;
    measurementInput.required = false;
  }

  // Funcion para limpiar los campos de las cosas de descripcion
  function clearDescriptionFields() {
    typeInput.value = "";
    dosageInput.value = "";
    measurementInput.value = "";
    descripcionTextarea.value = "";
  }

  // Cuando se selecciona un med
  medSelect.addEventListener("change", function () {
    const selectedOption = this.options[this.selectedIndex];
    const nombre = selectedOption.getAttribute("data-nombre");

    // Se habilita el campo de nombre de med
    nameInput.disabled = false;
    nameInput.required = true;
    nameInput.value = nombre || "";

    // Se habilita el select de descripcion
    descriptionSelect.disabled = false;

    // Se resetea a la opcion por defecto del select de descripcion
    descriptionSelect.value = "-1";

    disableDescriptionFields();
    clearDescriptionFields();
  });

  // Cuando se selecciona una descripcion
  descriptionSelect.addEventListener("change", function () {
    const selectedOption = this.options[this.selectedIndex];

    // Si el campo seleccionado es el por defecto, reinicio todo
    if (this.value === "-1") {
      disableDescriptionFields();
      clearDescriptionFields();
      return;
    }

    // Sino, habilito las cosas de descripcion
    typeInput.disabled = false;
    dosageInput.disabled = false;
    measurementInput.disabled = false;
    descripcionTextarea.disabled = false;

    typeInput.required = true;
    dosageInput.required = true;
    measurementInput.required = true;

    // 💡 Autocompletar
    dosageInput.value = selectedOption.dataset.dosis || "";
    typeInput.value = selectedOption.dataset.tipoId || "";
    measurementInput.value = selectedOption.dataset.unidadId || "";
    descripcionTextarea.value = selectedOption.dataset.descripcion || "";
  });
});


function createDescriptionCard(desc) {
  return `
    <div class="col-12 col-md-4">
      <div class="card p-3 shadow-sm">
        <h6>${desc.dosis} ${desc.unidad}</h6>
        <p class="mb-2 text-muted">${desc.tipo}</p>

        <button 
          class="btn btn-outline-danger btn-sm delete-desc-btn"
          data-id="${desc.id}"
        >
          🗑️ Eliminar
        </button>
      </div>
    </div>
  `;
}

document.addEventListener("DOMContentLoaded", () => {
  const medSelect = document.getElementById("medSelectDelete");
  const medInfo = document.getElementById("medInfo");
  const medName = document.getElementById("medName");
  const descContainer = document.getElementById("descContainer");
  const deleteMedBtn = document.getElementById("deleteMedBtn");

  let currentMedId = null;

  // 🔄 Cuando selecciona medicamento
  medSelect.addEventListener("change", async function () {
    const medId = this.value;

    if (medId === "-1") {
      medInfo.classList.add("d-none");
      return;
    }

    currentMedId = medId;

    // 🔥 FETCH AL BACKEND (traer descripciones)
    const response = await fetch(`/medicamentos/${medId}/descripciones`);
    const data = await response.json();

    // Mostrar info
    medInfo.classList.remove("d-none");
    medName.textContent = data.nombre;

    // Renderizar cards
    descContainer.innerHTML = "";
    data.descripciones.forEach(desc => {
      descContainer.innerHTML += createDescriptionCard(desc);
    });
  });

  // 🗑️ Eliminar descripción (delegación de eventos)
  descContainer.addEventListener("click", async (e) => {
    if (!e.target.classList.contains("delete-desc-btn")) return;

    const id = e.target.dataset.id;

    if (!confirm("¿Eliminar esta descripción?")) return;

    await fetch(`/descripciones/${id}`, {
      method: "DELETE"
    });

    // 🔄 refrescar
    medSelect.dispatchEvent(new Event("change"));
  });

  // 🗑️ Eliminar medicamento completo
  deleteMedBtn.addEventListener("click", async () => {
    if (!confirm("Esto eliminará TODAS las descripciones. ¿Continuar?")) return;

    await fetch(`/medicamentos/${currentMedId}`, {
      method: "DELETE"
    });

    // reset UI
    medSelect.value = "-1";
    medInfo.classList.add("d-none");
  });
});