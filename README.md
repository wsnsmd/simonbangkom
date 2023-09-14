# Doc
## Installation guide
### Install dependencies
```bash
git clone https://github.com/wsnsmd/simonbangkom.git
cd simonbangkom
cp .env.example .env
# If you are using api make IS_API_PROJECT=true in env file
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
# if public/storage folder is present in public folder then remove it.
php artisan storage:link 
npm install
```
### Run the app
```bash
npm run dev
```

# Api Documentation
## Authentication
- [x] Postman [collection](https://api.postman.com/collections/24461563-44d29f80-f3c6-443d-b974-673814076daa?access_key=PMAT-01GJA5RV70QHN3434V13CKFCCW)
