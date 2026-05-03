<!-- crearPedido.php -->
<div class="container-fluid py-4">
    <form id="crearPedidoForm" method="POST" action="<?= base_url('pedidos/guardar') ?>">
        <!-- Sección 1: Datos generales del pedido -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Datos generales del pedido</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="servicioMedico" class="form-label">Servicio Médico *</label>
                        <select id="servicioMedico" name="id_servicio_medico" class="form-select" required>
                            <option value="" selected disabled hidden>Seleccione un servicio médico...</option>
                            <?php foreach ($servicios as $id => $nombre): ?>
                                <option value="<?= $id ?>">
                                    <?= esc($nombre) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="fechaPedido" class="form-label">Fecha de solicitud *</label>
                        <input type="date" id="fechaPedido" name="fecha_solicitud_pedido" class="form-control" 
                               value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 2: Detalles del pedido (cards) -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Detalles del pedido</h5>
            </div>
            <div class="card-body">
                <!-- Formas de creación de cards -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6>Crear múltiples detalles</h6>
                                <div class="input-group">
                                    <input type="number" id="cantidadCards" class="form-control" 
                                           placeholder="Número de detalles" min="1" max="20">
                                    <button type="button" id="crearMultiplesCards" class="btn btn-secondary">
                                        Crear X detalles
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6>Crear detalle individual</h6>
                                <button type="button" id="crearCardIndividual" class="btn btn-primary">
                                    + Agregar nuevo detalle
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contenedor de cards -->
                <div id="cardsContainer" class="row"></div>
            </div>
        </div>

        <!-- Sección 3: Comentario adicional -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Información adicional</h5>
            </div>
            <div class="card-body">
                <label for="comentarioPedido" class="form-label">Comentario del pedido</label>
                <textarea id="comentarioPedido" name="comentario_pedido" class="form-control" 
                          rows="3" placeholder="Observaciones adicionales sobre el pedido..."></textarea>
            </div>
        </div>

        <!-- Botón de submit -->
        <div class="text-end">
            <button type="submit" class="btn btn-success btn-lg">Crear Pedido</button>
        </div>
    </form>
</div>

<!-- Template para una card de detalle -->
<template id="detalleCardTemplate">
    <div class="col-md-6 col-lg-4 mb-3">
        <div class="card h-100">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <strong>Detalle #<span class="detalleNumero">1</span></strong>
                <button type="button" class="btn-close btn-eliminar-card" aria-label="Eliminar"></button>
            </div>
            <div class="card-body">
                <input type="hidden" name="detalles[INDEX][id]" class="detalleId" value="INDEX">
                
                <div class="mb-3">
                    <label class="form-label">Medicamento *</label>
                    <select class="form-select medicamento-select" name="detalles[INDEX][id_medicamento]" required>
                        <option value="" selected disabled hidden>Seleccione medicamento...</option>
                        <?php foreach ($medicamentos as $id => $nombre): ?>
                            <option value="<?= $id ?>"><?= esc($nombre) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Producto farmacéutico *</label>
                    <select class="form-select producto-select" name="detalles[INDEX][id_producto]" disabled required>
                        <option value="">Primero seleccione un medicamento</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Cantidad *</label>
                    <input type="number" class="form-control cantidad-input" name="detalles[INDEX][cantidad_medicamento]" 
                           min="1" step="1" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Proveedor *</label>
                    <select class="form-select proveedor-select" name="detalles[INDEX][id_proveedor]" required>
                        <option value="" selected disabled hidden>Seleccione proveedor...</option>
                        <?php foreach ($proveedores as $id => $nombre): ?>
                            <option value="<?= $id ?>"><?= esc($nombre) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>