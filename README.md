# Proyecto Web Social

## Descripción
**Proyecto Web Social** es una plataforma web que permite a los usuarios interactuar, compartir contenido y conectarse con otras personas. Está diseñada con enfoque en la experiencia de usuario, seguridad y escalabilidad.

Este proyecto forma parte de los trabajos académicos de desarrollo web y tiene como objetivo aprender y aplicar tecnologías modernas en un entorno realista.

---

## Funcionalidades

- Registro y autenticación de usuarios  
- Creación y gestión de perfiles  
- Publicaciones con imágenes y texto  
- Sistema de comentarios asociados a los posts  

---

## Tecnologías utilizadas

- **Frontend:** HTML5, CSS3, JavaScript, Bootstrap  
- **Backend:** PHP  
- **Base de datos:** MySQL  
- **Control de versiones:** Git y GitHub  

---

## Base de datos

### Nombre de la base de datos
`proyecto1`

### Tablas y columnas

#### 🧍 Tabla `users`
| Columna | Tipo | Descripción |
|----------|------|-------------|
| `id` | int (PK) | Identificador único del usuario |
| `name` | varchar | Nombre del usuario |
| `email` | varchar (UNIQUE) | Correo electrónico del usuario |
| `password` | varchar | Contraseña encriptada del usuario |

---

#### 📝 Tabla `posts`
| Columna | Tipo | Descripción |
|----------|------|-------------|
| `id` | int (PK) | Identificador único del post |
| `titulo` | varchar | Título del post |
| `contenido` | longtext | Contenido principal de la publicación |
| `imagen` | varchar (NULLABLE) | URL o nombre de archivo de imagen asociada |
| `created_at` | datetime | Fecha y hora de creación |
| `user_id` | int (FK) | Identificador del usuario autor del post |

---

#### 💬 Tabla `comentarios`
| Columna | Tipo | Descripción |
|----------|------|-------------|
| `id` | int (PK) | Identificador único del comentario |
| `autor` | varchar | Nombre o autor del comentario |
| `cuerpo` | longtext | Texto del comentario |
| `created_at` | datetime | Fecha y hora de creación |
| `post_id` | int (FK) | Identificador del post al que pertenece |

---

#### ⚙️ Tabla `doctrine_migration_versions`
Tabla utilizada internamente por el sistema de migraciones para registrar versiones y cambios en la base de datos.

---
