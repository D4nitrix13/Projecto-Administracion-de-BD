<script>
    document.addEventListener("DOMContentLoaded", function() {
        const body = document.body;
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