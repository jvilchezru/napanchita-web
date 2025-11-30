# Directorio de Uploads

Este directorio almacena todos los archivos subidos por los usuarios del sistema.

## Estructura

- `/platos/` - Imágenes de platos
- `/combos/` - Imágenes de combos
- `/usuarios/` - Avatares de usuarios
- `/clientes/` - Documentos de clientes (opcional)

## Permisos

Asegúrate de que este directorio tenga permisos de escritura:

```bash
chmod 755 public/uploads
```

En XAMPP Windows, normalmente no se requiere cambiar permisos.

## Seguridad

- Solo se permiten archivos de imagen: JPG, JPEG, PNG, GIF, WEBP
- Tamaño máximo: 5MB por archivo
- Los nombres de archivo se renombran automáticamente para evitar colisiones
- Se valida el tipo MIME real del archivo

## Límites

- Máximo 10 imágenes por producto/combo
- Compresión automática de imágenes grandes
- Generación de thumbnails para optimización
