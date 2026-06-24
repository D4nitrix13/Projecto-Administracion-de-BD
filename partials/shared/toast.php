<style>
    .app-toast {
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 10px;
        pointer-events: none;
    }

    .app-toast-item {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 300px;
        max-width: 480px;
        padding: 14px 18px;
        border-radius: 12px;
        color: #ffffff;
        font-size: 0.92rem;
        font-weight: 700;
        line-height: 1.4;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.18);
        transform: translateX(120%);
        opacity: 0;
        transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.35s ease;
        pointer-events: auto;
        cursor: pointer;
    }

    .app-toast-item.show {
        transform: translateX(0);
        opacity: 1;
    }

    .app-toast-item.hide {
        transform: translateX(120%);
        opacity: 0;
    }

    .app-toast-success {
        background: #16a34a;
    }

    .app-toast-error {
        background: #dc2626;
    }

    .app-toast-warning {
        background: #d97706;
    }

    .app-toast-info {
        background: #2563eb;
    }

    .app-toast-icon {
        flex-shrink: 0;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .app-toast-close {
        flex-shrink: 0;
        margin-left: auto;
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.8);
        font-size: 1.2rem;
        cursor: pointer;
        padding: 0;
        line-height: 1;
    }

    .app-toast-close:hover {
        color: #ffffff;
    }

    @media (max-width: 640px) {
        .app-toast {
            top: 12px;
            right: 12px;
            left: 12px;
        }

        .app-toast-item {
            min-width: unset;
            max-width: unset;
        }
    }
</style>

<div class="app-toast" id="app-toast"></div>

<script>
    (function () {
        const container = document.getElementById("app-toast");
        if (!container) return;

        const ICONS = {
            success: "\u2713",
            error: "\u2717",
            warning: "\u26A0",
            info: "\u2139"
        };

        window.showToast = function (message, type = "success", duration = 4000) {
            const item = document.createElement("div");
            item.className = `app-toast-item app-toast-${type}`;

            item.innerHTML = `
                <span class="app-toast-icon">${ICONS[type] || ICONS.info}</span>
                <span>${escapeHtml(message)}</span>
                <button class="app-toast-close" aria-label="Cerrar">&times;</button>
            `;

            container.appendChild(item);

            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    item.classList.add("show");
                });
            });

            const dismiss = () => {
                item.classList.remove("show");
                item.classList.add("hide");
                setTimeout(() => item.remove(), 350);
            };

            item.querySelector(".app-toast-close").addEventListener("click", dismiss);
            item.addEventListener("click", dismiss);

            if (duration > 0) {
                setTimeout(dismiss, duration);
            }

            return { dismiss };
        };

        window.confirmAction = function (message, onConfirm, onCancel) {
            const overlay = document.createElement("div");
            overlay.style.cssText = `
                position: fixed; inset: 0; z-index: 10000;
                background: rgba(0, 0, 0, 0.4);
                display: flex; align-items: center; justify-content: center;
                opacity: 0; transition: opacity 0.2s ease;
            `;

            const dialog = document.createElement("div");
            dialog.style.cssText = `
                background: #ffffff; border-radius: 16px; padding: 28px;
                max-width: 420px; width: 90%; box-shadow: 0 24px 48px rgba(0,0,0,0.18);
                transform: scale(0.9); transition: transform 0.2s ease;
            `;

            dialog.innerHTML = `
                <p style="margin: 0 0 20px; color: #111827; font-size: 1rem; font-weight: 700; line-height: 1.5;">
                    ${escapeHtml(message)}
                </p>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button class="confirm-cancel" style="
                        padding: 10px 18px; border-radius: 10px; border: 1px solid #cbd5e1;
                        background: #ffffff; color: #374151; font-weight: 800; font-size: 0.9rem;
                        cursor: pointer; transition: background 0.15s;
                    ">Cancelar</button>
                    <button class="confirm-ok" style="
                        padding: 10px 18px; border-radius: 10px; border: none;
                        background: #dc2626; color: #ffffff; font-weight: 800; font-size: 0.9rem;
                        cursor: pointer; transition: background 0.15s;
                    ">Confirmar</button>
                </div>
            `;

            overlay.appendChild(dialog);
            document.body.appendChild(overlay);

            requestAnimationFrame(() => {
                overlay.style.opacity = "1";
                dialog.style.transform = "scale(1)";
            });

            const close = () => {
                overlay.style.opacity = "0";
                dialog.style.transform = "scale(0.9)";
                setTimeout(() => overlay.remove(), 200);
            };

            dialog.querySelector(".confirm-cancel").addEventListener("click", () => {
                close();
                if (onCancel) onCancel();
            });

            dialog.querySelector(".confirm-ok").addEventListener("click", () => {
                close();
                if (onConfirm) onConfirm();
            });

            overlay.addEventListener("click", (e) => {
                if (e.target === overlay) {
                    close();
                    if (onCancel) onCancel();
                }
            });
        };

        function escapeHtml(text) {
            const div = document.createElement("div");
            div.textContent = text;
            return div.innerHTML;
        }
    })();
</script>
