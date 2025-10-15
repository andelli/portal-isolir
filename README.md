# Portal Isolir

Repositori ini berisi konfigurasi **Docker Compose**, **Nginx**, dan **Bind9 (DNS)** untuk membangun server **Portal Isolir**.  
Tujuan dari portal ini adalah **mengalihkan trafik pengguna yang terisolasi ke halaman informasi khusus**, dengan cara melakukan override DNS pada domain-domain tertentu.

---

## 📂 Struktur Direktori

```text
.
├── bind/                       # Konfigurasi Bind9
│   ├── Dockerfile              # Image custom bind9
│   ├── entrypoint.sh           # Entrypoint bind9 (set IP portal)
│   ├── named.conf              # Konfigurasi utama
│   ├── named.conf.local        # Definisi zone
│   ├── named.conf.options      # Opsi global DNS
│   └── zones/                  # File zone
│       ├── override.zone
│       ├── override.zone.tmpl
│       ├── portal.local.zone
│       └── portal.local.zone.tmpl
├── docker-compose.yml          # Definisi layanan
└── nginx/                      # Konfigurasi Nginx
    ├── default.conf            # Virtual host Nginx
    └── html/                   # Halaman portal isolir
        ├── index.html
        ├── no-wifi.png
        └── no-wifi1.png
```

---

## 🚀 Layanan yang Disediakan

- **Bind9 (DNS Override)**  
  - Melayani query DNS pada port 53 (TCP/UDP).  
  - Mengarahkan domain seperti:
    - `msftconnecttest.com`
    - `connectivitycheck.gstatic.com`
    - `portal.local`  
    ke **IP Portal** (`PORTAL_IP`).  

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

3. **Bind9** (di-build custom via `bind/Dockerfile`)  
   - DNS server yang digunakan untuk override domain.  
   - Dijalankan di dalam container `portal_bind9`.  
   - Dependencies dalam container (sudah dihandle oleh image `internetsystemsconsortium/bind9:9.18`):  
     - `bind9`
     - `bind9-utils`
     - `bind9-host`

4. **Nginx**  
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
  - `53` (TCP/UDP) untuk DNS  
  - `80` untuk HTTP  

---

## ⚙️ Instalasi & Menjalankan

1. **Clone repository**
   ```bash
   git clone https://github.com/andelli/portal-isolir.git
   cd portal-isolir
   ```

2. **Buat file `.env`**  
   Misalnya:
   ```env
   PORTAL_IP=10.10.10.1
   ```

   Nilai `PORTAL_IP` adalah IP server portal isolir.

3. **Build & jalankan container**
   ```bash
   docker-compose up -d --build
   ```

4. **Cek container berjalan**
   ```bash
   docker ps
   ```

---

## 🌐 Cara Kerja

1. Client mencoba akses internet → melakukan DNS lookup.  
2. Bind9 menangkap query untuk domain tertentu (`msftconnecttest.com`, dll).  
3. Bind9 mengarahkan domain tersebut ke `PORTAL_IP`.  
4. Client diarahkan ke Nginx → ditampilkan halaman **Portal Isolir** (`nginx/html/index.html`).  

---

## 🔥 Konfigurasi Firewall (iptables)

### Firewall Dasar

```bash
# Flush rules lama (opsional, hati-hati jika server produksi)
iptables -F
iptables -X

# Default policy: drop semua, allow sesuai kebutuhan
iptables -P INPUT DROP
iptables -P FORWARD DROP
iptables -P OUTPUT ACCEPT

# Allow loopback
iptables -A INPUT -i lo -j ACCEPT

# Allow SSH (ubah port sesuai konfigurasi)
iptables -A INPUT -p tcp --dport 22 -j ACCEPT

# Allow DNS (UDP & TCP port 53)
iptables -A INPUT -p udp --dport 53 -j ACCEPT
iptables -A INPUT -p tcp --dport 53 -j ACCEPT

# Allow HTTP (port 80)
iptables -A INPUT -p tcp --dport 80 -j ACCEPT

# Allow ICMP (ping)
iptables -A INPUT -p icmp -j ACCEPT

# Logging (opsional)
iptables -A INPUT -j LOG --log-prefix "DROP INPUT: " --log-level 4
```

### Redirect HTTPS/QUIC ke Portal

Untuk memastikan semua trafik HTTPS juga diarahkan ke portal, tambahkan aturan berikut di tabel NAT:

```bash
# Redirect semua TCP HTTPS (443) ke HTTP (80)
iptables -t nat -A PREROUTING -p tcp --dport 443 -j REDIRECT --to-ports 80

# Redirect semua UDP HTTPS (443/QUIC) ke HTTP (80)
iptables -t nat -A PREROUTING -p udp --dport 443 -j REDIRECT --to-ports 80
```

Cek aturan yang sudah aktif:

```bash
iptables -t nat -L PREROUTING -n -v
```

Contoh output:
```
Chain PREROUTING (policy ACCEPT)
 pkts bytes target     prot opt in     out     source               destination         
 ...  ...  REDIRECT   tcp  --  0.0.0.0/0       0.0.0.0/0           tcp dpt:443 redir ports 80
 ...  ...  REDIRECT   udp  --  0.0.0.0/0       0.0.0.0/0           udp dpt:443 redir ports 80
```

Dengan aturan ini, **semua request HTTPS/QUIC user akan dipaksa masuk ke portal isolir**, meskipun user mencoba membuka situs lain.

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

- **Bind9 error**  
  Pastikan konfigurasi zona valid:
  ```bash
  docker logs portal_bind9
  ```

---

