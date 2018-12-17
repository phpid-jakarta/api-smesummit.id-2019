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
|No.| Nama | Type | Keterangan | 
|---|------|------|------------|
|1. |`name`| string | Nama pendaftar (nama orang). |
|2. |`company_name` | string | Nama Perusahaan. |
|3. |`position`| string | Jabatan orang tersebut dalam perusahaan yang didaftarkan. |
|4. |`company_sector`| string | Sektor perusahaan. |
|5. |`coached_sector`| string | Sektor yang hendak dipelajari. |
|6. |`email`| string | Email peserta. |
|7. |`phone`| string | Nomor HP peserta. |
|8. |`problem_desc`| string | Deskripsi masalah yang dihadapi. |