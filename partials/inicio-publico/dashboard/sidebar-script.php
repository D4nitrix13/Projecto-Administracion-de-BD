<style>
    .notif-sidebar-badge {
        margin-left: auto;
        background: #dc2626;
        color: #fff;
        font-size: 0.7rem;
        font-weight: 800;
        min-width: 20px;
        height: 20px;
        padding: 0 6px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const body = document.body;

        function actualizarBadgeNotificaciones() {
            fetch("notificacion_contador.php")
                .then(r => r.json())
                .then(data => {
                    const badge = document.getElementById("notifBadge");
                    if (!badge) return;
                    if (data.ok && data.sin_leer > 0) {
                        badge.textContent = data.sin_leer;
                        badge.style.display = "inline-flex";
                    } else {
                        badge.style.display = "none";
                    }
                })
                .catch(() => {});
        }

        actualizarBadgeNotificaciones();
        setInterval(actualizarBadgeNotificaciones, 30000);
        const sidebarToggle = document.getElementById("sidebarToggle");
        const sidebarToggleFloating = document.getElementById("sidebarToggleFloating");

        function isMobile() {
            return window.matchMedia("(max-width: 900px)").matches;
        }

        function toggleSidebar() {
            if (isMobile()) {
                body.classList.toggle("sidebar-open");
                return;
            }

            body.classList.toggle("sidebar-collapsed");

            localStorage.setItem(
                "sidebar-collapsed",
                body.classList.contains("sidebar-collapsed") ? "true" : "false"
            );
        }

        if (!isMobile() && localStorage.getItem("sidebar-collapsed") === "true") {
            body.classList.add("sidebar-collapsed");
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener("click", toggleSidebar);
        }

        if (sidebarToggleFloating) {
            sidebarToggleFloating.addEventListener("click", toggleSidebar);
        }

        document.querySelectorAll(".sidebar a").forEach(function(link) {
            link.addEventListener("click", function() {
                if (isMobile()) {
                    body.classList.remove("sidebar-open");
                }
            });
        });

        window.addEventListener("resize", function() {
            if (isMobile()) {
                body.classList.remove("sidebar-collapsed");
                return;
            }

            body.classList.remove("sidebar-open");

            if (localStorage.getItem("sidebar-collapsed") === "true") {
                body.classList.add("sidebar-collapsed");
            }
        });
    });
</script>