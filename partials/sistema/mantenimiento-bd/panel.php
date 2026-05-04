<section class="mantenimiento-layout">

    <article class="mantenimiento-card">
        <div class="mantenimiento-card-header">
            <div>
                <span class="mantenimiento-badge mantenimiento-badge-info">
                    Ejecución
                </span>

                <h2>Acciones de mantenimiento</h2>

                <p>
                    Ejecute manualmente las tareas principales del plan de mantenimiento de PostgreSQL.
                </p>
            </div>
        </div>

        <div class="mantenimiento-actions-grid">
            <form method="POST" class="mantenimiento-action-card">
                <input type="hidden" name="action" value="backup_full">

                <div>
                    <span>Backup full</span>
                    <h3>Respaldo completo</h3>
                    <p>Genera una copia completa de toda la base de datos en backups/full.</p>
                </div>

                <button
                    type="submit"
                    class="mantenimiento-primary-button"
                    onclick="return confirm('¿Desea ejecutar un backup completo ahora?');">
                    Ejecutar full
                </button>
            </form>

            <form method="POST" class="mantenimiento-action-card">
                <input type="hidden" name="action" value="backup_diff">

                <div>
                    <span>Backup rápido</span>
                    <h3>Datos importantes</h3>
                    <p>Respalda tablas críticas del sistema en backups/diff.</p>
                </div>

                <button
                    type="submit"
                    class="mantenimiento-primary-button"
                    onclick="return confirm('¿Desea ejecutar el respaldo rápido ahora?');">
                    Ejecutar rápido
                </button>
            </form>

            <form method="POST" class="mantenimiento-action-card">
                <input type="hidden" name="action" value="backup_logs">

                <div>
                    <span>Logs</span>
                    <h3>Copia de registros</h3>
                    <p>Copia o comprime registros del sistema hacia backups/logs.</p>
                </div>

                <button
                    type="submit"
                    class="mantenimiento-primary-button"
                    onclick="return confirm('¿Desea ejecutar la copia de logs ahora?');">
                    Copiar logs
                </button>
            </form>

            <form method="POST" class="mantenimiento-action-card mantenimiento-action-card-blue">
                <input type="hidden" name="action" value="mantenimiento_completo">

                <div>
                    <span>Plan completo</span>
                    <h3>Mantenimiento general</h3>
                    <p>Ejecuta backup completo, respaldo rápido y copia de logs.</p>
                </div>

                <button
                    type="submit"
                    class="mantenimiento-primary-button"
                    onclick="return confirm('¿Desea ejecutar el plan completo de mantenimiento?');">
                    Ejecutar todo
                </button>
            </form>
        </div>
    </article>

    <aside class="mantenimiento-side">

        <article class="mantenimiento-card">
            <div class="mantenimiento-card-header">
                <div>
                    <span class="mantenimiento-badge mantenimiento-badge-info">
                        Estado
                    </span>

                    <h2>Últimos archivos</h2>

                    <p>
                        Resumen de los archivos más recientes generados por cada proceso.
                    </p>
                </div>
            </div>

            <div class="mantenimiento-info-list">
                <div>
                    <span>Último backup completo</span>
                    <strong><?= htmlspecialchars($resumenFull["ultimo_archivo"]) ?></strong>
                    <small><?= htmlspecialchars($resumenFull["ultima_fecha"]) ?></small>
                </div>

                <div>
                    <span>Último backup rápido</span>
                    <strong><?= htmlspecialchars($resumenDiff["ultimo_archivo"]) ?></strong>
                    <small><?= htmlspecialchars($resumenDiff["ultima_fecha"]) ?></small>
                </div>

                <div>
                    <span>Último log de backup</span>
                    <strong><?= htmlspecialchars($resumenBackupLogs["ultimo_archivo"]) ?></strong>
                    <small><?= htmlspecialchars($resumenBackupLogs["ultima_fecha"]) ?></small>
                </div>

                <div>
                    <span>Último WAL</span>
                    <strong><?= htmlspecialchars($resumenWal["ultimo_archivo"]) ?></strong>
                    <small><?= htmlspecialchars($resumenWal["ultima_fecha"]) ?></small>
                </div>
            </div>
        </article>

        <article class="mantenimiento-card mantenimiento-card-warning">
            <div class="mantenimiento-card-header">
                <div>
                    <span class="mantenimiento-badge mantenimiento-badge-warning">
                        Frecuencia sugerida
                    </span>

                    <h2>Plan recomendado</h2>
                </div>
            </div>

            <div class="mantenimiento-frequency-list">
                <div>
                    <span>Backup full</span>
                    <strong>Semanal</strong>
                </div>

                <div>
                    <span>Backup rápido</span>
                    <strong>Diario</strong>
                </div>

                <div>
                    <span>Copia de logs</span>
                    <strong>Diaria</strong>
                </div>

                <div>
                    <span>Prueba de restauración</span>
                    <strong>Mensual</strong>
                </div>

                <div>
                    <span>Archivado WAL</span>
                    <strong>Continuo</strong>
                </div>
            </div>
        </article>

    </aside>

</section>