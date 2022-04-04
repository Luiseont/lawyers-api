## Lawyers API

Prueba tecnica para puestro de desarrollador backend para Nexoabogados

## Instalacion

Inicialmente

* [Clonar repositorio]
* [Ejecutar Composer install]

Ejecutar en consola los siguientes comandos.

* [php artisan migrate]
* [php artisan db:seed]
* [php artisan passport:install]

Para iniciar las Queue debe ejecutar el worker de Laravel con el siguiente comando.

* [php artisan queue:work --queue=process,dialy]

## Consideraciones

Utiliza Laravel Queue para manejar las "Suscripciones" entrantes y posterior proceso de pago.
Segun las indicaciones los pagos se deben procesar 30 minutos luego de recibida.. Esto se lleva a cabo en el Job [ProcessPayments] utilizando la queue [Process].

En caso de pago erroneo, el sistema agrega la suscripcion a la cola [dialy] manejado por el Job [ProcessDayliPayments] el cual se encargara de reintentar el proceso cada 24 horas hasta un segundo intento, si el pago no es exitoso en ninguno de los intentos la suscripcion se marcara como inactiva.

Se envian notificaciones por [email] en cada caso.

## Autenticacion

Los [seeders] contienen informacion de usuarios para el inicio de sesion.
Para acciones de administracion utilizar el usuario

User: Admin@admin.com
pwd:  administrator

Para acciones de lawyer utilizar los usuario

User: Lawyer@admin.com
pwd:  lawyer

User: Lawyer2@admin.com
pwd:  lawyer

User: Lawyer3@admin.com
pwd:  lawyer

## uso

Al realizar el proceso de autenticacion y este resulte exitoso recibira un token.
Dicho token debera ser enviado en las cabeceras de las peticiones utilizando la clave [Authorization] con el valor [Bearer {token}]
ademas la peticion debe enviarse con la clave [Accept] cuyo valor debe ser [application/json]

## Endpoints

Segun las instrucciones los endpoints requeridos son los siguiente:

## POST /login

Inicio de sesion en el sistema.

**Parameters**
|Nombre     | Required |  Tipo    | Descripcion  
|`email`    | required | String   | Correo del usuario
|`password` | required | String   | clave de acceso.

-[Lawyers]

## POST /suscription

Registra una nueva suscripcion

**Parameters**
|Nombre    | Required |  Tipo     | Descripcion  
|`type`    | required | Numeric   | Tipo de suscripcion a crear. existen 3 tipos [Semanal, Mensual, Anual]
|`amount`  | required | Numeric   | Monto de la nueva suscripcion.


## GET /suscription/{id}

Retorna la informacion de una suscripcion.

**Parameters**
|Nombre    | Required |  Tipo     | Descripcion  
|`id`      | required | Numeric   | ID de la suscripcion a visualizar.

## PUT /suscription

Actualiza la suscripcion del usuario.

**Parameters**
|Nombre        | Required |  Tipo     | Descripcion  
|`suscription` | required | Numeric   | ID de la suscripcion a actualizar.
|`type`        | required | Numeric   | Nuevo tipo de suscripcion.
|`amount`      | opcional | Numeric   | Nuevo monto de la suscripcion

## DELETE /suscription/{id}

Elimina la suscripcion actual del usuario.

**Parameters**
|Nombre    | Required |  Tipo     | Descripcion  
|`id`      | required | Numeric   | ID de la suscripcion a eliminar.


-[Admin]

## GET /getsuscriptions/{criterio}

Retorna todas las suscripciones que coincidan con el criterio

**Parameters**
|Nombre    | Required |  Tipo     | Descripcion  
|`criterio`| required | String    | Criterio de busqueda. [activo] o [inactivo]

## GET /getsuscription/{id}

Retorna la informacion de una suscripcion.

**Parameters**
|Nombre    | Required |  Tipo     | Descripcion  
|`id`      | required | Numeric   | ID de la suscripcion a visualizar.suscription

## POST /cancelSuscription

Cancela una suscripcion arbitrariamente.

**Parameters**
|Nombre         | Required |  Tipo     | Descripcion  
|`suscription`  | required | Numeric   | ID de la suscripcion a cancelar.

## POST /retryPayment

Reintenta un pago de una suscripcion inactiva.
Si es satisfactorio, reactiva la suscripcion.

**Parameters**
|Nombre         | Required |  Tipo     | Descripcion  
|`suscription`  | required | Numeric   | ID de la suscripcion.

