<h1 align="center">NCF Hud</h1>

## Descripción

**ncfhud** es una aplicación desarrollada en Laravel diseñada para la gestión y validación de comprobantes fiscales en la República Dominicana. Proporciona herramientas avanzadas para el procesamiento de facturas, conciliación de datos y generación de reportes fiscales, facilitando la automatización y el control en la administración tributaria.

## Características

- ✅ **Validación automática de comprobantes fiscales** para garantizar la integridad de los datos.   
- 📌 **Interfaz intuitiva y adaptable**, optimizada para distintos dispositivos.  

## Instalación

Para instalar y configurar la aplicación, sigue estos pasos:

```bash
# Clona el repositorio
git clone https://github.com/Alejandro-Echavarria/ncfhud.git
cd ncfhud

# Instala las dependencias
composer install
npm install && npm run dev

# Configura el entorno
cp .env.example .env
php artisan key:generate

# Configura la base de datos en el archivo .env y luego ejecuta las migraciones
php artisan migrate --seed
```

## Uso

Inicia el servidor de desarrollo con:

```bash
php artisan serve
```

## Contribución
Si deseas contribuir al desarrollo de ncfhud, puedes hacer un fork del repositorio y enviar pull requests con mejoras o correcciones. También puedes reportar errores o sugerencias en la sección de issues de GitHub.

## Licencia

Este proyecto está licenciado bajo **Creative Commons BY-NC-ND 4.0**, lo que significa que puedes usarlo y compartirlo sin modificaciones, pero **no puedes modificarlo ni venderlo**.

🔗 [Ver licencia completa](LICENSE)

***🚀 Desarrollado por:*** Alejandro Echavarría
