# README - DIAGRAMAS DEL SISTEMA

Este directorio contiene todos los diagramas técnicos del Sistema de Gestión Integral para Cevichería "Napanchita".

## 📁 Contenido

### 1. **CASOS_DE_USO.md**

- 36 casos de uso detallados
- Descripción completa de funcionalidades
- Matriz de casos de uso por actor
- Código PlantUML para generar diagrama visual
- Actores: Admin, Mesero, Repartidor, Cliente Externo

### 2. **MODELO_ENTIDAD_RELACION.md**

- Diseño completo de la base de datos
- 16 entidades con atributos detallados
- Relaciones y cardinalidades
- Diagrama en formato Mermaid
- Índices y optimizaciones
- Reglas de integridad
- Normalización 3FN

### 3. **ARQUITECTURA_SISTEMA.md**

- Arquitectura MVC completa
- Patrón Front Controller
- Diagramas de capas
- Flujo de ejecución
- Componentes y tecnologías
- Decisiones arquitectónicas
- Escalabilidad y rendimiento

### 4. **DIAGRAMA_CLASES.md**

- Diagrama UML de clases completo
- Modelos, Controladores y Vistas
- Relaciones de herencia y asociación
- Patrones de diseño aplicados
- Diagrama de secuencia
- Diagrama de componentes
- Responsabilidades por capa

## 🎨 Herramientas para Visualizar

### PlantUML

Los diagramas de casos de uso y clases están en formato PlantUML.

**Opciones para renderizar:**

1. **Online:**

   - https://plantuml.com/
   - Copiar código y generar imagen

2. **VS Code:**

   - Instalar extensión: "PlantUML"
   - Abrir archivo .md
   - Ver preview del diagrama

3. **IntelliJ IDEA:**
   - Plugin PlantUML integration
   - Vista previa automática

### Mermaid

El Modelo Entidad-Relación está en formato Mermaid.

**Opciones:**

1. **Online:**

   - https://mermaid.live/
   - Copiar código y visualizar

2. **VS Code:**

   - Extensión: "Markdown Preview Mermaid Support"
   - Preview automático en .md files

3. **GitHub:**
   - Renderiza automáticamente en README

## 📊 Exportar Diagramas

### Para la Tesis (Documentos Word/PDF):

1. **Generar imagen desde PlantUML:**

   ```bash
   java -jar plantuml.jar diagrama.puml
   ```

2. **Screenshot desde herramientas online**

3. **Draw.io:**
   - Recrear manualmente para mayor control
   - Exportar como PNG, SVG o PDF

### Formatos Recomendados:

- **Presentaciones:** PNG (300 DPI)
- **Documentos:** SVG (vectorial, escalable)
- **Impresión:** PDF

## 📝 Uso en la Tesis

### Capítulo III - Análisis del Sistema

**Incluir:**

- ✅ Diagrama de Casos de Uso
- ✅ Descripción detallada de cada CU
- ✅ Matriz de Casos de Uso por Actor

**Ubicación:** Sección 3.2 - Análisis de Requisitos

---

### Capítulo III - Diseño de la Base de Datos

**Incluir:**

- ✅ Modelo Entidad-Relación
- ✅ Diccionario de datos
- ✅ Normalización (1FN → 2FN → 3FN)

**Ubicación:** Sección 3.3 - Diseño de Base de Datos

---

### Capítulo IV - Diseño del Sistema

**Incluir:**

- ✅ Arquitectura del Sistema (3 capas)
- ✅ Diagrama de Clases
- ✅ Diagrama de Componentes
- ✅ Diagrama de Secuencia (operaciones críticas)

**Ubicación:** Sección 4.1 - Arquitectura del Software

---

## 🔄 Actualización de Diagramas

Si necesitas actualizar algún diagrama:

1. **Editar archivo .md correspondiente**
2. **Actualizar código PlantUML/Mermaid**
3. **Regenerar imagen**
4. **Actualizar versión en el footer**

## 📞 Notas

- Todos los diagramas están sincronizados con el `schema_completo.sql`
- Los roles de usuario son: **admin, mesero, repartidor**
- Los clientes NO tienen acceso al sistema
- La fecha base de elaboración: 16/11/2025

## ✅ Checklist para Tesis

- [ ] Imprimir diagramas en alta resolución
- [ ] Incluir leyenda en cada diagrama
- [ ] Numerar figuras correctamente
- [ ] Referenciar en el texto
- [ ] Agregar pie de página con fuente: "Elaboración propia"
- [ ] Verificar que sean legibles en blanco y negro

---

**Última actualización:** 16/11/2025  
**Versión:** 1.0  
**Autor:** [Tu nombre]
