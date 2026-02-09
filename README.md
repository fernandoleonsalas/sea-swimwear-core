# sea-swimwear-core
Este proyecto es el motor lógico de una tienda en línea de trajes de baño, desarrollado bajo el **TALL Stack**. El sistema gestiona todo el flujo de venta, desde la selección de productos hasta la confirmación de pedidos con lógica de pagos parciales.

## Demostración en Vídeo
Haz clic en la imagen a continuación para ver el flujo completo del sistema (Carrito, Pagos Zelle y Panel Admin):

[![Ver Demo de Sea Swimwear](https://img.youtube.com/vi/qtr6EO3ELSY/0.jpg)](https://www.youtube.com/watch?v=qtr6EO3ELSY)

> **Nota:** Si no puedes ver la imagen, puedes acceder directamente al video aquí: [Enlace al Demo](https://www.youtube.com/watch?v=qtr6EO3ELSY)
> 
## Características Principales 
* Desarrollo de una plataforma de comercio electrónico a medida con PHP/Laravel y WordPress, gestionando ventas al detal y al por mayor.
* Implementación de un sistema de pagos híbrido (Pago Móvil, Zelle) con lógica de pagos fraccionados (abonos del 50%).
* Creación de un Panel de Administración (Backoffice) para la gestión centralizada de inventario, pedidos y catálogo de productos.
* Desarrollo de un sistema de notificaciones automatizadas vía SMTP para confirmación de pedidos y estados de envío.
* Optimización de la experiencia de usuario con buscador dinámico de productos y carrito de compras funcional.

## Tecnologías Utilizadas

El proyecto se basa en el **TALL Stack**, un conjunto de herramientas modernas para el ecosistema Laravel que permite crear aplicaciones web reactivas y escalables.

* **Frontend & UI:**
    * HTML5
    * [Tailwind CSS](https://tailwindcss.com/): Framework de diseño basado en utilidades para interfaces personalizadas.
    * [Alpine.js](https://alpinejs.dev/): Framework de JavaScript ligero para manejar la interactividad en el cliente.
    * [Livewire](https://livewire.laravel.com/): Framework full-stack para Laravel que permite crear interfaces dinámicas sin salir de PHP.
    * [JavaScript (ES6+)](https://developer.mozilla.org/es/docs/Web/JavaScript): Lógica adicional para el manejo de eventos y APIs del navegador.
    * [CSS3](https://developer.mozilla.org/es/docs/Web/CSS): Estilos personalizados y variables de diseño.
* **Backend & Lógica de Negocio:**
    * [Laravel](https://laravel.com/): Framework de PHP elegante y robusto bajo el patrón MVC.
    * [Laravel Breeze](https://laravel.com/docs/starter-kits#laravel-breeze): Sistema de autenticación simple y ligero con soporte para Tailwind y Alpine.
    * [PHP](https://www.php.net/): Lenguaje de programación principal del servidor.
    * [Composer](https://getcomposer.org/): Gestor de dependencias para los paquetes de PHP.
* **Almacenamiento & Servidor:**
    * [MariaDB](https://mariadb.org/) / [MySQL](https://www.mysql.com/)**: Motor de base de datos relacional.
    * [Vite](https://vitejs.dev/): Herramienta de compilación rápida para los activos del frontend (CSS y JS).
    * [Apache](https://httpd.apache.org/): Servidor web utilizado para el despliegue y desarrollo (vía XAMPP).
* **Servicios de Correo:**
    * Integración con SMTP (Spacemail) mediante colas de trabajo.

## Requisitos del Sistema

Para ejecutar este proyecto localmente, necesitas tener instalado:

* **XAMPP** (o un entorno LAMP/WAMP/MAMP similar con Apache, PHP y MariaDB).
* **Composer** (gestor de dependencias de PHP).
* **Git** (para clonar el repositorio).

## Configuración e Instalación Local

Para ejecutar este proyecto en su entorno local, asegúrese de tener instalados los siguientes componentes:

1. **Entorno PHP:** [XAMPP](https://www.apachefriends.org/), [Laragon](https://laragon.org/) o [Docker](https://www.docker.com/).
2. **PHP:** Versión `>= 8.2` (con extensiones `bcmath`, `ctype`, `fileinfo`, `mbstring`, `pdo_mysql`).
3. **Gestor de Dependencias PHP:** [Composer](https://getcomposer.org/) versión `2.x`.
4. **Entorno Node.js:** [Node.js](https://nodejs.org/) (versión LTS recomendada) y **NPM**.
5. **Base de Datos:** MariaDB `10.4+` o MySQL `8.0+`.

## Guía de Instalación Local

Siga estos pasos detallados para configurar el proyecto en su máquina:

1.  **Clonar el Repositorio:**
    Abre tu terminal y ejecuta:
    ```bash
    git clone [https://github.com/TU_USUARIO/sea-swimwear-core.git](https://github.com/TU_USUARIO/sea-swimwear-core.git)
    ```
2.  **Instalar dependencias:**
    ```bash
    composer install
    npm install && npm run build
    ```
3.  **Configurar el entorno:**
    * Renombrar `.env.example` a `.env`.
    * Configurar las credenciales de la base de datos y de **Spacemail**.
4.  **Ejecutar migraciones y carga de datos:**
    ```bash
    php artisan migrate --seed
    ```
### Ejecución de Correos y Procesos (Worker)
Este sistema requiere que los procesos de segundo plano estén activos para gestionar el envío de correos y la inserción de registros en las tablas de seguimiento:

1. **Para procesar la cola de correos:**
   ```bash
   php artisan queue:work
   ```
### Tips adicionales para el usuario:
* **Si el usuario usa XAMPP:** Recuérdale que Apache y MySQL deben estar activos en el panel de control.
* **Producción:** Si alguien va a subirlo a un servidor real, debe usar `npm run build` en lugar de `npm run dev`.

## Estructura del Proyecto
```dotenv
sea-swimwear-core/        - Raiz del proyecto 
├── .github/              - Configuración de GitHub (Actions, Workflows)
├── app/                  - Lógica central (Modelos, Controladores)
├── bootstrap/            - Archivos de inicio del framework
├── config/               - Archivos de configuración general
├── database/             - Migraciones, Seeders y Factories
├── lang/                 - Archivos de traducción/idiomas
├── node_modules/         - Dependencias de JavaScript (NPM)
├── public/               - Directorio raíz del servidor web
│   ├── build/            - Assets compilados (Vite)
│   ├── images/           - Imágenes estáticas
│   ├── pdf/              - Documentos PDF
│   ├── storage/          - Enlace simbólico a storage/app/public
│   ├── .htaccess         - Configuración de Apache
│   ├── favicon.ico       - Icono del sitio
│   ├── index.php         - Punto de entrada principal
│   └── robots.txt        - Instrucciones para buscadores
├── resources/            - Vistas (Blade), CSS y JS sin procesar
├── routes/               - Definición de rutas (web.php, api.php)
├── storage/              - Logs, caché y archivos subidos
├── tests/                - Pruebas automatizadas
├── vendor/               - Dependencias de PHP (Composer)
├── .editorconfig         - Configuración del editor de código
├── .env                  - Variables de entorno (Sensible)
├── .env.example          - Plantilla para el archivo .env
├── .gitattributes        - Configuración de atributos Git
├── .gitignore            - Archivos excluidos de Git
├── .htaccess             - Configuración de servidor adicional
├── artisan               - Interfaz de línea de comandos de Laravel
├── composer.json         - Lista de dependencias de PHP
├── composer.lock         - Versiones exactas de dependencias PHP
├── deploy-staging.yml    - Script de despliegue
├── package-lock.json     - Versiones exactas de dependencias JS
├── package.json          - Lista de dependencias de JS
├── phpunit.xml           - Configuración de pruebas
├── README.md             - Documentación del proyecto
└── vite.config.js        - Configuración del compilador Vite
```

## Contacto

Para cualquier consulta, soporte o información adicional sobre el proyecto, puedes contactar a:

[Fernando León]
[fernandoleom56@gmail.com]
