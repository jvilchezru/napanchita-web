<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
            min-height: 100vh;
        }
        .navbar {
            background: linear-gradient(135deg, #00838f 0%, #00acc1 100%) !important;
            box-shadow: 0 4px 12px rgba(0,131,143,0.3);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            animation: fadeInUp 0.6s ease;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .card-header {
            background: linear-gradient(135deg, #00838f 0%, #00acc1 100%);
            color: white;
            padding: 1.2rem;
            font-weight: 600;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            margin: 0 auto 1rem;
            box-shadow: 0 10px 30px rgba(102,126,234,0.4);
        }
        .form-label {
            font-weight: 500;
            color: #00838f;
            margin-bottom: 0.5rem;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e0f7fa;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #00acc1;
            box-shadow: 0 0 0 0.2rem rgba(0,172,193,0.25);
        }
        .btn-save {
            background: linear-gradient(135deg, #00838f 0%, #00acc1 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,131,143,0.4);
            color: white;
        }
        .direccion-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 0.5rem;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }
        .direccion-item:hover {
            border-color: #00acc1;
            transform: translateX(5px);
        }
        .direccion-item.principal {
            background: linear-gradient(135deg, rgba(0,131,143,0.1) 0%, rgba(0,172,193,0.1) 100%);
            border-color: #00acc1;
        }
        .btn-direccion {
            padding: 0.25rem 0.75rem;
            border-radius: 8px;
            font-size: 0.85rem;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-editar {
            background: #00acc1;
            color: white;
        }
        .btn-eliminar {
            background: #f44336;
            color: white;
        }
        .btn-direccion:hover {
            transform: scale(1.05);
        }
        .btn-add-direccion {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
        }
        .stats-card {
            text-align: center;
            padding: 1.5rem;
            background: linear-gradient(135deg, #00838f 0%, #00acc1 100%);
            color: white;
            border-radius: 15px;
            margin-bottom: 1rem;
        }
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
        }
        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        /* Rating Selector */
        .rating-selector {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 5px;
            font-size: 2rem;
        }
        
        .rating-selector input[type="radio"] {
            display: none;
        }
        
        .rating-selector label {
            cursor: pointer;
            color: #ddd;
            transition: color 0.2s;
        }
        
        .rating-selector label:hover,
        .rating-selector label:hover ~ label,
        .rating-selector input[type="radio"]:checked ~ label {
            color: #ffc107;
        }
        
        .rating-selector label:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>index.php?action=portal">
                <img src="<?php echo BASE_URL; ?>public/images/logo.png" alt="<?php echo APP_NAME; ?>" style="height: 50px; width: auto;">
            </a>
            <div>
                <a href="<?php echo BASE_URL; ?>index.php?action=portal" class="btn btn-light me-2">
                    <i class="fas fa-home me-2"></i>Inicio
                </a>
                <a href="<?php echo BASE_URL; ?>index.php?action=portal&subaction=mis-pedidos" class="btn btn-light me-2">
                    <i class="fas fa-receipt me-2"></i>Mis Pedidos
                </a>
                <a href="<?php echo BASE_URL; ?>index.php?controller=ClienteAuth&action=logout" class="btn btn-outline-light">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </nav>
    
    <div class="container py-5">
        <div class="row">
            <!-- Columna Izquierda: Info del Usuario -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="profile-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h4><?php echo htmlspecialchars($cliente['nombre']); ?></h4>
                        <p class="text-muted"><?php echo htmlspecialchars($cliente['email']); ?></p>
                        <hr>
                        <div class="text-start">
                            <div class="mb-2">
                                <i class="fas fa-phone me-2" style="color: #00acc1;"></i>
                                <?php echo htmlspecialchars($cliente['telefono']); ?>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-calendar me-2" style="color: #00acc1;"></i>
                                Miembro desde <?php echo date('M Y', strtotime($cliente['created_at'] ?? 'now')); ?>
                            </div>
                            <?php if (isset($cliente['ultimo_acceso'])): ?>
                                <div class="mb-2">
                                    <i class="fas fa-clock me-2" style="color: #00acc1;"></i>
                                    Último acceso: <?php echo date('d/m/Y H:i', strtotime($cliente['ultimo_acceso'])); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Estadísticas -->
                <?php if (isset($estadisticas)): ?>
                    <div class="stats-card">
                        <div class="stats-number"><?php echo $estadisticas['total_pedidos'] ?? 0; ?></div>
                        <div class="stats-label">Pedidos Realizados</div>
                    </div>
                    <div class="stats-card">
                        <div class="stats-number">S/ <?php echo number_format($estadisticas['total_gastado'] ?? 0, 2); ?></div>
                        <div class="stats-label">Total Gastado</div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Columna Derecha: Formularios -->
            <div class="col-lg-8">
                <!-- Datos Personales -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Datos Personales</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo BASE_URL; ?>index.php?action=portal&subaction=actualizar-perfil" method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nombre Completo *</label>
                                    <input type="text" name="nombre" class="form-control" 
                                           value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Teléfono *</label>
                                    <input type="tel" name="telefono" class="form-control" 
                                           value="<?php echo htmlspecialchars($cliente['telefono']); ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?php echo htmlspecialchars($cliente['email']); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-save">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Cambiar Contraseña -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Cambiar Contraseña</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo BASE_URL; ?>index.php?action=portal&subaction=cambiar-password" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Contraseña Actual *</label>
                                <input type="password" name="password_actual" class="form-control" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nueva Contraseña *</label>
                                    <input type="password" name="password_nueva" id="password_nueva" 
                                           class="form-control" required minlength="6">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Confirmar Contraseña *</label>
                                    <input type="password" name="password_confirmar" class="form-control" 
                                           required minlength="6">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-save">
                                <i class="fas fa-key me-2"></i>Cambiar Contraseña
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Direcciones -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Mis Direcciones</h5>
                        <button type="button" class="btn btn-add-direccion" data-bs-toggle="modal" data-bs-target="#modalDireccion">
                            <i class="fas fa-plus me-2"></i>Agregar Dirección
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if (empty($direcciones)): ?>
                            <p class="text-muted text-center py-3">
                                <i class="fas fa-map-marker-alt fa-3x mb-3" style="color: #00acc1;"></i>
                                <br>No tienes direcciones guardadas
                            </p>
                        <?php else: ?>
                            <?php foreach ($direcciones as $direccion): ?>
                                <div class="direccion-item <?php echo $direccion['principal'] ? 'principal' : ''; ?>">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>
                                                <i class="fas fa-map-marker-alt me-2" style="color: #00acc1;"></i>
                                                <?php echo htmlspecialchars($direccion['direccion']); ?>
                                            </strong>
                                            <?php if ($direccion['principal']): ?>
                                                <span class="badge bg-warning text-dark ms-2">Principal</span>
                                            <?php endif; ?>
                                            <?php if (!empty($direccion['referencia'])): ?>
                                                <div class="text-muted small mt-1">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    <?php echo htmlspecialchars($direccion['referencia']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <?php if (!$direccion['principal']): ?>
                                                <button type="button" class="btn btn-direccion btn-editar me-1" 
                                                        onclick="marcarPrincipal(<?php echo $direccion['id']; ?>)">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-direccion btn-eliminar" 
                                                    onclick="eliminarDireccion(<?php echo $direccion['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Mis Reseñas -->
            <div class="col-lg-12 mt-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-star me-2"></i>Mi Reseña
                    </div>
                    <div class="card-body">
                        <?php if (isset($miResena) && $miResena): ?>
                            <!-- Reseña Existente -->
                            <div class="alert alert-info">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="mb-2">
                                            <strong>Tu calificación:</strong>
                                            <span class="ms-2" style="color: #ffc107;">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star<?php echo $i <= $miResena['calificacion'] ? '' : '-o'; ?>"></i>
                                                <?php endfor; ?>
                                            </span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Tu comentario:</strong>
                                            <p class="mb-0 mt-1"><?php echo nl2br(htmlspecialchars($miResena['comentario'])); ?></p>
                                        </div>
                                        <small class="text-muted">
                                            Publicado el: <?php echo date('d/m/Y', strtotime($miResena['fecha_creacion'])); ?>
                                        </small>
                                        <?php if ($miResena['activo'] == 0): ?>
                                            <span class="badge bg-warning ms-2">Pendiente de Aprobación</span>
                                        <?php endif; ?>
                                        <?php if ($miResena['destacado']): ?>
                                            <span class="badge bg-success ms-2">
                                                <i class="fas fa-trophy"></i> Destacada
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted small">
                                <i class="fas fa-info-circle me-1"></i>
                                Ya has dejado una reseña. Gracias por tu opinión.
                            </p>
                        <?php else: ?>
                            <!-- Formulario Nueva Reseña -->
                            <h5 class="mb-3">Comparte tu experiencia</h5>
                            <p class="text-muted mb-3">Tu opinión es muy importante para nosotros y nos ayuda a mejorar cada día.</p>
                            
                            <form action="<?php echo BASE_URL; ?>index.php?action=portal&subaction=crear-resena" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Calificación *</label>
                                    <div class="rating-selector">
                                        <input type="radio" name="calificacion" value="5" id="star5" required>
                                        <label for="star5"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="calificacion" value="4" id="star4">
                                        <label for="star4"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="calificacion" value="3" id="star3">
                                        <label for="star3"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="calificacion" value="2" id="star2">
                                        <label for="star2"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="calificacion" value="1" id="star1">
                                        <label for="star1"><i class="fas fa-star"></i></label>
                                    </div>
                                    <small class="text-muted">Selecciona de 1 a 5 estrellas</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Tu Comentario *</label>
                                    <textarea name="comentario" class="form-control" rows="4" 
                                              placeholder="Cuéntanos sobre tu experiencia con nuestros productos y servicio..." 
                                              required maxlength="500"></textarea>
                                    <small class="text-muted">Máximo 500 caracteres</small>
                                </div>
                                
                                <div class="alert alert-light">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Tu reseña será revisada por nuestro equipo antes de ser publicada.
                                </div>
                                
                                <button type="submit" class="btn btn-save">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar Reseña
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Agregar Dirección -->
    <div class="modal fade" id="modalDireccion" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header" style="background: linear-gradient(135deg, #00838f 0%, #00acc1 100%); color: white;">
                    <h5 class="modal-title"><i class="fas fa-map-marker-alt me-2"></i>Agregar Nueva Dirección</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo BASE_URL; ?>index.php?action=portal&subaction=agregar-direccion" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Dirección Completa *</label>
                            <textarea name="direccion" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Referencia</label>
                            <input type="text" name="referencia" class="form-control" 
                                   placeholder="Ej: Casa verde, al costado de...">
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="principal" class="form-check-input" id="checkPrincipal" value="1">
                            <label class="form-check-label" for="checkPrincipal">
                                Establecer como dirección principal
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-save">
                            <i class="fas fa-save me-2"></i>Guardar Dirección
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function marcarPrincipal(id) {
            if (confirm('¿Marcar esta dirección como principal?')) {
                window.location.href = `<?php echo BASE_URL; ?>index.php?action=portal&subaction=marcar-principal&id=${id}`;
            }
        }
        
        function eliminarDireccion(id) {
            if (confirm('¿Estás seguro de eliminar esta dirección?')) {
                window.location.href = `<?php echo BASE_URL; ?>index.php?action=portal&subaction=eliminar-direccion&id=${id}`;
            }
        }
        
        // Validar que las contraseñas coincidan
        document.querySelector('form[action*="cambiar-password"]').addEventListener('submit', function(e) {
            const nueva = document.getElementById('password_nueva').value;
            const confirmar = document.querySelector('input[name="password_confirmar"]').value;
            
            if (nueva !== confirmar) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                return false;
            }
        });
    </script>
</body>
</html>
