<?php
$pageTitle = 'Dashboard Repartidor';
include __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-truck me-2"></i> Dashboard Repartidor</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>

<!-- Estadísticas del Día -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card blue">
            <i class="fas fa-shipping-fast"></i>
            <p>Entregas Asignadas</p>
            <h3 id="entregasAsignadas">0</h3>
            <small>Hoy</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card green">
            <i class="fas fa-check-circle"></i>
            <p>Entregas Completadas</p>
            <h3 id="entregasCompletadas">0</h3>
            <small>Hoy</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card orange">
            <i class="fas fa-clock"></i>
            <p>En Ruta</p>
            <h3 id="entregasEnRuta">0</h3>
            <small>Actualmente</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card red">
            <i class="fas fa-dollar-sign"></i>
            <p>Total Recaudado</p>
            <h3 id="totalRecaudado">S/ 0.00</h3>
            <small>Hoy</small>
        </div>
    </div>
</div>

<!-- Entregas Pendientes -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-list-alt me-2"></i> Mis Entregas Pendientes
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Dirección</th>
                                <th>Teléfono</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaEntregasPendientes">
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-check-double fa-3x mb-3 text-success"></i>
                                    <p>¡Genial! No hay entregas pendientes</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Historial de Entregas -->
        <div class="card mt-4">
            <div class="card-header">
                <i class="fas fa-history me-2"></i> Historial de Hoy
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Hora Entrega</th>
                                <th>Cliente</th>
                                <th>Zona</th>
                                <th>Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody id="historialEntregas">
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    No hay entregas completadas hoy
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel Lateral -->
    <div class="col-md-4">
        <!-- Estado del Repartidor -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-user-circle me-2"></i> Mi Estado
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-circle fa-3x text-success"></i>
                </div>
                <h5 class="text-success">Disponible</h5>
                <button class="btn btn-warning btn-sm mt-2" id="btnCambiarEstado">
                    <i class="fas fa-pause me-1"></i> Marcar No Disponible
                </button>
            </div>
        </div>

        <!-- Zonas Asignadas -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-map-marked-alt me-2"></i> Mis Zonas
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-map-pin me-2 text-primary"></i> Centro</span>
                        <span class="badge bg-primary">Activa</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-map-pin me-2 text-success"></i> Norte</span>
                        <span class="badge bg-success">Activa</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-map-pin me-2 text-info"></i> Este</span>
                        <span class="badge bg-info">Activa</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rendimiento del Mes -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-trophy me-2"></i> Rendimiento Mensual
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small>Entregas Completadas</small>
                        <small><strong>124/150</strong></small>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 83%" aria-valuenow="83" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small>Puntualidad</small>
                        <small><strong>95%</strong></small>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 95%" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small>Calificación</small>
                        <small><strong>4.8/5.0</strong></small>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 96%" aria-valuenow="96" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <hr>

                <div class="text-center">
                    <h4 class="text-primary">S/ 1,245.00</h4>
                    <small class="text-muted">Total del Mes</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$extraScripts = '
<script>
    // Actualizar estadísticas
    document.getElementById("entregasAsignadas").textContent = "5";
    document.getElementById("entregasCompletadas").textContent = "3";
    document.getElementById("entregasEnRuta").textContent = "1";
    document.getElementById("totalRecaudado").textContent = "S/ 285.00";
    
    // Cambiar estado del repartidor
    document.getElementById("btnCambiarEstado").addEventListener("click", function() {
        const button = this;
        const currentText = button.textContent.trim();
        
        if (currentText.includes("No Disponible")) {
            button.innerHTML = \'<i class="fas fa-play me-1"></i> Marcar Disponible\';
            button.classList.remove("btn-warning");
            button.classList.add("btn-success");
            
            button.parentElement.querySelector("h5").textContent = "No Disponible";
            button.parentElement.querySelector("h5").classList.remove("text-success");
            button.parentElement.querySelector("h5").classList.add("text-warning");
            
            button.parentElement.querySelector(".fa-circle").classList.remove("text-success");
            button.parentElement.querySelector(".fa-circle").classList.add("text-warning");
        } else {
            button.innerHTML = \'<i class="fas fa-pause me-1"></i> Marcar No Disponible\';
            button.classList.remove("btn-success");
            button.classList.add("btn-warning");
            
            button.parentElement.querySelector("h5").textContent = "Disponible";
            button.parentElement.querySelector("h5").classList.remove("text-warning");
            button.parentElement.querySelector("h5").classList.add("text-success");
            
            button.parentElement.querySelector(".fa-circle").classList.remove("text-warning");
            button.parentElement.querySelector(".fa-circle").classList.add("text-success");
        }
    });
    
    // TODO: Cargar entregas reales con AJAX
</script>
';

include __DIR__ . '/../layouts/footer.php';
?>