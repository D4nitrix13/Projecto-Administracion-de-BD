# Configuración SMTP — Panda Estampados / Kitsune

Guía completa del sistema de envío de correos electrónicos implementado en el proyecto.

---

## Arquitectura

```bash
┌─────────────────┐     ┌───────────────┐     ┌─────────────────┐
│  PHP App (:80)  │────▶│  MailPit      │────▶│  Bandeja web    │
│  PHPMailer      │     │  SMTP (:1025) │     │  (:8025)        │
└─────────────────┘     └───────────────┘     └─────────────────┘
```

- **PHPMailer 7.1.1** — Librería de envío de correos (instalada vía Composer)
- **MailPit** — Servidor SMTP de desarrollo que captura todos los correos sin enviarlos realmente
- **EmailService** — Servicio propio que encapsula toda la lógica de envío

---

## Variables de entorno (.env)

```env
# Host del servidor SMTP
SMTP_HOST=mailpit

# Puerto SMTP (1025 = MailPit, 587 = Gmail TLS, 465 = SSL)
SMTP_PORT=1025

# Autenticación (vacío = sin auth, como MailPit)
SMTP_USER=
SMTP_PASS=

# Dirección del remitente
SMTP_FROM=noreply@pandakitsune.com

# Nombre visible del remitente
SMTP_FROM_NAME=Panda Estampados y Kitsune
```

### Descripción de cada variable

| Variable         | Descripción                 | Ejemplo                                           |
| ---------------- | --------------------------- | ------------------------------------------------- |
| `SMTP_HOST`      | Dirección del servidor SMTP | `mailpit`, `smtp.gmail.com`, `smtp.office365.com` |
| `SMTP_PORT`      | Puerto del servidor         | `1025` (MailPit), `587` (TLS), `465` (SSL)        |
| `SMTP_USER`      | Usuario para autenticación  | `tu@gmail.com` (vacío si no requiere auth)        |
| `SMTP_PASS`      | Contraseña o App Password   | `abcd efgh ijkl mnop` (vacío si no requiere auth) |
| `SMTP_FROM`      | Email del remitente         | `noreply@pandakitsune.com`                        |
| `SMTP_FROM_NAME` | Nombre del remitente        | `Panda Estampados y Kitsune`                      |

---

## Docker: MailPit

MailPit es un servidor SMTP de prueba que no envía correos reales. Captura todo en una interfaz web.

### docker-compose.yml

```yaml
mailpit:
  image: axllent/mailpit:latest
  container_name: pandas_mailpit
  ports:
    - "8025:8025"   # Interfaz web
    - "1025:1025"   # SMTP (la app se conecta aquí)
  environment:
    TZ: America/Managua
  restart: unless-stopped
```

### Acceso

| Servicio                  | URL                     |
| ------------------------- | ----------------------- |
| Bandeja de entrada web    | `http://localhost:8025` |
| Puerto SMTP (para la app) | localhost:1025          |

### Cómo verificar que funciona

1. Abrí `http://localhost:8025` en el navegador
2. Realizá una acción que envíe correo (ej: "Olvidé mi contraseña")
3. El correo aparecerá en la bandeja de entrada de MailPit
4. Hacé clic para ver el contenido HTML del correo

---

## Flujo de envío de correos

### 1. Recuperación de contraseña

```bash
Usuario hace clic en "Olvidé mi contraseña"
    ↓
forgot_password.php → ForgotPasswordController
    ↓
TokenService.generate() → genera token HMAC SHA-256
    ↓
EmailService.sendPasswordReset() → envía correo con enlace
    ↓
MailPit captura el correo en http://localhost:8025
    ↓
Usuario hace clic en el enlace → reset_password.php
    ↓
ResetPasswordController → actualiza contraseña
```

### 2. Notificación de cambio de contraseña

```bash
Usuario cambia contraseña (desde configurar_cuenta o admin)
    ↓
configurar_cuenta_controller.php / editar_usuario_controller.php
    ↓
EmailService.sendPasswordChangedNotification() → envía notificación
    ↓
MailPit captura el correo
```

