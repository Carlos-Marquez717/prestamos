# Sistema de Prestamos

Sistema web desarrollado con Laravel para administrar clientes, prestamos, abonos y reportes financieros. La aplicacion permite registrar clientes, crear prestamos, controlar pagos parciales, consultar saldos pendientes y generar comprobantes en PDF con codigos QR.

Este proyecto fue creado como una solucion administrativa para centralizar el seguimiento de prestamos y pagos, reduciendo el uso de registros manuales y facilitando la consulta historica por cliente.

## Descripcion Corta Para GitHub

Sistema web en Laravel para gestion de clientes, prestamos, abonos, dashboard financiero y generacion de reportes PDF con codigos QR.

## Caracteristicas Principales

- Autenticacion de usuarios con Laravel Breeze.
- Panel de control con resumen de prestamos y abonos.
- Gestion CRUD de clientes.
- Registro de prestamos asociados a clientes.
- Registro de abonos con validacion del saldo disponible.
- Busqueda y filtrado de informacion.
- Historial de prestamos y abonos por cliente.
- Generacion de comprobantes PDF individuales.
- Reportes financieros por dia, semana, mes, anio y total acumulado.
- Codigos QR en comprobantes para facilitar verificacion.
- Interfaz construida con Blade, Tailwind CSS y componentes reutilizables.

## Modulos

### Clientes

Permite registrar, editar, listar, buscar y eliminar clientes. Cada cliente puede tener multiples prestamos asociados y un historial completo de movimientos.

### Prestamos

Permite crear prestamos por cliente, consultar el detalle de cada prestamo, visualizar saldos y eliminar registros cuando corresponde.

### Abonos

Permite registrar pagos parciales sobre un prestamo. El sistema valida que el abono no exceda el saldo pendiente.

### Dashboard

Presenta indicadores generales de la operacion, incluyendo montos prestados y abonados agrupados por periodos.

### Reportes PDF

Genera comprobantes y reportes descargables usando TCPDF. Incluye reportes por periodos y documentos asociados a clientes, prestamos y abonos.

## Tecnologias Utilizadas

- PHP 8.1+
- Laravel 10
- MySQL / MariaDB
- Laravel Breeze
- Laravel Sanctum
- Blade
- Tailwind CSS
- Alpine.js
- Vite
- Chart.js
- TCPDF
- Endroid QR Code
- Filament

## Requisitos

- PHP 8.1 o superior
- Composer
- Node.js y npm
- MySQL o MariaDB
- Extensiones PHP recomendadas:
  - `curl`
  - `fileinfo`
  - `gd`
  - `intl`
  - `mbstring`
  - `openssl`
  - `pdo_mysql`
  - `zip`

## Instalacion

1. Clonar el repositorio:

```bash
git clone https://github.com/Carlos-Marquez717/prestamos.git
cd prestamos
```

2. Instalar dependencias de PHP:

```bash
composer install
```

3. Instalar dependencias de frontend:

```bash
npm install
```

4. Crear el archivo de entorno:

```bash
cp .env.example .env
```

En Windows PowerShell tambien puedes usar:

```powershell
Copy-Item .env.example .env
```

5. Generar la clave de la aplicacion:

```bash
php artisan key:generate
```

6. Configurar la base de datos en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prestamos
DB_USERNAME=root
DB_PASSWORD=
```

7. Ejecutar migraciones:

```bash
php artisan migrate
```

8. Iniciar el servidor de Laravel:

```bash
php artisan serve
```

9. En otra terminal, iniciar Vite:

```bash
npm run dev
```

La aplicacion quedara disponible normalmente en:

```text
http://127.0.0.1:8000
```

## Comandos Utiles

Ejecutar pruebas:

```bash
php artisan test
```

Compilar assets para produccion:

```bash
npm run build
```

Limpiar cache de Laravel:

```bash
php artisan optimize:clear
```

Aplicar formato de codigo:

```bash
./vendor/bin/pint
```

En Windows:

```powershell
vendor\bin\pint
```

## Estructura Del Proyecto

```text
app/
  Http/Controllers/   Controladores de clientes, prestamos, abonos y reportes
  Models/             Modelos principales del dominio
  Services/           Servicios auxiliares para generacion de documentos
database/
  migrations/         Definicion de tablas y relaciones
  seeders/            Datos iniciales de la aplicacion
resources/
  views/              Vistas Blade de la interfaz
  css/                Estilos principales
  js/                 JavaScript de la aplicacion
routes/
  web.php             Rutas web protegidas por autenticacion
public/
  images/             Imagenes y recursos publicos
```

## Modelo De Datos Principal

- Un cliente puede tener muchos prestamos.
- Un prestamo pertenece a un cliente.
- Un prestamo puede tener muchos abonos.
- Cada abono pertenece a un prestamo.

Esta estructura permite consultar el saldo pendiente de cada prestamo y el historial financiero completo de cada cliente.

## Flujo Principal De Uso

1. El usuario inicia sesion.
2. Registra un cliente.
3. Crea un prestamo asociado al cliente.
4. Registra abonos sobre el prestamo.
5. Consulta el estado del prestamo y saldo restante.
6. Genera comprobantes o reportes en PDF.
7. Revisa metricas generales desde el dashboard.

## Variables De Entorno Importantes

```env
APP_NAME="Sistema Prestamos"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_DATABASE=prestamos
DB_USERNAME=root
DB_PASSWORD=
```

Para produccion se recomienda usar:

```env
APP_ENV=production
APP_DEBUG=false
```

## Estado Del Proyecto

Proyecto funcional orientado a portfolio, con modulos principales de gestion financiera, autenticacion, dashboard y reportes PDF. Antes de usarlo en produccion real se recomienda reforzar permisos, roles de usuario, validaciones especificas del negocio, respaldos de base de datos y despliegue seguro.

## Aprendizajes Demostrados

- Arquitectura MVC con Laravel.
- Relaciones Eloquent entre modelos.
- Validacion de formularios.
- Rutas protegidas por autenticacion.
- Generacion de documentos PDF.
- Integracion de codigos QR.
- Uso de migraciones para versionar base de datos.
- Construccion de interfaz con Blade y Tailwind CSS.
- Uso de Vite para compilar assets frontend.

## Autor

Desarrollado como proyecto de portfolio para demostrar habilidades en desarrollo web con Laravel, PHP, MySQL y herramientas modernas de frontend.

## Licencia

Este proyecto esta disponible como muestra de portfolio. Puedes adaptar la licencia segun el uso que quieras darle al repositorio.
