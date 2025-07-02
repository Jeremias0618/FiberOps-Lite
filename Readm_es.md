# FiberOps-Lite

**FiberOps-Lite** es un sistema de autogestión y monitoreo de red desarrollado en PHP para entornos ISP que utilizan **dispositivos MikroTik** como núcleo de su infraestructura. Esta demo forma parte del ecosistema **FiberOps**, un sistema más robusto y avanzado desarrollado por **CyberCode Labs**.

> ⚠️ **Nota:** Este proyecto es una *versión ligera* de **FiberOps**, que ofrece una versión reducida y simplificada de funcionalidades clave para propósitos de prueba, prototipado o despliegues rápidos.

---

## 👨‍💻 Desarrollador

* **Nombre:** Yeremi Tantaraico
* **Correo:** [yeremitantaraico@gmail.com](mailto:yeremitantaraico@gmail.com)
* **Empresa:** CyberCode Labs

---

## 🚀 Funcionalidad Principal

FiberOps-Lite permite consultar información en tiempo real de dispositivos MikroTik, tales como:

* Estado de conexión PPPoE/Hotspot
* Uptime y dirección IP del cliente
* Plan de servicio actual (perfil asignado)
* Tráfico de red (Tx/Rx)
* VLAN asignada por interfaz
* Potencia óptica y modelo de ONT (GPON)
* Información sensible embebida en campos de MikroTik (`profile`, `comment`)

Todo esto a través de una capa backend en PHP que consume la **API binaria de RouterOS** mediante una clase especializada (`RouterosAPI`).

---

## 🧩 Componentes y Scripts Incluidos

| Archivo                          | Función Principal                                                                 |
| -------------------------------- | --------------------------------------------------------------------------------- |
| `routeros_api.class.php`         | Clase para conectar a MikroTik usando su API nativa (TCP/SSL, md5 auth, binario). |
| `FiberOps_Lite_Address.php`      | Consulta y retorna la IP asociada a una interfaz.                                 |
| `FiberOps_Lite_Current_Plan.php` | Muestra el perfil asignado a un usuario PPPoE/Hotspot.                            |
| `FiberOps_Lite_Graphic.php`      | Devuelve tráfico Tx/Rx de una interfaz.                                           |
| `FiberOps_Lite_ONT_Model.php`    | Muestra modelo de la ONT (basado en campos del router).                           |
| `FiberOps_Lite_Power.php`        | Devuelve la potencia óptica (dBm) registrada.                                     |
| `FiberOps_Lite_State.php`        | Verifica si un cliente está conectado o no.                                       |
| `FiberOps_Lite_Uptime.php`       | Muestra el uptime de un usuario conectado.                                        |
| `FiberOps_Lite_Vlan.php`         | Consulta la VLAN asignada a una interfaz bridge.                                  |

---

## 🛠️ Tecnologías Utilizadas

* **PHP** (Backend)
* **API de MikroTik RouterOS**
* **Protocolo TCP/IP** (puertos 8728/8729)
* **Métodos API binarios personalizados** (`write`, `read`, `comm`)
* (Recomendado: integración con JavaScript/AJAX en frontend)

---

## 📦 Casos de Uso

* Paneles de autogestión para clientes ISP
* Dashboards de soporte técnico
* Diagnóstico remoto de problemas de red
* Visualización del estado y tráfico por interfaz
* Prototipado de soluciones de red para MikroTik

---

## ⚠️ Consideraciones de Seguridad

* 🔐 **No usar credenciales en código** directamente. Externalízalas (ej. `.env` o variables de entorno).
* 🧼 **Sanitiza las entradas** `$_GET` para evitar inyecciones o consultas inválidas.
* 📝 **Log de errores**: Implementa manejo de errores robusto y registros para trazabilidad.
* 🚧 **Evita código duplicado** como `routeros_api.class.php` vs `routeros_api_class.php`.

---

## 📈 Escalabilidad y Recomendaciones

* Para grandes volúmenes de clientes o dispositivos, considera:

  * Uso de caché (Redis, Memcached)
  * Almacenamiento temporal de respuestas
  * Migración a un microservicio en segundo plano (Worker API)
  * Alerta proactiva sobre degradación óptica o desconexiones masivas

---

## 📂 Arquitectura General

```plaintext
[ Cliente Web (AJAX/JS) ]
        ↓
[ Scripts PHP (API Gateway Lite) ]
        ↓
[ RouterOS API (MikroTik) ]
```

---

## 🔧 Instalación y Configuración

1. Clonar este repositorio:

   ```bash
   git clone https://github.com/Jeremias0618/FiberOps-Lite.git
   ```
2. Configurar los accesos MikroTik en cada archivo PHP:

   ```php
   define('HOST', '192.168.88.1');
   define('USER', 'admin');
   define('PASS', 'tu_clave');
   ```

   ⚠️ **Importante:** Reemplazar esta lógica con un archivo de configuración seguro.
3. Publicar los archivos PHP en un servidor web compatible con PHP (Apache, Nginx).
4. Consumir desde el frontend vía AJAX/Fetch.

---

## 🧪 Proyecto Oficial

Este proyecto corresponde a una **demo técnica parcial** del sistema **FiberOps**, una plataforma completa para gestión avanzada de red, clientes, servicios y diagnóstico para ISPs.

Para más información sobre el sistema completo, contacta a:
📩 [yeremitantaraico@gmail.com](mailto:yeremitantaraico@gmail.com)

---

## 📃 Licencia

Este proyecto es solo para uso **demostrativo**. Su código puede modificarse con fines educativos, pero no debe ser reutilizado comercialmente sin autorización expresa del autor.

---

## 📎 Etiquetas Sugeridas (Tags)

```
fiberops, mikrotik, php, routeros, api, isp-tools, gpon, pppoe, hotspot, monitoring, dashboard, ajax, self-service, ftth, autogestion, redes, scripts-php
```

---

## 📌 Resumen Técnico para GitHub (373 caracteres)

> Sistema PHP de monitoreo y autogestión ligera para ISPs con infraestructura MikroTik. Incluye scripts para estado, IP, uptime, tráfico, VLAN, plan, modelo ONT y potencia óptica. Proyecto demo parte de FiberOps, plataforma avanzada de CyberCode Labs. Desarrollado por Yeremi Tantaraico.

---