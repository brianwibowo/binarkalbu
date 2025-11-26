# 🚀 QUICK REFERENCE - Manual Deployment to Production

## 📝 Files to Copy (4 files)

```
✓ app/Models/ClientSession.php
✓ app/Filament/Resources/ClientResource/RelationManagers/SessionsRelationManager.php
✓ database/migrations/2025_11_26_125138_change_medical_record_path_to_json.php
✓ app/Console/Commands/DebugSessionFiles.php
```

---

## 🎯 Production Terminal Commands (Copy-Paste)

### 1️⃣ Backup Database (PENTING!)
```bash
mysqldump -u username -p databasename > backup_$(date +%Y%m%d).sql
```

### 2️⃣ Navigate to Production Folder
```bash
cd /home/username/public_html/binarkalbu
```

### 3️⃣ Run Migration
```bash
php artisan migrate
```

### 4️⃣ Clear Cache
```bash
php artisan optimize:clear && php artisan config:cache && php artisan route:cache
```

### 5️⃣ Verify (Optional)
```bash
php artisan debug:session-files
```

---

## ✨ Done! Testing:

- [ ] Table view - file count benar?
- [ ] Edit form - load cepat?
- [ ] Preview modal - gambar muncul?
- [ ] File link - bisa buka?
- [ ] Upload - bisa simpan?

---

## ❌ Error? Rollback:

```bash
php artisan migrate:rollback --step=1
```

