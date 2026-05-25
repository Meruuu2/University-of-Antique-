# UML Deployment Diagram — UAFMS (University Asset and Facilities Management System)

---

## Production Deployment Architecture

The diagram below illustrates the production-ready deployment architecture for the UAFMS system using standard UML deployment notation (Nodes, Artifacts, and Communication Paths).

```mermaid
architecture-beta
    group client(cloud)[Client Node]
    group infra(cloud)[Infrastructure / Web Server Node]
    group app(cloud)[Application / Backend Node]
    group data(cloud)[Data Node]
    group security(cloud)[Security and External Services]

    service userDevice(server)[User Device - Laptop / Mobile - Web Browser] in client

    service nginx(server)[Nginx Reverse Proxy - Static Assets - SSL Termination] in infra

    service laravelApp(server)[Laravel 12 Application - PHP 8.2 plus FPM - Blade Templating - Eloquent ORM - Vite Built Assets] in app
    service queueWorker(server)[Queue Worker - Background Jobs - Email Notifications - Document Generation] in app

    service mysql(database)[MySQL Database - Asset Records - User Credentials - Maintenance Logs - Audit Trails] in data

    service authService(server)[Auth Service - Session Based Auth - Laravel Sanctum - CSRF Protection - Role Based Access] in security
    service cloudStorage(disk)[Cloud Storage and Backup - File Uploads - Maintenance Images - Generated Reports - Automated Backups] in security

    userDevice:R --> L:nginx
    nginx:R --> L:laravelApp
    laravelApp:R --> L:mysql
    laravelApp:B --> T:queueWorker
    laravelApp:B --> T:authService
    laravelApp:B --> T:cloudStorage
```

---

## Detailed Node Descriptions

### 1. Client Node — User Device (Laptop / Mobile)

| Component | Details |
|---|---|
| **Device** | Laptop, Desktop, or Mobile Device |
| **Interface** | Modern Web Browser (Chrome, Firefox, Safari, Edge) |
| **Roles** | Students, College Staff, Organization Staff, Administrators |
| **Access** | Desktop Launchers (pywebview/Electron) or direct browser access |
| **Protocol** | HTTPS (TLS 1.2+) |

### 2. Infrastructure / Web Server Node — Nginx

| Component | Details |
|---|---|
| **Server** | Nginx (Reverse Proxy) |
| **Responsibilities** | SSL/TLS termination, static asset serving, request proxying, rate limiting, gzip compression |
| **Protocol (Inbound)** | HTTPS (port 443) |
| **Protocol (Outbound)** | FastCGI to PHP-FPM |

### 3. Application / Backend Node — Laravel 12

| Component | Details |
|---|---|
| **Framework** | Laravel 12.x on PHP 8.2+ (FPM) |
| **Templating** | Blade (server-side rendered views) |
| **ORM** | Eloquent (database abstraction) |
| **Frontend Build** | Vite 7.x + Tailwind CSS 4.x |
| **Queue Worker** | Laravel Queue (database/Redis driver) for background jobs — email notifications, DOCX/PDF generation (PHPWord + LibreOffice headless) |
| **Protocol (to DB)** | MySQL protocol (TCP port 3306) |

### 4. Data Node — MySQL Database Server

| Component | Details |
|---|---|
| **Engine** | MySQL (InnoDB) |
| **Stored Data** | Asset records, facility maintenance requests, user credentials (bcrypt-hashed), session data, audit logs, role/permission mappings |
| **Protocol** | MySQL wire protocol over TCP (port 3306) |
| **Backup** | Scheduled mysqldump or binary log replication to cloud storage |

### 5. Security & External Services

#### 5a. Authentication / Authorization Service

