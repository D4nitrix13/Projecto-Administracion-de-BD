<section class="backup-panel">
    <div class="backup-panel-header">
        <span class="backup-panel-badge backup-badge-safe">Exportar</span>

        <h2>Generar respaldo</h2>

        <p>
            Cree un archivo .sql con el estado actual de la base de datos y agregue una descripción para identificarlo mejor.
        </p>
    </div>

    <form method="POST" class="backup-form">
        <input type="hidden" name="action" value="backup">

        <div class="form-group">
            <label class="label">Nombre del archivo</label>

            <input
                type="text"
                name="nombre_archivo"
                class="input"
                maxlength="80"
                placeholder="Ej. respaldo_cierre_mes">

            <p class="dashboard-muted backup-help">
                No incluya la extensión .sql. Se agregará automáticamente.
            </p>
        </div>

        <div class="form-group">
            <label class="label">Descripción del respaldo</label>

            <input
                type="text"
                name="mensaje"
                class="input"
                maxlength="255"
                placeholder="Ej. Respaldo antes de importar catálogo nuevo">

            <p class="dashboard-muted backup-help">
                Esta descripción aparecerá en la lista para identificar el motivo del respaldo.
            </p>
        </div>

        <button type="submit" class="backup-btn backup-btn-primary">
            Generar respaldo
        </button>
    </form>
</section>