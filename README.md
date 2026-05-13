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

## Deploy: Vercel + Railway

Ieteicamais hostinga sadalījums šim projektam:

- `frontend/` publicē Vercel.
- `backend/` publicē Railway.
- Datubāzi izveido Railway kā MySQL servisu.

### 1. Railway Backend

Railway projektā izveido jaunu Laravel backend servisu no šī GitHub repozitorija.

Svarīgi iestatījumi:

```text
Root directory: backend
Builder: Nixpacks
Start command: tiek ņemts no backend/railway.json
```

Pievieno Railway MySQL datubāzi un backend servisam iestati production environment mainīgos. Paraugs atrodas:

```text
backend/.env.railway.example
```

Obligāti jāiestata:

```text
APP_KEY=base64:...
APP_URL=https://your-railway-backend-url.up.railway.app
DB_CONNECTION=mysql
DB_HOST=...
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
FRONTEND_URL=https://your-vercel-app.vercel.app
CORS_ALLOWED_ORIGINS=https://your-vercel-app.vercel.app
```

`APP_KEY` var ģenerēt lokāli:

```bash
cd backend
php artisan key:generate --show
```

Railway starta komanda automātiski palaiž migrācijas un Laravel serveri:

```bash
php artisan migrate --force
php artisan serve --host=0.0.0.0 --port=${PORT}
```

Piezīme par attēliem: pašreizējā sistēma saglabā augšupielādētos attēlus Laravel `storage/app/public`. Railway filesystem var nebūt pastāvīgs starp deploy reizēm, tāpēc stabilai production videi vajag Railway Volume vai ārēju failu glabātuvi, piemēram, S3-compatible storage.

### 2. Vercel Frontend

Vercel projektā importē šo pašu GitHub repozitoriju.

Vari deployot divos veidos.

Variants A: izmanto repozitorija sakni. Tad Vercel izmantos `vercel.json`:

```text
Install command: cd frontend && npm ci
Build command: cd frontend && npm run build
Output directory: frontend/dist
```

Variants B: Vercel iestati:

```text
Root directory: frontend
Framework preset: Vite
Build command: npm run build
Output directory: dist
```

Vercel environment variables:

```text
VITE_API_URL=https://your-railway-backend-url.up.railway.app/api
```

Paraugs atrodas:

```text
frontend/.env.production.example
```

Pēc Vercel deploy pabeigšanas nokopē Vercel domēnu un pievieno to Railway backend mainīgajos:

```text
FRONTEND_URL=https://your-vercel-app.vercel.app
CORS_ALLOWED_ORIGINS=https://your-vercel-app.vercel.app
```

## Piezīmes Izstrādei

- Frontend API adrese tiek konfigurēta ar `frontend/.env` vērtību `VITE_API_URL`. Ja tā nav norādīta, tiek izmantots `http://127.0.0.1:8000/api`.
- Lietotāja sesija frontend pusē tiek glabāta `localStorage` ar Sanctum tokenu.
- Galvenais autorizētās lietotnes izkārtojums sastāv no `AppHeader`, `AppSidebar`, centrālās satura zonas un labās sānu joslas.
- Plūsmas, bibliotēkas, apmaiņu un ziņu lapas ir sadalītas mazākos komponentos, lai `views/` faili paliktu vieglāk uzturami.
