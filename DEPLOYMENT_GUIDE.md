# 📋 Production Deployment Guide

## ✅ Changes yang di-deploy

Commit: `cb3ab189` - Fix medical record file upload (Nov 26, 2025)

### Files yang berubah:
1. `app/Models/ClientSession.php` - Added getMedicalRecordsAttribute accessor
2. `app/Filament/Resources/ClientResource/RelationManagers/SessionsRelationManager.php` - Optimized FileUpload & preview
3. `database/migrations/2025_11_26_125138_change_medical_record_path_to_json.php` - NEW migration
4. `app/Console/Commands/DebugSessionFiles.php` - NEW debug command

---

## 🚀 Production Deployment Steps (via cPanel Terminal)

### Step 1: SSH ke Production Server
```bash
# Login via cPanel terminal atau SSH
ssh user@production-domain.com
cd /path/to/laravel/app
```

### Step 2: Pull Latest Code
```bash
git pull origin main
```

### Step 3: Install Dependencies (if needed)
```bash
composer install --no-dev --optimize-autoloader
```

### Step 4: Run Migration
```bash
php artisan migrate
```

### Step 5: Clear Cache & Optimize
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 6: (Optional) Test Debug Command
```bash
php artisan debug:session-files
```

---

## ✨ What's New in Production

### Medical Record File Upload
- Files sekarang di-store sebagai **JSON array** (support multiple files)
- Table column menampilkan jumlah file yang benar
- Modal preview menampilkan grid gambar + list file dengan download link
- Edit form tidak loading lama (disabled preview, show file count)
- Relative URL digunakan (work di semua domain)

### Features:
✅ Upload multiple medical records per session
✅ Preview gambar dalam grid di modal
✅ Download individual file atau ZIP semua files
✅ Delete/replace files di edit form
✅ Fast loading (optimized FileUpload)

---

## 🔧 Rollback (If Needed)

Jika ada issue di production:

```bash
# Rollback migration
php artisan migrate:rollback --step=1

# Revert to previous commit
git reset --hard HEAD~1
git push origin main -f

# Clear cache
php artisan optimize:clear
```

---

## 📝 Notes

- **Storage Path**: Pastikan folder `storage/app/public/medical-records/` exist dan writable
- **Symbolic Link**: Pastikan `public/storage` symlink ke `storage/app/public`
- **Permissions**: Folder storage harus writable oleh web server user
- **Backup**: Selalu backup database sebelum migration!

---

## ✅ Post-Deployment Testing

1. Login ke admin panel
2. Buka Client → Sessions
3. Edit session existing → lihat apakah file terlihat dengan cepat
4. Klik "Lihat" → preview gambar + download link harus work
5. Upload file baru → harus tersimpan dalam format array di database

