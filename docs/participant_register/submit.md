# Participant Register
## submit

### Endpoint
```
https://api.smesummit.id/participant_register.php?action=submit
```

### HTTP Method
```
POST
```

### Kegunaan
Method `submit` digunakan untuk mengirimkan data peserta yang hendak mendaftar.


### Field yang harus dikirim (JSON)
|No.| Nama | Type | Accepted Pattern | Keterangan | 
|---|------|------|------------------|------------|
|1. |`name`| string |`/^[a-z\.\'\s]{3,255}$/i`| Nama pendaftar (nama orang). |
|2. |`company_name` | string |`/^[a-z0-9\-\.\'\s]{3,255}$/i`| Nama Perusahaan. |
|3. |`position`| string |`/^[\\a-z0-9\-\.\'\s]{3,255}$/i`| Jabatan orang tersebut dalam perusahaan yang didaftarkan. |
|4. |`company_sector`| string |`/^[a-z0-9\-\.\'\s]{3,255}$/i`| Sektor perusahaan. |
|5. |`coached_sector`| string |`/^[a-z0-9\-\.\'\s]{3,255}$/i`| Sektor yang hendak dipelajari (bener nggak sih?, CC @mazipan). |
|6. |`email`| string |`filter_var($i["email"], FILTER_VALIDATE_EMAIL)`|Email peserta. |
|7. |`phone`| string |`/^[0\+]\d{4,13}$/` or `/^\@[a-z0-9][a-z0-9\_]{3,25}[a-z0-9]$/i`| Nomor HP atau Username Telegram peserta. |
|8. |`problem_desc`| string |`/.{20,1024}/`| Deskripsi masalah yang dihadapi. |
|9. |`captcha`| string |`/[a-z0-9]/i`| Input captcha (baca di sini) |