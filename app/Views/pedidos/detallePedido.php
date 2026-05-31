<?php
// Establezco los distintos estados en los que puede estar el pedido
$estado = $pedido->obtenerEstado()->obtenerNombre();

$esPendiente = $estado === 'Pendiente';
$esRechazado = $estado === 'Rechazado';
$esAprobado  = $estado === 'Aprobado';
?>

<div class="container mt-4">

    <!-- Verifico si es rechazado, y si ya fue rechazado, creo un flashdata avisando esto -->
    <?php if ($esRechazado): ?>
        <div class="alert alert-danger shadow-sm">
            ❌ El pedido ya fue rechazado
        </div>
    <!-- Verifico si el estado es aprobado, y si ya fue aprobado, creo un flashdata avisando esto -->
    <?php elseif ($esAprobado): ?>
        <div class="alert alert-success shadow-sm">
            ✅ El pedido ya fue aprobado
        </div>
    <?php endif; ?>

    <!-- Flashdata correspondientes -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>


    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center bg-dark text-white">
            <h5 class="mb-0">
                Pedido #<?= $pedido->obtenerID() ?>
            </h5>

            <!-- Badge para los colores de los estados -->
            <?php
                $badge = 'secondary';
                if ($esPendiente) $badge = 'warning text-dark';
                if ($esAprobado)  $badge = 'success';
                if ($esRechazado) $badge = 'danger';
            ?>
            <span class="badge bg-<?= $badge ?> px-3 py-2">
                <?= $estado ?>
            </span>
        </div>

        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-4">
                    <small class="text-muted">Fecha</small>
                    <div class="fw-semibold"><?= $pedido->obtenerFechaSolicitud()->format('Y-m-d H:i:s') ?></div>
                </div>

                <div class="col-md-4">
                    <small class="text-muted">Servicio Médico</small>
                    <div class="fw-semibold"><?= $pedido->obtenerServicioMedico()->obtenerNombre() ?></div>
                </div>

                <div class="col-md-12">
                    <small class="text-muted">Comentario</small>
                    <div class="fw-semibold">
                        <?= $pedido->obtenerComentario() ?: 'Sin comentarios' ?>
                    </div>
                </div>

            </div>
        </div>
    </div>



    <div class="card mb-4 shadow-sm border-0">

        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Productos del Pedido</h5>
        </div>

        <div class="card-body">

            <?php foreach ($detalles_pedido as $d): ?>
                <div class="border rounded p-3 mb-3">

                    <div class="row align-items-center">

                        <!-- Medicamento -->
                        <div class="col-md-3">
                            <small class="text-muted">Medicamento</small>
                            <div class="fw-semibold">
                                <?= esc($d->obtenerProducto()->obtenerMedicamento()->obtenerNombre()) ?>
                            </div>
                        </div>

                        <!-- Tipo -->
                        <div class="col-md-3">
                            <small class="text-muted">Tipo</small>
                            <div class="fw-semibold"><?= esc($d->obtenerProducto()->obtenerMedicamento()->obtenerNombre()) ?></div>
                        </div>

                        <!-- Dosis -->
                        <div class="col-md-3">
                            <small class="text-muted">Dosis</small>
                            <div>
                                <span class="badge bg-light text-dark border">
                                    <?= esc($d->obtenerProducto()->obtenerDosis() . ' ' . $d->obtenerProducto()->obtenerUnidadMedida()->obtenerNombre()) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Proveedor -->
                        <div class="col-md-3">
                            <small class="text-muted">Proveedor</small>
                            <div class="fw-semibold"><?= esc($d->obtenerProveedor()->obtenerNombre()) ?></div>
                        </div>

                    </div>

                </div>
            <?php endforeach; ?>

        </div>
    </div>



    <!-- Div que contiene los botones de aprobado o rechazado, solo aparece si el pedido es pendiente-->
    <?php if ($esPendiente): ?>
        <!-- Le agrego un id para ocultarlo luego -->
        <div id="acciones" class="d-flex gap-2">

            <form method="post" action="<?= base_url('pedidos/aprobar') ?>">
                <input type="hidden" name="idPedido" value="<?= $pedido->obtenerID() ?>">
                <button class="btn btn-outline-primary">
                    Aprobar
                </button>
            </form>

            <!-- Boton que cuando se aprieta hace que el div "formRechazo" aparezca -->
            <button type="button" class="btn btn-outline-danger" onclick="mostrarRechazo()">
                Rechazar
            </button>

        </div>
    <?php endif; ?>


    <!-- El div no aparece si el pedido no fue rechazado -->
    <div id="formRechazo"
         class="mt-4 <?= $esRechazado ? '' : 'd-none' ?>">

        <div class="card border-danger shadow-sm">
            <div class="card-header bg-danger text-white">
                Motivo del rechazo
            </div>

            <div class="card-body">
                <form method="post" action="<?= base_url('pedidos/rechazar') ?>">
                    <input type="hidden" name="idPedido" value="<?= $pedido->obtenerID() ?>">
                    <!-- El textarea es unicamente escribible si el pedido esta en pendiente, sino no se puede escribir-->
                    <textarea name="motivo_rechazo"
                              class="form-control mb-3"
                              rows="3"
                              placeholder="Opcional..."
                              <?= !$esPendiente ? 'readonly' : '' ?>><?= $esRechazado ? esc($pedido->obtenerMotivoRechazo()) : '' ?></textarea>
                    <?php if ($esPendiente): ?>
                        <button class="btn btn-danger">
                            Confirmar Rechazo
                        </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

</div>


<script>
    // Si se aprieta el boton de rechazo, oculto el div de acciones y hago que el de formRechazo aparezca
    function mostrarRechazo() {
        document.getElementById('acciones').classList.add('d-none');
        document.getElementById('formRechazo').classList.remove('d-none');
    }
</script>