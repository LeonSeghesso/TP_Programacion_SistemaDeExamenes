# Sistema de Exámenes

Trabajo Práctico N.° 2 – Integración de JavaScript y PHP

- **Asignatura:** Programación III
- **Carrera:** Tecnicatura Superior en Desarrollo de Software
- **Instituto:** [COMPLETAR: nombre del instituto]
- **Docente:** Delfor Hernán Castro
- **Autor/es:** [COMPLETAR: nombre y apellido]
- **Año:** 2026

## Descripción

Aplicación web que permite a cada usuario registrarse, iniciar sesión, administrar sus propios exámenes y preguntas, y sortear al azar una cantidad de preguntas de un examen sin que se repita ninguna.

## Funcionalidades

- Registro de usuarios (sin login previo). Las contraseñas se guardan con `password_hash()`.
- Inicio y cierre de sesión (sesiones de PHP).
- Alta, edición y baja de **exámenes**. Cada usuario ve solo los suyos.
- Alta, edición y baja de **preguntas** dentro de cada examen. Cada usuario ve solo las suyas.
- **Sorteo** de preguntas: el usuario elige cuántas quiere y se sortean al azar entre las preguntas de ese examen, sin repetir.

## Tecnologías

- PHP (mysqli con consultas preparadas)
- MySQL / MariaDB
- JavaScript con jQuery 4 (AJAX con formato JSON)
- Bootstrap 5

## Instalación

1. Instalar [XAMPP](https://www.apachefriends.org/) e iniciar **Apache** y **MySQL**.
2. Copiar la carpeta del proyecto en `htdocs`, por ejemplo `htdocs/SistemaExamenes`.
3. En phpMyAdmin, importar el archivo `sql/base_de_datos.sql` (crea la base `examenes_pagina` y sus tablas).
4. Si es necesario, ajustar usuario y contraseña de MySQL en `php/conexion.php`.
5. Abrir en el navegador: `http://localhost/SistemaExamenes/index.html`

> Importante: la página debe abrirse mediante `http://localhost/...`, no haciendo doble clic sobre el archivo, porque si no el navegador bloquea las peticiones AJAX.

## Uso

1. Registrarse en la página de inicio e iniciar sesión.
2. En **Mis exámenes**, crear un examen.
3. Entrar con el botón **Preguntas** y cargar las preguntas del examen.
4. En esa misma pantalla, elegir la cantidad de preguntas y tocar **Sortear preguntas**.

## Base de datos

Tablas: `usuarios`, `examen` y `preguntas`.

- Un usuario puede tener uno o muchos exámenes.
- Un examen pertenece a un solo usuario y puede tener una o muchas preguntas.
- Una pregunta pertenece a un solo examen.
- Al eliminar un examen se eliminan sus preguntas (`ON DELETE CASCADE`).

La estructura completa está en `sql/base_de_datos.sql`.

## Estructura de archivos

```
SistemaExamenes/
├── index.html            Inicio de sesión y registro
├── examenes.html         Lista y gestión de exámenes
├── preguntas.html        Gestión de preguntas y sorteo
├── README.md
├── css/
│   └── estilos.css       Estilos de la página de inicio
├── js/
│   ├── ajax_script.js    Registro e inicio de sesión
│   ├── examenes.js       Gestión de exámenes
│   └── preguntas.js      Gestión de preguntas y sorteo
├── php/
│   ├── conexion.php      Conexión a la base de datos
│   ├── guardar_usuario.php, iniciar_sesion.php, cerrar_sesion.php
│   ├── listar_examenes.php, crear_examen.php, editar_examen.php, eliminar_examen.php
│   └── listar_preguntas.php, crear_pregunta.php, editar_pregunta.php,
│       eliminar_pregunta.php, sortear_preguntas.php
└── sql/
    └── base_de_datos.sql Estructura de la base de datos
```
