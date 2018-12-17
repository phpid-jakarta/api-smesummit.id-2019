# API Pendaftaran Peserta

### Pastikan Anda telah memahami Encrypted Token Session. Jika belum silakan baca di sini <a href="https://github.com/phpid-jakarta/api-smesummit.id-2019/blob/docs/docs/token_concept.md">Konsep Encrypted Token Session</a>

## 1. API Endpoint
```
https://api.smesummit.id/participant_register.php
```

## 2. Daftar Method yang Tersedia
|No.| Nama Method | HTTP Method |
|---|-------------|-------------|
|1.|<a href="#21-get_token">get_token</a>|GET|
|2.|<a href="#22-submit">submit</a>|POST|


#### 2.1 get_token

`get_token` digunakan untuk menggenerate sebuah token yang nantinya akan dipakai untuk submit data.


```
https://api.smesummit.id/participant_register.php?action=get_token
```

##### Contoh Response Body
```json
{
    "status": "success",
    "data": {
        "token": "OOaO7Uf0xPheV20g0m8Hfg5QzbNH9KLNQMjiw3dJN0OaF3UpKx7KzXOm",
        "expired": 1545060763
    }
}
```

#### 2.2 submit
```
https://api.smesummit.id/participant_register.php?action=submit
```