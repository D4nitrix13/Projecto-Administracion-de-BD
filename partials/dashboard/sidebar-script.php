<script>
    document.addEventListener("DOMContentLoaded", function() {
        const button = document.getElementById("sidebarToggle");
        const body = document.body;

        if (!button) {
            return;
        }

        const savedState = localStorage.getItem("sidebar-collapsed");

        if (savedState === "true") {
            body.classList.add("sidebar-collapsed");
        }

        button.addEventListener("click", function() {
            const isMobile = window.matchMedia("(max-width: 900px)").matches;

            if (isMobile) {
                body.classList.toggle("sidebar-open");
                return;
            }

            body.classList.toggle("sidebar-collapsed");

            localStorage.setItem(
                "sidebar-collapsed",
                body.classList.contains("sidebar-collapsed") ? "true" : "false"
            );
        });
    });
</script>