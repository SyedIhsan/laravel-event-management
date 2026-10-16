# Event Management API

> **For learning purposes only.** This is a practice project I built to learn how to create a
> **REST API** with Laravel. It is not meant for production use.

A simple API where users can create events and register as attendees. There is no frontend —
every endpoint was tested with **Postman**.

## What I Learned

- **REST API with resource controllers** — `php artisan make:controller Api/EventController --api`
  and `Route::apiResource()` to get the full CRUD routes (index, show, store, update, destroy) in one line.
- **CRUD operations & data validation** — using `$request->validate()` with rules like
  `required`, `nullable`, `date`, and `after:start_time`.
- **API Resources** — `php artisan make:resource EventResource` to control exactly what the JSON
  response looks like, instead of dumping the whole model.
- **Pagination** — `->paginate()` on the query, which returns the data with `links` and `meta`.
- **Relation loading** — a reusable `CanLoadRelationships` trait so the client decides what to load
  with `?include=user,attendees`, using `whenLoaded()` in the resource to avoid N+1 queries.
- **Nested resources** — `Route::apiResource('events.attendees', ...)->scoped()` so attendees are
  always scoped to their parent event.
- **Authentication with Sanctum** — issuing a token on login with `createToken()`.
- **Protecting routes with tokens** — the `auth:sanctum` middleware on a route group, so reading
  events is public but creating/updating/deleting needs a token.
- **Automatically adding the token in Postman** — saving the token into a collection variable from
  the login request's test script, so every other request sends it automatically.
- **Revoking tokens on logout** — `$request->user()->tokens()->delete()`.
- **Authorization with Gates and Policies** — started with `Gate::define()` closures, then moved to
  `EventPolicy` / `AttendeePolicy` so only the owner can update or delete their event.
- **Custom Artisan command** — `app:send-event-reminders` to find events starting in the next 24 hours.
- **Task scheduling** — running that command daily from `routes/console.php`.
- **Notifications & email sending** — `EventReminderNotification` with a `MailMessage`.
- **Mailpit via Docker** — to catch and view the test emails locally instead of sending real ones.
- **Queues** — the notification implements `ShouldQueue`, so emails are sent by a worker in the
  background instead of blocking the request.
- **Rate limiting** — a named `api` limiter (60 requests/minute, per user or IP) applied with the
  `throttle:api` middleware.
- **JSON error responses** — `shouldRenderJsonWhen()` in `bootstrap/app.php`, so API errors never
  return an HTML page.
- **Factories & seeders** — `EventFactory`, `EventSeeder`, and `AttendeeSeeder` for fake test data.

## Endpoints

| Method | Endpoint                             | Auth | Description              |
| ------ | ------------------------------------ | ---- | ------------------------ |
| POST   | `/api/login`                         | No   | Get an API token         |
| POST   | `/api/logout`                        | Yes  | Revoke all tokens        |
| GET    | `/api/user`                          | Yes  | Current user             |
| GET    | `/api/events`                        | No   | List events (paginated)  |
| GET    | `/api/events/{id}`                   | No   | Show one event           |
| POST   | `/api/events`                        | Yes  | Create an event          |
| PUT    | `/api/events/{id}`                   | Yes  | Update own event         |
| DELETE | `/api/events/{id}`                   | Yes  | Delete own event         |
| GET    | `/api/events/{id}/attendees`         | No   | List attendees           |
| GET    | `/api/events/{id}/attendees/{id}`    | No   | Show one attendee        |
| POST   | `/api/events/{id}/attendees`         | Yes  | Join an event            |
| DELETE | `/api/events/{id}/attendees/{id}`    | Yes  | Remove an attendee       |

Add `?include=user,attendees,attendees.user` to any event endpoint to load relationships.

## Setup

```sh
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Run the queue worker in another terminal so notifications actually get sent:

```sh
php artisan queue:work
```

For email testing, run Mailpit with Docker and point the mail config at it
(`MAIL_MAILER=smtp`, `MAIL_HOST=127.0.0.1`, `MAIL_PORT=1025`):

```sh
docker run -d -p 1025:1025 -p 8025:8025 axllent/mailpit
```

Then open <http://localhost:8025> to read the emails.

To trigger the reminder command manually:

```sh
php artisan app:send-event-reminders
```

## Built With

Laravel 13 · Laravel Sanctum · SQLite · Postman · Mailpit (Docker)
