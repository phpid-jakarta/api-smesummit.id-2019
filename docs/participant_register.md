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


### 2.1 get_token

#### 2.1.1 Endpoint
```
https://api.smesummit.id/participant_register.php?action=get_token
```

#### 2.1.2 Kegunaan
`get_token` digunakan untuk menggenerate sebuah token yang nantinya akan dipakai untuk submit data.


#### 2.1.3 Contoh Response Body
```json
{
    "status": "success",
    "data": {
        "token": "OOaO7Uf0xPheV20g0m8Hfg5QzbNH9KLNQMjiw3dJN0OaF3UpKx7KzXOm",
        "expired": 1545060763
    }
}
```

#### 2.1.4 Curl Example
```
curl https://api.smesummit.id/participant_register.php?action=get_token
```

### 2.2 submit
```
https://api.smesummit.id/participant_register.php?action=submit
```

## 3. Contoh code riil dapat dilihat di test case berikut ini
<a href="https://github.com/phpid-jakarta/api-smesummit.id-2019/blob/docs/tests/API/ParticipantRegisterTest.php">https://github.com/phpid-jakarta/api-smesummit.id-2019/blob/docs/tests/API/ParticipantRegisterTest.php</a>