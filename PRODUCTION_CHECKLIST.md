# 🎯 Quick Deployment Checklist

## Sebelum di-deploy ke Production

### Code
- [x] Semua code sudah di-commit ke main branch
- [x] Code sudah di-push ke GitHub
- [x] Migration file sudah included

### Testing Local
- [x] Table column count file: **WORKING** ✅
- [x] Modal preview gambar: **WORKING** ✅
- [x] Edit form loading: **FAST** ✅
- [x] File download: **WORKING** ✅
- [x] File URL: **CORRECT** ✅

---

## 📋 Deployment Execution

### Via cPanel Terminal (atau SSH):

```bash
# 1. Navigate to production folder
cd /home/username/public_html/binarkalbu  # atau path sesuai production

# 2. Pull latest code
git pull origin main

# 3. Run migration
php artisan migrate

# 4. Clear cache
php artisan optimize:clear

# 5. (Optional) Test
php artisan debug:session-files
```

---

## ⏱️ Perkiraan Waktu

- Pull code: **< 1 menit**
- Composer update (jika dijalankan): **3-5 menit**
- Migration: **< 1 menit**
- Cache clear: **< 1 menit**
- **Total: ~5-10 menit** (jika tanpa composer)

---

## ✅ Post-Deployment Verification

```
Di Admin Panel:
1. Buka Client → Sessions
2. Edit session existing → FileUpload load cepat? ✓
3. Klik "Lihat" → Preview gambar muncul? ✓
4. Klik file link → Gambar/file terbuka? ✓
5. Upload file baru → Tersimpan? ✓
6. Lihat table → File count benar? ✓
```

---

## 🆘 Troubleshooting

### Issue: "File fisik tidak ditemukan di server"
**Solution**: 
```bash
ls -la storage/app/public/medical-records/
chmod -R 755 storage/app/public/medical-records/
```

### Issue: "Column data too long for column 'medical_record_path'"
**Solution**: Migration sudah handle ini, pastikan:
```bash
php artisan migrate --force
```

### Issue: Migration error "Invalid JSON"
**Solution**: Data lama bukan JSON, migration handle dengan clear data:
```bash
php artisan migrate:refresh --step=1
```

---

## 📞 Support

Jika ada issue saat deployment:
1. Check error log: `tail -100 storage/logs/laravel.log`
2. Run debug command: `php artisan debug:session-files`
3. Check storage permissions: `chmod -R 755 storage/`