| Component | Details |
|---|---|
| **Mechanism** | Session-based authentication (Laravel built-in) |
| **Token Support** | Laravel Sanctum for API token authentication; JWT (web-token/jwt-core) for desktop/mobile launcher gate verification |
| **Password Hashing** | bcrypt via Laravel Hash facade |
| **CSRF Protection** | Laravel CSRF middleware on all state-changing routes |
| **Authorization** | Role-based access control (Admin, College Staff, Organization Staff) enforced via middleware and Gate/Policy classes |
| **ID Obfuscation** | Hashids for public-facing resource identifiers |
| **Encryption** | defuse/php-encryption for sensitive data at rest |

#### 5b. Cloud Storage / Backup

| Component | Details |
|---|---|
| **Purpose** | Store uploaded maintenance report images, generated DOCX/PDF documents, and scheduled database backups |
| **Options** | Laravel Filesystem with S3-compatible driver (AWS S3, DigitalOcean Spaces, MinIO) or local NAS |
| **Protocol** | HTTPS (S3 API) or NFS/SMB for local storage |
| **Retention** | Configurable retention policy for backups |

---

## Communication Paths Summary

| From | To | Protocol | Description |
|---|---|---|---|
| User Device | Nginx | **HTTPS (TLS 1.2+)** | Encrypted client requests from browser or desktop/mobile launcher |
| Nginx | Laravel App (PHP-FPM) | **FastCGI** | Proxied application requests |
| Laravel App | MySQL | **MySQL Protocol (TCP 3306)** | Database queries via Eloquent ORM |
| Laravel App | Queue Worker | **Internal (database/Redis)** | Dispatched background jobs |
| Laravel App | Auth Service | **Internal (in-process)** | Session/token validation via Laravel middleware |
| Laravel App | Cloud Storage | **HTTPS (S3 API) / NFS** | File uploads and backup storage |

---

## Architecture Justification

This deployment architecture is specifically suited for an **internal university management system** like UAFMS for the following reasons:

### Security
- **HTTPS everywhere**: All client-server communication is encrypted via TLS, protecting sensitive maintenance request data and user credentials in transit.
- **Session-based authentication with CSRF protection**: Laravel's built-in session auth is battle-tested and ideal for server-rendered Blade applications where users interact through browsers. CSRF tokens prevent cross-site request forgery attacks.
- **Role-based access control**: Middleware-enforced RBAC ensures that students, college staff, organization staff, and administrators can only access their permitted resources.
- **Encryption at rest**: Sensitive fields are encrypted using `defuse/php-encryption`, and passwords are bcrypt-hashed — meeting university data protection requirements.
- **ID obfuscation**: Hashids prevents enumeration attacks on asset and request IDs.

### Reliability
- **Nginx as reverse proxy**: Provides load balancing capability, graceful failover, and efficient static asset serving — reducing load on the PHP application server.
- **Queue workers**: Background job processing (document generation, email notifications) is decoupled from the request lifecycle, preventing long-running tasks from degrading user experience.
- **Automated backups to cloud storage**: Scheduled database dumps and file backups ensure data can be recovered in case of hardware failure.
- **Proven stack**: Laravel + MySQL + Nginx is one of the most widely deployed and well-documented web stacks, ensuring access to community support and security patches.

### Data Consistency
- **MySQL with InnoDB**: ACID-compliant transactions ensure data integrity for critical operations like asset transfers, maintenance request state changes, and user permission updates.
- **Eloquent ORM with migrations**: Schema versioning via Laravel migrations ensures consistent database state across development, staging, and production environments.
- **Single source of truth**: Centralized MySQL database prevents data duplication and ensures all users see consistent, up-to-date information.

### Scalability (Future Growth)
- **Horizontal scaling**: The stateless Laravel application layer (with session storage in database/Redis) can be scaled horizontally behind Nginx load balancing if the university's user base grows.
- **Queue scaling**: Additional queue workers can be added independently to handle increased background job volume.
- **Cloud storage**: S3-compatible storage scales seamlessly for growing file upload volumes.

### Cost Efficiency
- **Open-source stack**: PHP, Laravel, MySQL, and Nginx are all free and open-source — ideal for a university with budget constraints.
- **Single-server capable**: The entire stack can run on a single server for initial deployment, with the option to distribute components across multiple servers as needed.
