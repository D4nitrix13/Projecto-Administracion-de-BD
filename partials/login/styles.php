<style>
    :root {
        --login-bg: #f3f5f9;
        --login-card-bg: #ffffff;
        --login-primary: #111827;
        --login-primary-hover: #1f2937;
        --login-text: #111827;
        --login-muted: #6b7280;
        --login-border: #e5e7eb;
        --login-danger-bg: #fee2e2;
        --login-danger-text: #991b1b;
        --login-danger-border: #fecaca;
        --login-success-bg: #dcfce7;
        --login-success-text: #166534;
        --login-success-border: #bbf7d0;
    }

    * {
        box-sizing: border-box;
    }

    body.login-page {
        margin: 0;
        min-height: 100vh;
        background:
            radial-gradient(circle at top left, rgba(255, 47, 99, 0.12), transparent 32%),
            radial-gradient(circle at bottom right, rgba(37, 99, 235, 0.10), transparent 30%),
            var(--login-bg);
        color: var(--login-text);
        font-family: Arial, sans-serif;
    }

    .login-wrapper {
        min-height: 100vh;
        display: grid;
        place-items: center;
        padding: 28px;
    }

    .login-card {
        width: min(100%, 430px);
        background: var(--login-card-bg);
        border-radius: 22px;
        padding: 34px;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.16);
        border: 1px solid rgba(229, 231, 235, 0.9);
    }

    .login-brand {
        width: 58px;
        height: 58px;
        margin: 0 auto 18px;
        border-radius: 18px;
        overflow: hidden;
        background: #ffffff;
        display: grid;
        place-items: center;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
    }

    .login-brand img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .login-title {
        margin: 0;
        color: var(--login-text);
        font-size: 1.7rem;
        font-weight: 900;
        text-align: center;
        letter-spacing: -0.03em;
    }

    .login-subtitle {
        margin: 10px 0 22px;
        color: var(--login-muted);
        text-align: center;
        line-height: 1.5;
    }

    .alert {
        padding: 13px 15px;
        border-radius: 12px;
        margin-bottom: 16px;
        font-weight: 700;
        font-size: 0.92rem;
    }

    .alert-danger {
        background: var(--login-danger-bg);
        color: var(--login-danger-text);
        border: 1px solid var(--login-danger-border);
    }

    .alert-success {
        background: var(--login-success-bg);
        color: var(--login-success-text);
        border: 1px solid var(--login-success-border);
    }

    .login-form {
        display: grid;
        gap: 15px;
    }

    .form-group {
        display: grid;
        gap: 7px;
    }

    .label {
        color: var(--login-text);
        font-size: 0.9rem;
        font-weight: 800;
    }

    .input {
        width: 100%;
        min-height: 44px;
        padding: 0 13px;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        background: #ffffff;
        color: var(--login-text);
        font-size: 0.95rem;
        outline: none;
    }

    .input:focus {
        border-color: var(--login-primary);
        box-shadow: 0 0 0 3px rgba(17, 24, 39, 0.10);
    }

    .btn-primary {
        width: 100%;
        min-height: 44px;
        margin-top: 4px;
        border: none;
        border-radius: 12px;
        background: var(--login-primary);
        color: #ffffff;
        font-weight: 900;
        font-size: 0.95rem;
        cursor: pointer;
        transition: background 0.2s ease, transform 0.2s ease;
    }

    .btn-primary:hover {
        background: var(--login-primary-hover);
        transform: translateY(-1px);
    }

    .back-link {
        display: block;
        margin-top: 22px;
        color: var(--login-muted);
        text-align: center;
        text-decoration: none;
        font-weight: 700;
    }

    .back-link:hover {
        color: var(--login-primary);
        text-decoration: underline;
    }

    @media (max-width: 520px) {
        .login-wrapper {
            padding: 18px;
        }

        .login-card {
            padding: 26px 20px;
            border-radius: 18px;
        }

        .login-title {
            font-size: 1.45rem;
        }
    }
</style>