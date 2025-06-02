# Beneficios API
## Requerimientos

- PHP 8.0+
- Laravel 11.x **
- Composer

## Installation
1. Clonar el repositorio:
   git clone https://github.com/snowflakeCs/benefits.git
   cd benefits

2. Instalar dependencias:
   composer install

3. generar app key:
   php artisan key:generate

6. correr migraciones (opcional):
   php artisan migrate

7. iniciar  el servidor
   php artisan serve

la API esta disponible en `http://localhost:8000/api/documentation`.

## API Documentation

La api esta documentada en Swagger/OpenAPI

```
http://localhost:8000/api/documentation
```

## Endpoints Disponibles

- `GET /api/benefits/by-year` - Obtiene beneficios ordenados por año
- `GET /api/benefits/total-amount-per-year` - Obtiene monto total por año
- `GET /api/benefits/count-per-year` - Obtiene conteo de beneficios por año
- `GET /api/benefits/filter-by-amount-range` - Filtros beneficios por rango de monto
- `GET /api/benefits/with-cards` - Obtiene beneficios con susfichas
- `GET /api/benefits/by-year-asc-to-desc` - Obtiene beneficios ordenados por año
- `GET /api/benefits` - obtiene todos los beneficios en el formato del ejemplo


## Postman Collection

Se encuentra en el archivo `postman_collection.json`. para utilizar:
1. Importar el archivo `postman_collection.json` en postman.
2. Actualizar la `base_url` si es necesario.
Y lIsto.

## Tests

El proyecto tiene tests unitarios , para correrlos:
$php artisan test

Para correr el test especifico: 
$php artisan test --filter BenefitsTest

## Creditos
Creado por Constanza Lopez Modinger - Snowflake