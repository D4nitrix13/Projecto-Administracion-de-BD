<script>
    document.addEventListener("DOMContentLoaded", function() {
        const rolSelect = document.getElementById("id_rol");
        const seccionInfo = document.getElementById("seccion_info");

        if (!rolSelect || !seccionInfo) {
            return;
        }

        function actualizarSeccion() {
            const valor = rolSelect.value;

            if (valor === "1") {
                seccionInfo.value = "Todas las secciones";
            } else if (valor === "2" || valor === "3") {
                seccionInfo.value = "Kitsune";
            } else {
                seccionInfo.value = "Seleccione un rol";
            }
        }

        rolSelect.addEventListener("change", actualizarSeccion);
        actualizarSeccion();
    });
</script>