---

## Configuración para producción

### Gmail

```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=tu@gmail.com
SMTP_PASS=abcd efgh ijkl mnop    # App Password de 16 caracteres
SMTP_FROM=tu@gmail.com
SMTP_FROM_NAME=Panda Estampados y Kitsune
```

**Pasos:**

1. Activar verificación en 2 pasos en tu cuenta de Google
2. Ir a `https://myaccount.google.com/apppasswords`
3. Crear una contraseña de aplicación para "Correo"
4. Usar esa contraseña en `SMTP_PASS` (con espacios cada 4 caracteres)

### Outlook / Office 365

```env
SMTP_HOST=smtp.office365.com
SMTP_PORT=587
SMTP_USER=tu@outlook.com
SMTP_PASS=tu_contraseña
SMTP_FROM=tu@outlook.com
SMTP_FROM_NAME=Panda Estampados y Kitsune
```

### Hostinger / Proveedor genérico

```env
SMTP_HOST=smtp.tudominio.com
SMTP_PORT=587
SMTP_USER=noreply@tudominio.com
SMTP_PASS=tu_contraseña
SMTP_FROM=noreply@tudominio.com
SMTP_FROM_NAME=Panda Estampados y Kitsune
```

---

## Cómo funciona EmailService

### Construcción del servicio

```php
use App\Service\EmailService;

$emailService = new EmailService();
```

El constructor lee automáticamente las variables de entorno `SMTP_*` usando `$_ENV`.

### Método: sendPasswordReset()

```php
$result = $emailService-`sendPasswordReset(
    "usuario@ejemplo.com",     // Email del destinatario
    "Juan Pérez",              // Nombre del destinatario
    "https://app.com/reset?token=abc123"  // Enlace de restablecimiento
);

// Retorna true en éxito, o string con mensaje de error
if ($result !== true) {
    echo "Error: " . $result;
}
```

### Método: sendPasswordChangedNotification()

```php
$result = $emailService-`sendPasswordChangedNotification(
    "usuario@ejemplo.com",     // Email del destinatario
    "Juan Pérez"               // Nombre del destinatario
);

// Retorna true en éxito, o string con mensaje de error
```

### Lógica de autenticación SMTP

```php
// Si SMTP_USER está vacío → no usa autenticación (MailPit)
if (!empty($this-`user)) {
    $mail-`SMTPAuth   = true;
    $mail-`Username   = $this-`user;
    $mail-`Password   = $this-`pass;
    $mail-`SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
}
```

Esto permite que funcione tanto con MailPit (sin auth) como con servidores reales (con auth).

---

## Estructura de los correos enviados

### Correo de recuperación de contraseña

- **Asunto:** Recuperación de contraseña — Panda Estampados / Kitsune
- **Contenido:** Botón para restablecer contraseña + advertencia de expiración
- **Expiración:** 30 minutos (configurable con `RESET_PASSWORD_EXPIRES_MINUTES`)

### Correo de notificación de cambio

- **Asunto:** Tu contraseña ha sido cambiada — Panda Estampados / Kitsune
- **Contenido:** Fecha del cambio + advertencia si no fue el usuario
- **Color del header:** Verde (#166534) para indicar éxito

---

## Troubleshooting

### El correo no aparece en MailPit

1. Verificar que el contenedor esté corriendo: `docker ps | grep mailpit`
2. Verificar que `SMTP_FROM` no esté vacío en `.env`
3. Revisar logs: `docker logs pandas_mailpit`

### Error "El servicio de correo no está configurado"

Significa que `SMTP_FROM` está vacío en `.env`. Completar con un email válido.

### Gmail rechaza la conexión

- Usar App Password, no la contraseña normal
- Verificar que la verificación en 2 pasos esté activa
- Si usa Google Workspace, verificar que SMTP esté habilitado

### El correo llega a spam

- Configurar SPF/DKIM en tu dominio
- Usar una dirección `noreply@tudominio.com` consistente
- En producción, considerar un servicio como SendGrid, Mailgun o Amazon SES
