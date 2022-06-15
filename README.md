# FileSystem API for MultiService

## Documentation

Documentation from Postman [click here](https://documenter.getpostman.com/view/19496704/UzBgvq4B)
 
 
## Installation

Setup file .env or rename **.env.example** to **.env**. 

Fill DB_DATABASE, DB_USERNAME and DB_PASSWORD.

Fill FTP_HOST, FTP_USERNAME, FTP_PASSWORD, FTP_URL.

if you have done the above, then just follow the steps below on command prompt.
```bash
  php artisan migrate:fresh
  php artisan serve
```
    

## API Reference


## Create Token

```http
  POST {{base_url}}/api/create-token
```

#### Body "Form Data"
| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `name` | `string` | **Required**. Name App |
| `domain` | `string` | **Required**. Domain url, not using http:// or https:// |


## Upload Files

```http
  POST {{base_url}}/api/upload
```

#### Body "Form Data"
| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `token` | `string` | **Required**. Token from App services register |
| `file` | `file` | **Required**. File upload |

## List Files

```http
  GET {{base_url}}/api/list/
```

| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `token` | `string` | **Required**. Token from App services register |
| `p`      | `integer` | **Optional**. total data per page for pagination |


## Rename File

```http
  POST {{base_url}}/api/rename
```

#### Body "Form Data"
| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `token` | `string` | **Required**. Token from App services register |
| `file_id` | `string` | **Required**. File_id to rename file |
| `name` | `string` | **Required**. New name file to rename |


## Delete File

```http
  POST {{base_url}}/api/delete
```

#### Body "Form Data"
| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `token` | `string` | **Required**. Token from App services register |
| `file_id` | `string` | **Required**. File_id to delete file |


## Search Files

```http
  GET {{base_url}}/api/search
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `token` | `string` | **Required**. Token from App services register |
| `q` | `string` | **Required**. Name file to search file in database |


## Filter Files

```http
  GET {{base_url}}/api/filter
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `token` | `string` | **Required**. Token from App services register |
| `f` | `string` | **Required**. filter name. example: extension, mime_type, disk, etc. |
| `search` | `string` | **Required**. Search name filter, example: if (f=extension) {search=mp4}, etc. |


## Resize Image

```http
  GET {{base_url}}/api/image/{app_id}
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `width` | `integer` | **Optional**. Size width you want to resize image |
| `height` | `integer` | **Optional**. Size height you want to resize image |

## Download File

```http
  GET {{base_url}}/api/download/{app_id}
```
