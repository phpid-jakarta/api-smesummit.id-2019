# Participant Register
## get_token

### Endpoint
```
https://api.smesummit.id/participant_register.php?action=get_token
```

### 2.1.2 Kegunaan
`get_token` digunakan untuk menggenerate sebuah token yang nantinya akan dipakai untuk submit data.


### 2.1.3 Contoh Response Body
```json
{
    "status": "success",
    "data": {
        "token": "OOaO7Uf0xPheV20g0m8Hfg5QzbNH9KLNQMjiw3dJN0OaF3UpKx7KzXOm",
        "expired": 1545060763
    }
}
```

### 2.1.4 Curl Example
```
curl https://api.smesummit.id/participant_register.php?action=get_token
```