            </main>

            <!-- Footer -->
            <footer class="bg-white border-top py-3 px-4 text-center mt-auto">
                <small class="text-muted">
                    © <?php echo date('Y'); ?> <?php echo APP_NAME; ?> v<?php echo APP_VERSION; ?> - Todos los derechos reservados
                    <!-- | <a href="#" class="text-decoration-none">Soporte</a>
                    | <a href="#" class="text-decoration-none">Documentación</a> -->
                </small>
            </footer>
            </div>
            </div>

            <!-- jQuery -->
            <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

            <!-- Bootstrap 5 JS -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

            <!-- DataTables -->
            <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

            <!-- SweetAlert2 -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <!-- Custom JS -->
            <script src="<?php echo BASE_URL; ?>public/js/main.js"></script>

            <script>
                // Sidebar Toggle
                document.getElementById('sidebarToggle').addEventListener('click', function() {
                    const sidebar = document.querySelector('.sidebar');
                    const mainContent = document.querySelector('.main-content');
                    
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                    
                    // Guardar preferencia en localStorage
                    const isCollapsed = sidebar.classList.contains('collapsed');
                    localStorage.setItem('sidebarCollapsed', isCollapsed);
                });
                
                // Restaurar estado del sidebar al cargar
                document.addEventListener('DOMContentLoaded', function() {
                    const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                    if (sidebarCollapsed) {
                        document.querySelector('.sidebar').classList.add('collapsed');
                        document.querySelector('.main-content').classList.add('expanded');
                    }
                });

                // Mensaje de éxito/error
                <?php if (isset($_SESSION['success'])): ?>
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: '<?php echo $_SESSION['success']; ?>',
                        timer: 3000,
                        showConfirmButton: false
                    });
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '<?php echo $_SESSION['error']; ?>',
                        timer: 3000,
                        showConfirmButton: false
                    });
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                // Confirmación de eliminación
                document.querySelectorAll('.btn-delete').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = this.getAttribute('href');

                        Swal.fire({
                            title: '¿Está seguro?',
                            text: "Esta acción no se puede revertir",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = url;
                            }
                        });
                    });
                });

                // Auto-hide alerts
                setTimeout(function() {
                    document.querySelectorAll('.alert').forEach(alert => {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    });
                }, 5000);
            </script>

            <?php if (isset($extraScripts)): ?>
                <?php echo $extraScripts; ?>
            <?php endif; ?>
            </body>

            </html>