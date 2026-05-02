<?php
$estado = $pedido->tipo_estado_pedido;

$esPendiente = $estado === 'Pendiente';
$esRechazado = $estado === 'Rechazado';
$esAprobado  = $estado === 'Aprobado';
?>

<div class="container mt-4">

    <!-- ===================== -->
    <!-- 🔔 ALERTAS -->
    <!-- ===================== -->

    <?php if ($esRechazado): ?>
        <div class="alert alert-danger shadow-sm">
            ❌ El pedido ya fue rechazado
        </div>
    <?php elseif ($esAprobado): ?>
        <div class="alert alert-success shadow-sm">
            ✅ El pedido ya fue aprobado
        </div>
    <?php endif; ?>



    <!-- ===================== -->
    <!-- 🧾 HEADER + INFO -->
    <!-- ===================== -->

    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center bg-dark text-white">
            <h5 class="mb-0">
                Pedido #<?= $pedido->id_pedido ?>
            </h5>

            <!-- Badge de estado -->
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
                    <div class="fw-semibold"><?= $pedido->fecha_solicitud_pedido ?></div>
                </div>

                <div class="col-md-4">
                    <small class="text-muted">Servicio Médico</small>
                    <div class="fw-semibold"><?= $pedido->nombre_servicio_medico ?></div>
                </div>

                <div class="col-md-12">
                    <small class="text-muted">Comentario</small>
                    <div class="fw-semibold">
                        <?= $pedido->comentario_pedido ?: 'Sin comentarios' ?>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <!-- ===================== -->
<!-- 💊 PRODUCTOS -->
<!-- ===================== -->

<div class="card mb-4 shadow-sm border-0">

    <!-- Header consistente -->
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0">Productos del Pedido</h5>
    </div>

    <div class="card-body">

        <?php foreach ($detalles as $d): ?>
            <div class="border rounded p-3 mb-3">

                <div class="row align-items-center">

                    <!-- Medicamento -->
                    <div class="col-md-3">
                        <small class="text-muted">Medicamento</small>
                        <div class="fw-semibold">
                            <?= esc($d->nombre_medicamento) ?>
                        </div>
                    </div>

                    <!-- Tipo -->
                    <div class="col-md-3">
                        <small class="text-muted">Tipo</small>
                        <div class="fw-semibold"><?= esc($d->nombre_tipo_producto) ?></div>
                    </div>

                    <!-- Dosis -->
                    <div class="col-md-3">
                        <small class="text-muted">Dosis</small>
                        <div>
                            <span class="badge bg-light text-dark border">
                                <?= esc($d->dosis_producto . ' ' . $d->nombre_medida) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Proveedor -->
                    <div class="col-md-3">
                        <small class="text-muted">Proveedor</small>
                        <div class="fw-semibold"><?= esc($d->nombre_proveedor) ?></div>
                    </div>

                </div>

            </div>
        <?php endforeach; ?>

    </div>
</div>


    <!-- ===================== -->
    <!-- 🔘 ACCIONES -->
    <!-- ===================== -->

    <?php if ($esPendiente): ?>
        <div id="acciones" class="d-flex gap-2">

            <form method="post" action="<?= base_url('pedidos/aprobar') ?>">
                <input type="hidden" name="idPedido" value="<?= $pedido->id_pedido ?>">
                <button class="btn btn-outline-primary">
                    Aprobar
                </button>
            </form>

            <button class="btn btn-outline-danger" onclick="mostrarRechazo()">
                Rechazar
            </button>

        </div>
    <?php endif; ?>


    <!-- ===================== -->
    <!-- ❌ RECHAZO -->
    <!-- ===================== -->

    <div id="formRechazo"
         class="mt-4"
         style="<?= ($esRechazado ? 'display:block;' : 'display:none;') ?>">

        <div class="card border-danger shadow-sm">
            <div class="card-header bg-danger text-white">
                Motivo del rechazo
            </div>

            <div class="card-body">

                <form method="post" action="<?= base_url('pedidos/rechazar') ?>">

                    <input type="hidden" name="idPedido" value="<?= $pedido->id_pedido ?>">

                    <textarea name="motivo_rechazo"
                              class="form-control mb-3"
                              rows="3"
                              placeholder="Opcional..."
                              <?= !$esPendiente ? 'readonly' : '' ?>><?= $esRechazado ? esc($pedido->motivo_cancelacion_pedido) : '' ?></textarea>

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
function mostrarRechazo() {
    document.getElementById('acciones').style.display = 'none';
    document.getElementById('formRechazo').style.display = 'block';
}
</script>