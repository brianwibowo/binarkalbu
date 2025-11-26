# 📋 Production Deployment Guide - Manual Copy-Paste Method

> Panduan untuk deploy ke production dengan copy-paste file manual via cPanel Terminal

---

## 📂 Files yang Perlu Dicopy ke Production

Commit: `619a489e` (Nov 26, 2025)

### **CRITICAL - File yang HARUS di-copy:**

1. **`app/Models/ClientSession.php`** - Model dengan accessor baru
2. **`app/Filament/Resources/ClientResource/RelationManagers/SessionsRelationManager.php`** - Form & Table baru
3. **`database/migrations/2025_11_26_125138_change_medical_record_path_to_json.php`** - MIGRATION BARU (PENTING!)
4. **`app/Console/Commands/DebugSessionFiles.php`** - Debug command (optional)

---

## 🚀 Step-by-Step Deployment

### **STEP 1: Backup Database Production**
⚠️ **PENTING!** Sebelum migration:

```bash
# Login ke cPanel Terminal
# Backup database
mysqldump -u username -p databasename > backup_$(date +%Y%m%d).sql
```

Simpan file backup di tempat aman!

---

### **STEP 2: Copy Files ke Production**

#### Option A: Via FTP/File Manager (cPanel)
1. Buka cPanel → File Manager
2. Navigasi ke folder production
3. Upload/replace files:
   - `app/Models/ClientSession.php`
   - `app/Filament/Resources/ClientResource/RelationManagers/SessionsRelationManager.php`
   - `database/migrations/2025_11_26_125138_change_medical_record_path_to_json.php`
   - `app/Console/Commands/DebugSessionFiles.php`

#### Option B: Via Terminal (lebih cepat)
Dari local machine:

```bash
# Copy files ke production server
scp -r app/Models/ClientSession.php user@production-server:/path/to/app/app/Models/
scp -r app/Filament/Resources/ClientResource/RelationManagers/SessionsRelationManager.php user@production-server:/path/to/app/app/Filament/Resources/ClientResource/RelationManagers/
scp -r database/migrations/2025_11_26_125138_change_medical_record_path_to_json.php user@production-server:/path/to/app/database/migrations/
scp -r app/Console/Commands/DebugSessionFiles.php user@production-server:/path/to/app/app/Console/Commands/
```

---

### **STEP 3: Login ke Production Terminal & Run Commands**

```bash
# Login via cPanel Terminal (atau SSH)
# Navigate ke production folder
cd /home/username/public_html/binarkalbu
# atau
cd /path/to/laravel/app
```

---

### **STEP 4: Run Migration**

```bash
php artisan migrate
```

**Output yang diharapkan:**
```
Migration table created successfully.
Migrating: 2025_11_26_125138_change_medical_record_path_to_json
Migrated:  2025_11_26_125138_change_medical_record_path_to_json
```

---

### **STEP 5: Clear Cache**

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
```

---

### **STEP 6: Verify Deployment**

```bash
php artisan debug:session-files
```

**Output yang diharapkan:**
```
Session 4: ["medical-records/...", "medical-records/...", ...]
```

---

## ✨ Apa yang Berubah di Production

| Feature | Before | After |
|---------|--------|-------|
| File Upload | Single file saja | Multiple files (array) |
| Table Count | Salah, selalu 1 | Benar (3 File, 2 File, dll) |
| Edit Form | Loading 1 menit | Load dalam 5 detik |
| Preview | Tidak ada | Grid gambar + file list |
| Download | Single file | Single atau ZIP semua |

---

## ⚠️ Jika Ada Error

### Error 1: "Migration table not found"
```bash
php artisan migrate:install
php artisan migrate
```

### Error 2: "File already exists"
Migration sudah pernah dijalankan? Cek:
```bash
php artisan migrate:status
```

### Error 3: "Invalid JSON text"
Ada data lama yang corrupt. Migration sudah handle, tapi jika error:
```bash
# Clear data lama
mysql> UPDATE client_sessions SET medical_record_path = NULL;

# Kemudian migrate lagi
php artisan migrate:refresh --step=1
```

### Error 4: "Permission denied"
Storage folder tidak writable:
```bash
chmod -R 755 storage/
chmod -R 755 storage/app/public/medical-records/
```

---

## 🔄 Rollback (If Needed)

Jika perlu revert ke state sebelumnya:

```bash
# Rollback migration (undo changes)
php artisan migrate:rollback --step=1

# Delete/backup migration file di production
rm database/migrations/2025_11_26_125138_change_medical_record_path_to_json.php

# Restore old files:
# - Copy ClientSession.php versi lama
# - Copy SessionsRelationManager.php versi lama
# - Clear cache
php artisan optimize:clear
```

---

## ✅ Post-Deployment Testing

Di Admin Panel Production:

1. ✓ Buka Client → Sessions
2. ✓ Edit session existing → FileUpload load cepat?
3. ✓ Klik "Lihat" → Preview gambar muncul?
4. ✓ Klik file link → Gambar/file terbuka?
5. ✓ Upload file baru → Tersimpan?
6. ✓ Lihat table → File count benar (3 File, 2 File)?

---

## 📝 Important Notes

- **ALWAYS BACKUP DATABASE** sebelum migration!
- **Copy semua 4 files** - jangan skip migration file!
- **Run migration command** - data tidak otomatis convert
- **Clear cache** - production harus reload code terbaru
- **Test di staging dulu** - jika ada staging environment

---

## 📋 Quick Checklist

Before Deploy:
- [ ] Backup database production
- [ ] Copy 4 files ke production
- [ ] Verify files sudah ter-copy (via File Manager)

During Deploy:
- [ ] Run `php artisan migrate`
- [ ] Run `php artisan optimize:clear`
- [ ] Run `php artisan debug:session-files`

After Deploy:
- [ ] Test table view (file count)
- [ ] Test edit form (loading speed)
- [ ] Test modal preview (gambar + link)
- [ ] Test upload (file tersimpan)
- [ ] Monitor `storage/logs/laravel.log` (ada error?)

