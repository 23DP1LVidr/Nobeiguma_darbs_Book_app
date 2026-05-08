# BookSwap

BookSwap ir sociāla tīmekļa platforma grāmatu lasītājiem Latvijā. Tā apvieno personīgo grāmatu bibliotēku, grāmatu apmaiņu, sociālo plūsmu un ziņapmaiņu vienā lietotnē.

## Funkcionalitāte

- Lietotāju reģistrācija, pieslēgšanās un profila rediģēšana
- Personīgās bibliotēkas pārvaldība
- Grāmatu pievienošana, rediģēšana un dzēšana
- Publiski pieejamo grāmatu pārlūkošana apmaiņai
- Apmaiņas pieprasījumu izveide, apstiprināšana, noraidīšana, pabeigšana un atcelšana
- Sarunas un ziņas starp lietotājiem
- Sociālā plūsma ar ierakstiem, attēliem, patīk atzīmēm un komentāriem
- Lietotāju profilu apskate
- Administratora sadaļa lietotāju, grāmatu un apmaiņu pārvaldībai
- Tumšā/gaišā režīma iestatījumi

## Tehnoloģijas

### Frontend

- Vue 3
- Vue Router
- Vuetify
- Axios
- Vite
- Material Design Icons

### Backend

- Laravel 12
- Laravel Sanctum autentifikācija
- REST API
- Laravel migrations
- PHP 8.2+

### Datubāze

Datubāze tiek konfigurēta `backend/.env` failā. `backend/.env.example` pēc noklusējuma izmanto SQLite, bet projektu var konfigurēt arī MySQL datubāzei, nomainot `DB_*` vērtības.

## Projekta Struktūra

```text
Nobeiguma_darbs_Book_app/
├── backend/                 # Laravel API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/
│   │   │   └── Middleware/
│   │   ├── Models/
│   │   └── Support/
│   ├── database/migrations/
│   ├── routes/
│   │   ├── api.php
│   │   └── web.php
│   └── composer.json
│
├── frontend/                # Vue lietotne
│   ├── src/
│   │   ├── components/
│   │   ├── plugins/
│   │   ├── router/
│   │   ├── stores/
│   │   └── views/
│   ├── package.json
│   └── vite.config.js
│
└── README.md
```

## Galvenās API Sadaļas

API maršruti atrodas `backend/routes/api.php`.

- `POST /api/register` - reģistrācija
- `POST /api/login` - pieslēgšanās
- `GET /api/me` - pašreizējais lietotājs
- `PUT /api/profile` - profila atjaunināšana
- `GET /api/books` - lietotāja grāmatas
- `POST /api/books` - pievienot grāmatu
- `PUT /api/books/{book}` - atjaunināt grāmatu
- `DELETE /api/books/{book}` - dzēst grāmatu
- `GET /api/books/available` - citu lietotāju pieejamās grāmatas
- `GET /api/exchanges` - lietotāja apmaiņas
- `POST /api/exchanges` - izveidot apmaiņas pieprasījumu
- `PUT /api/exchanges/{exchange}/accept` - apstiprināt apmaiņu
- `PUT /api/exchanges/{exchange}/reject` - noraidīt apmaiņu
- `PUT /api/exchanges/{exchange}/complete` - pabeigt apmaiņu
- `GET /api/conversations` - sarunas
- `GET /api/conversations/{conversation}/messages` - sarunas ziņas
- `POST /api/conversations/{conversation}/messages` - nosūtīt ziņu
- `GET /api/posts` - plūsmas ieraksti
- `POST /api/posts` - izveidot ierakstu
- `POST /api/posts/{post}/like` - patīk atzīme
- `POST /api/posts/{post}/comments` - komentārs
- `DELETE /api/posts/{post}` - dzēst ierakstu
- `GET /api/admin/*` - administratora darbības

## Palaišana

### Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan serve
```

Laravel API pēc noklusējuma darbojas:

```text
http://127.0.0.1:8000
```

### Frontend

Atver jaunu termināli:

```bash
cd frontend
npm install
npm run dev
```

Vite izdrukās lokālo frontend adresi, parasti:

```text
http://localhost:5173
```

## Build Un Testi

Frontend produkcijas build:

```bash
cd frontend
npm run build
```

Backend testiem:

```bash
cd backend
php artisan test
```

## Piezīmes Izstrādei

- Frontend pašlaik izmanto API adresi `http://127.0.0.1:8000/api`.
- Lietotāja sesija frontend pusē tiek glabāta `localStorage` ar Sanctum tokenu.
- Galvenais autorizētās lietotnes izkārtojums sastāv no `AppHeader`, `AppSidebar`, centrālās satura zonas un labās sānu joslas.
- Plūsmas, bibliotēkas, apmaiņu un ziņu lapas ir sadalītas mazākos komponentos, lai `views/` faili paliktu vieglāk uzturami.
