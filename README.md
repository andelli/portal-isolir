# Portal Isolir

Repositori ini berisi konfigurasi **Docker Compose**, **Nginx**, dan **Bind9 (DNS)** untuk membangun server **Portal Isolir**.  
Tujuan dari portal ini adalah **mengalihkan trafik pengguna yang terisolasi ke halaman informasi khusus**, dengan cara melakukan override DNS pada domain-domain tertentu.

---

## 📂 Struktur Direktori

```text
.
├── docker-compose.yml          # Definisi layanan
└── nginx/                      # Konfigurasi Nginx
    ├── default.conf            # Virtual host Nginx
    └── html/                   # Halaman portal isolir
```

---

## 🚀 Layanan yang Disediakan

- **Nginx (Portal Web Server)**  
  - Menyediakan halaman isolir (redirect landing page).  
  - Berjalan pada port 80.  

- **Docker Compose**  
  - Menjalankan dan mengelola semua layanan dengan mudah.

---

## 📦 Dependencies

### 🔑 Dependencies Utama
1. **Docker**  
   - Dibutuhkan untuk menjalankan container.  
   - [Install Docker](https://docs.docker.com/get-docker/)  

2. **Docker Compose**  
   - Untuk orkestrasi multi-container (Nginx + Bind9).  
   - [Install Docker Compose](https://docs.docker.com/compose/install/)  

3. **Nginx**  
   - Reverse proxy & web server untuk menampilkan halaman isolir.  
   - Menggunakan image resmi `nginx:latest`.

### ⚙️ Dependencies Tambahan (Opsional)
- **iptables / iptables-persistent**  
  Untuk mengatur firewall dan redirect traffic HTTPS/QUIC ke portal isolir.  
  - Debian/Ubuntu:  
    ```bash
    apt install iptables iptables-persistent -y
    ```
  - CentOS/RHEL:  
    ```bash
    yum install iptables-services -y
    ```

- **systemd-resolved** (optional handling)  
  Harus dimatikan jika bentrok dengan Bind9 di port 53.

---

## 🔧 Persyaratan

- Linux server (Debian/Ubuntu/CentOS)  
- [Docker](https://docs.docker.com/get-docker/)  
- [Docker Compose](https://docs.docker.com/compose/install/)  
- Port:
  - `443` (TCP/UDP) untuk HTTPS  
  - `80` untuk HTTP  

---

## ⚙️ Instalasi & Menjalankan

1. **Clone repository**
   ```bash
   git clone https://github.com/andelli/portal-isolir.git
   cd portal-isolir
   ```

2. **Build & jalankan container**
   ```bash
   docker-compose up -d --build
   ```

3. **Cek container berjalan**
   ```bash
   docker ps
   ```

---

## 🌐 Cara Kerja

1. Client mencoba akses internet → melakukan DNS lookup.  
3. Client diarahkan ke Nginx → ditampilkan halaman **Portal Isolir** (`nginx/html/index.html`).  

---

# 📌 Konfigurasi Firewall MikroTik - Portal Isolir

Konfigurasi ini digunakan untuk memaksa semua trafik **HTTP/HTTPS/DNS** user yang masuk dalam daftar `USER_PORTAL` agar diarahkan ke server **Nginx captive portal**.  
Dengan aturan ini, meskipun user mencoba membuka situs lain (termasuk HTTPS/QUIC), mereka akan tetap masuk ke halaman portal isolir.

---

## 🔹 Aturan Firewall Dasar

```bash
# Izinkan user akses ke IP web tertentu via HTTPS
/ip firewall nat add action=masquerade chain=srcnat \
    comment="ISOLIR_ALLOW_HTTPS" \
    dst-address=<IP_WEB_HTTPS> dst-port=443 \
    out-interface-list=gateway protocol=tcp \
    src-address-list=<USER_PORTAL>

# Redirect HTTP user portal ke Nginx captive portal
/ip firewall nat add action=dst-nat chain=dstnat \
    comment="ISOLIR_REDIRECT_WEB" \
    dst-port=80 protocol=tcp \
    src-address-list=<USER_PORTAL> \
    to-addresses=<IP_NGINX_SERVER>

# Redirect HTTPS user portal ke Nginx captive portal
/ip firewall nat add action=dst-nat chain=dstnat \
    comment="ISOLIR_REDIRECT_HTTPS" \
    dst-port=443 protocol=tcp \
    src-address-list=<USER_PORTAL> \
    to-addresses=<IP_NGINX_SERVER>

# Izinkan DNS query (UDP)
/ip firewall nat add action=masquerade chain=srcnat \
    comment="ISOLIR_ALLOW-DNS" \
    dst-port=53 protocol=udp \
    src-address-list=<USER_PORTAL>

# Izinkan DNS query (TCP)
/ip firewall nat add action=masquerade chain=srcnat \
    comment="ISOLIR_ALLOW-DNS" \
    dst-port=53 protocol=tcp \
    src-address-list=<USER_PORTAL>


---

## 🔍 Troubleshooting

- **DNS tidak resolve**  
  Pastikan service lain tidak memakai port 53 (misalnya `systemd-resolved`).  
  Bisa disable dengan:
  ```bash
  systemctl stop systemd-resolved
  systemctl disable systemd-resolved
  ```

- **Nginx tidak tampil**  
  Cek log container:
  ```bash
  docker logs portal_nginx
  ```

---

