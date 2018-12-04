# Konsep Encrypted Token Session

## 1. Client melakukan get_token ke API Server

Client melakukan request ke API Server dengan parameter action `get_token` menggunakan HTTP method `GET`.

<img src="https://raw.githubusercontent.com/phpid-jakarta/api-smesummit.id-2019/docs/docs/images/token_concept/1.jpg"/>

## 2. Server membuat token

Pada tahap ini server menghitung waktu 1 jam ke depan sebagai expired time dan melakukan generate string acak sebagai pendamping waktu expired. Kemudian disimpan dalam json dan diencrypt menggunakan application key.

<img src="https://raw.githubusercontent.com/phpid-jakarta/api-smesummit.id-2019/docs/docs/images/token_concept/2.jpg"/>

## 3. Server memberi response berisi token

Setelah membuat token, server akan merespon request dari client dengan isi token tersebut.

<img src="https://raw.githubusercontent.com/phpid-jakarta/api-smesummit.id-2019/docs/docs/images/token_concept/3.jpg"/>

## 4. Client mengirimkan submit data ke server disertai dengan token

Sesudah client mendapatkan token dari server, client dapat mengirim data ke server disertai dengan header `Authorization` dengan value `Bearer $token`. 

- Jika client mengirimkan data yang valid dengan token yang valid, maka request akan diterima dan mendapat response 200 OK.
- Jika client mengirimkan data bersama token yang telah melewati waktu expired, maka request akan ditolak dan mendapat response 400 Bad Request.
- Jika client mengirimkan data tanpa token, maka request akan ditolak dan mendapat response 401 Unauthorized.

<img src="https://raw.githubusercontent.com/phpid-jakarta/api-smesummit.id-2019/docs/docs/images/token_concept/4.jpg"/>

