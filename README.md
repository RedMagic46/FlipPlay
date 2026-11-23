# FlipPlay REST API - Demo Modul 4 Pemrograman Web

RESTful API resmi platform hiburan digital **FlipPlay** yang dikembangkan menggunakan **Laravel 12**. Project ini merupakan kelanjutan dari simulasi OOP berbasis terminal pada Modul 3, yang kini ditransformasikan menjadi arsitektur REST API dengan basis data relasional sungguhan, relasi Eloquent Many-to-Many, Form Request Validation, API Resource, Custom Actions, serta pengujian komprehensif Postman.

---

## 📌 Pemenuhan Ketentuan Tugas & Aspek Penilaian

| Aspek Penilaian | Status | Implementasi & File Rujukan |
| :--- | :---: | :--- |
| **Project Laravel 12 & Git Repository** | ✅ | Menggunakan **Laravel 12.73.0** (PHP 8.3) dengan inisialisasi Git repository di folder `Modul4`. |
| **Eloquent Relationship (Many-to-Many)** | ✅ | Relasi `belongsToMany` antara entitas **Media** dan **User** (`users` & `media`) melalui pivot table `media_user` ([Media.php](file:///d:/Naufal%20Muammar/Semester%205/Pemrograman%20Web/Praktikum/Demo/Modul4/app/Models/Media.php#L45-L53), [User.php](file:///d:/Naufal%20Muammar/Semester%205/Pemrograman%20Web/Praktikum/Demo/Modul4/app/Models/User.php#L50-L58)). |
| **Migration & Struktur Tabel Benar** | ✅ | Migration untuk tabel `media`, modifikasi `users` (`membership_type`), dan pivot `media_user` lengkap dengan foreign key constraints, `cascadeOnDelete`, dan unique composite index ([database/migrations](file:///d:/Naufal%20Muammar/Semester%205/Pemrograman%20Web/Praktikum/Demo/Modul4/database/migrations)). |
| **CRUD API Lengkap (Media)** | ✅ | Method CRUD lengkap (`index`, `show`, `store`, `update`, `destroy`) pada [MediaController.php](file:///d:/Naufal%20Muammar/Semester%205/Pemrograman%20Web/Praktikum/Demo/Modul4/app/Http/Controllers/Api/MediaController.php) dengan HTTP status code presisi (200, 201, 404, 422). |
| **Form Request Validation & Error 422** | ✅ | Menggunakan [StoreMediaRequest.php](file:///d:/Naufal%20Muammar/Semester%205/Pemrograman%20Web/Praktikum/Demo/Modul4/app/Http/Requests/StoreMediaRequest.php), [UpdateMediaRequest.php](file:///d:/Naufal%20Muammar/Semester%205/Pemrograman%20Web/Praktikum/Demo/Modul4/app/Http/Requests/UpdateMediaRequest.php), dan [StoreUserCollectionRequest.php](file:///d:/Naufal%20Muammar/Semester%205/Pemrograman%20Web/Praktikum/Demo/Modul4/app/Http/Requests/StoreUserCollectionRequest.php) dengan validasi field wajib, unik, dan format numerik. |
| **API Resource (Format Respons Konsisten)** | ✅ | Menggunakan [MediaResource.php](file:///d:/Naufal%20Muammar/Semester%205/Pemrograman%20Web/Praktikum/Demo/Modul4/app/Http/Resources/MediaResource.php) dan [UserResource.php](file:///d:/Naufal%20Muammar/Semester%205/Pemrograman%20Web/Praktikum/Demo/Modul4/app/Http/Resources/UserResource.php) dengan penerapan method `whenLoaded()` dan `whenPivotLoaded()`. |
| **Routing API (`routes/api.php`)** | ✅ | Seluruh endpoint terdaftar rapi pada [routes/api.php](file:///d:/Naufal%20Muammar/Semester%205/Pemrograman%20Web/Praktikum/Demo/Modul4/routes/api.php) dan diverifikasi melalui `php artisan route:list`. |
| **Custom Action di luar CRUD Standar** | ✅ | Endpoint penambahan media ke koleksi pengguna (`attach`) dengan validasi anti-duplikasi, serta penghapusan media dari koleksi (`detach`) pada [UserCollectionController.php](file:///d:/Naufal%20Muammar/Semester%205/Pemrograman%20Web/Praktikum/Demo/Modul4/app/Http/Controllers/Api/UserCollectionController.php). |
| **Pengujian Lengkap (Happy & Failure Path)** | ✅ | Menyediakan file export Postman Collection v2.1.0 ([FlipPlay_API.postman_collection.json](file:///d:/Naufal%20Muammar/Semester%205/Pemrograman%20Web/Praktikum/Demo/Modul4/FlipPlay_API.postman_collection.json)) serta **19 Automated Feature Tests (149 Assertions, 100% PASS)** via PHPUnit ([tests/Feature](file:///d:/Naufal%20Muammar/Semester%205/Pemrograman%20Web/Praktikum/Demo/Modul4/tests/Feature)). |

---

## 🏗️ Arsitektur Relasi Database (ERD)

```text
+-------------------+             +-----------------------+             +--------------------+
|       users       |             |      media_user       |             |       media        |
+-------------------+             +-----------------------+             +--------------------+
| id (PK)           |<----------->| id (PK)               |<----------->| id (PK)            |
| name              |  1       N  | user_id (FK -> users) |  N       1  | title (UNIQUE)     |
| email (UNIQUE)    |             | media_id (FK -> media)|             | type               |
| membership_type   |             | notes                 |             | genre              |
| password          |             | created_at            |             | release_year       |
| created_at        |             | updated_at            |             | duration           |
| updated_at        |             | UNIQUE(user, media)   |             | description        |
+-------------------+             +-----------------------+             | created_at         |
                                                                        | updated_at         |
                                                                        +--------------------+
```

- **User Model**: Merepresentasikan pengguna FlipPlay (`Regular` atau `Platinum VIP`). Memiliki relasi `media(): BelongsToMany` melalui tabel pivot `media_user`.
- **Media Model**: Merepresentasikan katalog tontonan digital (Movie, Series, Anime). Memiliki relasi `users(): BelongsToMany` melalui tabel pivot `media_user`.
- **Pivot `media_user`**: Menyimpan koleksi tontonan pribadi masing-masing pengguna dengan metadata tambahan `notes` (catatan pribadi/kesan) dan timestamp `created_at` (waktu ditambahkan ke koleksi).

---

## 📋 Daftar Endpoint API

Semua endpoint diawali dengan prefix `/api`.

| Method | Endpoint | Deskripsi | Status Code |
| :--- | :--- | :--- | :---: |
| `GET` | `/api/media` | Menampilkan seluruh media (mendukung query filter `?search=...`, `?title=...`, `?genre=...`, `?type=...`) | `200 OK` |
| `POST` | `/api/media` | Menambahkan media baru ke sistem FlipPlay | `201 Created` / `422` |
| `GET` | `/api/media/{id}` | Menampilkan detail satu media beserta pengguna yang mengoleksinya (`whenLoaded('users')`) | `200 OK` / `404` |
| `PATCH` | `/api/media/{id}` | Memperbarui informasi data media | `200 OK` / `422` / `404` |
| `DELETE` | `/api/media/{id}` | Menghapus media dari katalog sistem | `200 OK` / `404` |
| `GET` | `/api/users/{user}/collections` | Menampilkan seluruh koleksi media milik seorang pengguna beserta catatan pivot | `200 OK` / `404` |
| `POST` | `/api/users/{user}/collections` | **[Custom Action]** Menambahkan media ke koleksi pengguna (`attach`) dengan validasi anti-duplikasi | `201 Created` / `422` |
| `DELETE` | `/api/users/{user}/collections/{media}` | **[Custom Action]** Menghapus media dari koleksi pengguna (`detach`) | `200 OK` / `404` |
| `GET` | `/api/users` | Menampilkan daftar seluruh pengguna FlipPlay dan total koleksinya | `200 OK` |
| `GET` | `/api/users/{id}` | Menampilkan detail profil pengguna dan daftar koleksinya | `200 OK` / `404` |
| `POST` | `/api/users` | Mendaftarkan akun pengguna baru | `201 Created` / `422` |

---

## 🚀 Panduan Menjalankan Project

### 1. Kebutuhan Sistem
- PHP >= 8.2 (ekstensi `pdo_sqlite`, `curl`, `mbstring`, `openssl` aktif)
- Composer >= 2.0

### 2. Langkah Instalasi & Menjalankan Server
Buka terminal pada direktori `Modul4`:

```bash
# 1. Masuk ke direktori Modul4
cd Modul4

# 2. Salin environment file (jika belum ada)
cp .env.example .env

# 3. Generate Application Key (sudah siap secara default)
php artisan key:generate

# 4. Jalankan migrasi dan seeder data awal FlipPlay
php artisan migrate:fresh --seed

# 5. Jalankan development server
php artisan serve
```

Server API akan berjalan di: `http://127.0.0.1:8000`.

---

## 🧪 Panduan Pengujian dengan Postman

Telah disediakan file Postman Collection siap pakai:
📂 **`FlipPlay_API.postman_collection.json`**

### Cara Import ke Postman:
1. Buka aplikasi **Postman**.
2. Klik tombol **Import** di pojok kiri atas.
3. Pilih file `Modul4/FlipPlay_API.postman_collection.json`.
4. Collection `FlipPlay REST API - Modul 4` akan muncul di panel kiri Postman.
5. Pastikan server lokal Anda aktif (`php artisan serve`). Variable `baseUrl` sudah diset otomatis ke `http://127.0.0.1:8000/api`.

### Rangkuman Skenario Pengujian Postman:

#### 1. Folder Media CRUD Endpoints
- **1.1 Get All Media (Happy Path)**: Mengambil seluruh katalog (HTTP 200).
- **1.2 Search Media by Title (Happy Path)**: `GET /api/media?search=Inception` memfilter hasil pencarian.
- **1.3 Filter Media by Genre (Happy Path)**: `GET /api/media?genre=Sci-Fi` menyaring berdasarkan genre.
- **1.4 Create New Media (Happy Path - 201)**: Menambahkan film "Dune: Part Two" (HTTP 201 Created).
- **1.5 Create Media - Empty Title (Failure Path - 422)**: Payload dengan judul kosong `""` ditolak dengan pesan validasi *"Judul media wajib diisi."*.
- **1.6 Create Media - Duplicate Title (Failure Path - 422)**: Menambahkan "Interstellar" ditolak dengan pesan validasi *"Judul media sudah terdaftar di sistem FlipPlay."*.
- **1.7 Get Media Detail with Users (Happy Path - 200)**: Mengambil detail media ID 1 beserta relasi pengguna yang mengoleksinya (`whenLoaded('users')`).
- **1.8 Get Media Detail - ID Tidak Ada (Failure Path - 404)**: Mengakses ID 99999 menghasilkan error 404 Not Found.
- **1.9 Update Media (Happy Path - 200)**: Mengubah judul & durasi pada media ID 5 menjadi versi 4K Remastered.
- **1.10 Update Media - Duplicate Title (Failure Path - 422)**: Mengubah nama media ID 5 menjadi "Interstellar" ditolak karena nama sudah digunakan oleh media ID 1.
- **1.11 Delete Media (Happy Path - 200)**: Menghapus media ID 5 dari sistem.

#### 2. Folder User Collection & Custom Actions
- **2.1 Menampilkan Koleksi Media Pengguna (Happy Path - 200)**: `GET /api/users/1/collections` menampilkan koleksi tontonan Naufal beserta catatan pivot (`notes` dan `added_at`).
- **2.2 Custom Action: Tambah Media ke Koleksi (Happy Path - 201)**: `POST /api/users/1/collections` menambahkan Oppenheimer ke koleksi Naufal.
- **2.3 Custom Action: Tambah Media yang Sama 2x (Failure Path - 422)**: Menambahkan kembali media yang sudah ada di koleksi pengguna menghasilkan respons error 422: *"Media sudah ada di dalam koleksi pengguna."*.
- **2.4 Custom Action: Tambah Media ID Fiktif (Failure Path - 422)**: Mengirimkan `media_id: 99999` ditolak oleh validasi `exists:media,id`.
- **2.5 Custom Action: Hapus Media dari Koleksi (Happy Path - 200)**: `DELETE /api/users/1/collections/2` melepaskan relasi Inception dari koleksi Naufal via `detach()`.
- **2.6 Custom Action: Hapus Media yang Bukan Koleksinya (Failure Path - 404)**: Mencoba menghapus media yang tidak ada di daftar koleksi menghasilkan 404: *"Media tidak ditemukan di dalam koleksi pengguna."*.

---

## ⚡ Eksekusi Automated Test Suite

Untuk memverifikasi keandalan seluruh endpoint secara otomatis, jalankan test suite PHPUnit:

```bash
cd Modul4
php artisan test
```

### Hasil Uji Otomatis:
```text
   PASS  Tests\Unit\ExampleTest
  ✓ that true is true

   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response

   PASS  Tests\Feature\MediaApiTest
  ✓ can get all media list                                                        0.33s  
  ✓ can filter media by search keyword                                            0.03s  
  ✓ can filter media by genre                                                     0.03s  
  ✓ can create new media happy path                                               0.04s  
  ✓ cannot create media with empty title failure path                             0.03s  
  ✓ cannot create media with duplicate title failure path                         0.03s  
  ✓ can show media detail with users relation                                     0.03s  
  ✓ show non existent media returns 404                                           0.04s  
  ✓ can update media happy path                                                   0.03s  
  ✓ cannot update media with duplicate title failure path                         0.04s  
  ✓ can delete media happy path                                                   0.03s  

   PASS  Tests\Feature\UserCollectionApiTest
  ✓ can get user collection list                                                  0.03s  
  ✓ can add media to user collection happy path                                   0.03s  
  ✓ cannot add same media twice to user collection failure path                   0.03s  
  ✓ cannot add non existent media to user collection failure path                 0.03s  
  ✓ can remove media from user collection happy path                              0.03s  
  ✓ cannot remove media not in user collection failure path                       0.02s  

  Tests:    19 passed (149 assertions)
  Duration: 1.36s
```
Semua 19 skenario pengujian berhasil 100% tanpa error!
