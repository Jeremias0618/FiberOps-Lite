# FiberOps-Lite

**FiberOps-Lite** is a lightweight PHP-based self-management and network monitoring system designed for **ISPs using MikroTik** devices as their core infrastructure. This project is a **demo subset** of the full **FiberOps** platform — a more advanced, robust, and production-ready solution developed by **CyberCode Labs**.

> ⚠️ **Note:** This is a demo project, not intended for production use. The full version of **FiberOps** includes advanced features, improved security, better scalability, and refined UI/UX.

---

## 👨‍💻 Developer Info

* **Name:** Yeremi Tantaraico
* **Email:** [yeremitantaraico@gmail.com](mailto:yeremitantaraico@gmail.com)
* **Company:** CyberCode Labs

---

## 🚀 Main Functionality

FiberOps-Lite connects to **MikroTik RouterOS API** and provides essential real-time data about network clients and interfaces. Features include:

* PPPoE / Hotspot connection status
* Client IP and uptime
* Current service plan (profile)
* Interface traffic (Tx/Rx)
* VLAN ID assigned to ports
* Optical power and ONT model

These functionalities are exposed via PHP scripts that interact directly with MikroTik devices using their binary API protocol.

---

## 🧩 Project Components

| File                             | Description                                                     |
| -------------------------------- | --------------------------------------------------------------- |
| `routeros_api.class.php`         | PHP class for low-level connection to RouterOS API via TCP/SSL. |
| `FiberOps_Lite_Address.php`      | Gets the IP address assigned to a specific interface.           |
| `FiberOps_Lite_Current_Plan.php` | Retrieves the user profile (service plan) from PPP/Hotspot.     |
| `FiberOps_Lite_Graphic.php`      | Returns raw Tx/Rx bytes for graphing bandwidth.                 |
| `FiberOps_Lite_ONT_Model.php`    | Extracts ONT model info from MikroTik fields.                   |
| `FiberOps_Lite_Power.php`        | Retrieves optical power value (dBm) for ONTs.                   |
| `FiberOps_Lite_State.php`        | Checks if the user is currently connected.                      |
| `FiberOps_Lite_Uptime.php`       | Returns connection uptime for PPP/Hotspot users.                |
| `FiberOps_Lite_Vlan.php`         | Gets the PVID (VLAN ID) assigned to a bridge port.              |

---

## 🛠️ Technologies Used

* **PHP** (backend scripting)
* **MikroTik RouterOS API**
* **Binary API Protocol** (length-prefixed sentences)
* **TCP/IP Socket Communication**
* (Recommended: AJAX-based frontend)

---

## 📦 Use Cases

* **Customer self-service portals** for ISPs
* **Network monitoring dashboards**
* **Technical support tools** for remote diagnostics
* **Client-side real-time data visualization**
* **Prototype for MikroTik-based service platforms**

---

## ⚠️ Security & Best Practices

* 🔐 **Credentials** should NOT be hardcoded in scripts. Use secure configs (.env, vaults).
* 🧼 **Sanitize `$_GET` inputs** to prevent injection or invalid requests.
* 📝 **Implement proper error logging** instead of plain `"N/A"` responses.
* 🚫 **Avoid class duplication** (`routeros_api.class.php` vs `routeros_api_class.php`).

---

## 📈 Scalability Notes

For larger deployments or multiple MikroTik devices:

* Use **caching** for repeated API queries (Redis, APCu).
* Move to **background workers** for polling heavy metrics.
* Offload repeated metrics to **monitoring platforms** (e.g., Prometheus).
* Ensure scripts are async-safe and non-blocking on high load.

---

## 📂 System Architecture

```plaintext
[ Web Frontend (AJAX) ]
        ↓
[ FiberOps-Lite PHP Scripts ]
        ↓
[ MikroTik RouterOS API ]
```
---

## 🧪 About the Full Version: *FiberOps*

This is a **demo version** extracted from **FiberOps**, a full-featured commercial system designed for:

* ISP client management
* Real-time diagnostics
* Optical metrics monitoring (GPON/FTTH)
* Automated provisioning
* Billing & CRM integration

To learn more about the full product, contact:
📧 [yeremitantaraico@gmail.com](mailto:yeremitantaraico@gmail.com)

---

## 📃 License

This is a **demo/educational project only**. Redistribution or commercial use without permission from the author is not allowed.

---