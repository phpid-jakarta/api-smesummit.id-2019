# Participant Register
## get_token

### Endpoint
```
https://api.smesummit.id/participant_register.php?action=get_token
```

### Kegunaan
`get_token` digunakan untuk menggenerate sebuah token yang nantinya akan dipakai untuk submit data.


### Contoh Response Body
```json
{
    "status": "success",
    "data": {
        "token": "OOaO7Uf0xPheV20g0m8Hfg5QzbNH9KLNQMjiw3dJN0OaF3UpKx7KzXOm",
        "expired": 1545060763
    }
}
```

### Curl Example
```bash
curl https://api.smesummit.id/participant_register.php?action=get_token
```

####<a href="https://github.com/phpid-jakarta/api-smesummit.id-2019/blob/docs/docs/participant_register.md">Kembali ke participant_register.md</a>