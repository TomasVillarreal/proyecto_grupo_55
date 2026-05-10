<div class="container py-5" style="max-width: 1100px;">

    <form id="crearPedidoForm" method="POST" action="<?= base_url('pedidos/crearPedido') ?>">

        <input type="hidden" name="fecha_solicitud_pedido" value="<?= date('Y-m-d') ?>">

        <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap g-3">
            <div>
                <h2 class="fw-bold tracking-tight mb-1" style="color: #0f172a;">Crear Pedido</h2>
                <p class="text-muted mb-0">Complete la información general y agregue los detalles necesarios</p>
            </div>
        </div>

        <div id="alertaLimite" class="alert alert-warning d-none">
            Máximo 10 detalles alcanzado
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="section-title mb-3">
                    <span class="text-primary"><i class="bi bi-file-earmark-text"></i></span> Datos Generales
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Servicio Médico</label>
                        <select name="id_servicio_medico" class="form-select" required>
                            <option value="" disabled selected hidden>Seleccione el servicio...</option>
                            <?php foreach ($servicios as $id => $nombre): ?>
                                <option value="<?= $id ?>"><?= esc($nombre) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha de Solicitud</label>
                        <div class="dato-estatico">
                            <i class="bi bi-calendar-check text-muted"></i>
                            <?= date('d/m/Y') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="section-title mb-0">
                        <span class="text-primary"><i class="bi bi-capsule"></i></span> Detalles del Pedido
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light d-flex flex-column justify-content-center h-100">
                            <label class="form-label small fw-semibold text-muted mb-2">Crear múltiples tarjetas</label>
                            <div class="input-group">
                                <input type="number" id="cantidadCards" class="form-control"
                                       placeholder="¿Cuántos detalles?" min="1" max="9">
                                <button type="button" id="crearMultiplesCards" class="btn btn-outline-secondary px-3">
                                    Generar
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 d-flex align-items-end">
                        <button type="button" id="crearCardIndividual" class="btn btn-primary w-100 h-100 py-3 py-md-2 d-flex align-items-center justify-content-center">
                            <span class="fs-5 me-2">+</span> Agregar detalle individual
                        </button>
                    </div>
                </div>

                <div id="cardsContainer" class="row g-3"></div>

            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="section-title mb-3">
                    <span class="text-primary"><i class="bi bi-chat-left-dots"></i></span> Observaciones
                </div>
                <textarea name="comentario_pedido" class="form-control"
                          rows="3" placeholder="Agregue algún comentario u observación adicional si lo requiere..."></textarea>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-3 align-items-center">
            <button type="reset" id="reset_button" class="btn btn-light border px-4 py-2">
                Limpiar campos
            </button>
            <button type="submit" class="btn btn-success px-5 py-2 fw-semibold shadow-sm">
                Guardar Pedido
            </button>
        </div>

    </form>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="toastDuplicado"
         class="toast align-items-center text-bg-success border-0"
         role="alert">

        <div class="d-flex">
            <div class="toast-body">
                Se combinaron los detalles repetidos.
            </div>

            <button type="button"
                    class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast">
            </button>
        </div>

    </div>
</div>



<template id="detalleCardTemplate">
    <div class="col-md-6 col-lg-4">

        <div class="card shadow-sm h-100 detalle-card">

            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid #f1f5f9;">
                <span class="fw-bold text-secondary fs-6">
                    Detalle #<span class="detalleNumero">1</span>
                </span>
                <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-card" title="Remover este detalle">
                    <i class="bi bi-trash"></i> ✕
                </button>
            </div>

            <div class="card-body p-3">

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Medicamento</label>
                    <select class="form-select medicamento-select" name="detalles[INDEX][id_medicamento]" required>
                        <option value="" disabled selected hidden>Seleccione...</option>
                        <?php foreach ($medicamentos as $id => $nombre): ?>
                            <option value="<?= $id ?>"><?= esc($nombre) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Producto</label>
                    <select class="form-select producto-select" name="detalles[INDEX][id_producto]" disabled required>
                        <option value="">Primero seleccione un medicamento</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Cantidad</label>
                    <input type="number" class="form-control"
                           name="detalles[INDEX][cantidad_medicamento]"
                           placeholder="0" min="1" required>
                </div>

                <div class="mb-1">
                    <label class="form-label small fw-semibold">Proveedor</label>
                    <select class="form-select" name="detalles[INDEX][id_proveedor]" required>
                        <option value="" disabled selected hidden>Seleccione...</option>
                        <?php foreach ($proveedores as $id => $nombre): ?>
                            <option value="<?= $id ?>"><?= esc($nombre) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

        </div>
    </div>
</template>