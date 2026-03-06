# Prueba Técnica (Backend Symfony + App Móvil Ionic/Angular)

Este repositorio contiene:

## Backend (Symfony + MariaDB + Nginx + Docker)
- Panel **/admin** (solo `ROLE_ADMIN`)
- API **/api** consumida por la app móvil (JWT)
- Fixtures con datos de prueba
- Test automatizado (PHPUnit)

## App móvil (Ionic + Angular + Capacitor Android)
- Login técnico
- Listado y detalle de órdenes
- Cambio de estado de órdenes
- Soporta ambientes (dev vs prod)

---

## Requisitos

### Backend
- Docker Desktop (descargar desde la página oficial y seguir los pasos de instalación; en Windows ideal con WSL2)
- Docker Compose (normalmente viene incluido con Docker Desktop)

### Mobile
- Node.js (recomendado LTS)
- Ionic CLI
- Android Studio + SDK
- Java 17 (recomendado para Android Studio moderno)

---

# 1) Backend (Symfony)

## 1.1 Variables de entorno (ambientes)

En `backend/` existen archivos `.env` por ambiente:

- `.env` (base)
- `.env.dev` (opcional)
- `.env.test`
- `.env.qa`
- `.env.example` (plantilla)

Symfony usa `APP_ENV=dev|test|prod|qa`.

### Ejecutar comandos en un ambiente (recomendado: opción universal)

Desde el terminal de VS Code (o cualquier terminal), puedes ejecutar:

```bash
docker compose exec -e APP_ENV=qa app php bin/console cache:clear
```

> Nota: esto evita depender de si tu terminal es PowerShell o Bash.

---

## 1.2 Levantar servicios con Docker

Desde `backend/`:

```bash
docker compose up -d --build
docker compose ps
```

Servicios esperados:

- nginx: expone backend en `http://localhost:8000`
- app (php-fpm)
- db (mariadb) puerto host `3307`
- phpmyadmin en `http://localhost:8081`

---

## 1.3 Verificar que responde

Es normal que `GET /` sea **404** si no creaste una ruta home.

Pruebas rápidas:

- Panel admin: `http://localhost:8000/admin`
- API login: `POST http://localhost:8000/api/login`

---

## 1.4 Migraciones + fixtures (datos de prueba)

Dentro de `backend/`:

```bash
docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction
docker compose exec app php bin/console doctrine:fixtures:load --no-interaction
```

Esto deja creado:

- al menos 1 admin
- varios técnicos
- órdenes en distintos estados

---

## 1.5 Credenciales de prueba (ejemplo)

Ajusta estos datos a los que tengas en fixtures.

**Admin (Panel)**
- Email: `admin@ott.cl`
- Password: `Admin123!`

**Técnico (App)**
- Email: `tech1@ott.cl`
- Password: `Tech123!`

---

## 1.6 Endpoints principales API

- `POST /api/login` → retorna `{ token }`
- `GET /api/work-orders?status=...` → lista órdenes del técnico autenticado
- `GET /api/work-orders/{id}` → detalle
- `PATCH /api/work-orders/{id}/status` → actualiza estado

Protección:

- Todo `/api/**` requiere JWT (excepto `/api/login`)
- Un técnico solo ve/modifica lo propio

---

## 1.7 CORS (para app móvil)

La app (Capacitor) corre bajo `https://localhost`, por lo que el backend debe permitir ese origin.

Configurar CORS para permitir:

- Origin: `https://localhost`
- Métodos: `GET, POST, PATCH, OPTIONS`
- Headers: `Authorization, Content-Type`

---

## 1.8 Tests (PHPUnit)

Ejecutar tests:

```bash
docker compose exec app php bin/phpunit
```

### Nota sobre DB de test

En tests, Symfony usa una base con sufijo `_test` (ej: `app_test`).

Preparar ambiente test:

```bash
docker compose exec app php bin/console doctrine:database:create --env=test
docker compose exec app php bin/console doctrine:migrations:migrate --env=test --no-interaction
docker compose exec app php bin/console doctrine:fixtures:load --env=test --no-interaction
docker compose exec app php bin/phpunit
```

---

# 2) App Móvil (Ionic + Angular + Capacitor)

## 2.1 Instalar dependencias

Desde `mobile/`:

```bash
npm install
```

---

## 2.2 Configuración de ambientes (DEV vs PROD)

La app usa:

- `src/environments/environment.ts` (dev)
- `src/environments/environment.prod.ts` (prod)

### DEV (Emulador Android)

Usar:

```ts
apiUrl: "http://10.0.2.2:8000/api"
```

### PROD

```ts
apiUrl: "https://tu-dominio.com/api"
```

---

## 2.3 Levantar en navegador (modo web)

```bash
ionic serve
```

---

## 2.4 Correr en Android Studio

Build + sync:

```bash
ionic build
npx cap sync android
```

Abrir Android Studio:

```bash
npx cap open android
```

Luego ejecutas **Run** desde Android Studio.

---

## 2.5 Permiso de internet en Android

Verifica en `android/app/src/main/AndroidManifest.xml`:

```xml
<uses-permission android:name="android.permission.INTERNET" />
```

---

# 3) Puertos y URLs usadas

- Backend Nginx: `http://localhost:8000`
- PhpMyAdmin: `http://localhost:8081`
- DB (host): `localhost:3307`
- Android Emulator hacia PC: `http://10.0.2.2:8000`
