# Hotel Reservation & Stripe Payment Flow

This project includes a client-facing room reservation flow with Stripe payment verification.

## What Was Added

- Room availability query with overlap exclusion and capacity filtering.
- Reservation pricing in cents based on nights x room price.
- Stripe PaymentIntent creation and server-side payment verification before reservation creation.
- Inertia pages for:
  - `GET /reservations` (available rooms)
  - `GET /reservations/rooms/{room}` (reservation + payment)
  - `GET /my-reservations` (client reservations table)
- JSON-ready reservation resource endpoint on `GET /my-reservations` when `Accept: application/json` is used.

## Environment Variables

Add these to `.env`:

```dotenv
STRIPE_KEY=pk_test_xxx
STRIPE_SECRET=sk_test_xxx
STRIPE_CURRENCY=usd
```

## Main Backend Files

- `app/Models/Room.php`
- `app/Services/ReservationAvailabilityService.php`
- `app/Services/ReservationPricingService.php`
- `app/Services/StripeReservationPaymentService.php`
- `app/Http/Controllers/Client/ReservationController.php`
- `app/Http/Requests/CreateReservationPaymentIntentRequest.php`
- `app/Http/Requests/StoreReservationRequest.php`
- `app/Http/Resources/AvailableRoomResource.php`
- `app/Http/Resources/MyReservationResource.php`
- `routes/web.php`

## Main Frontend Files

- `resources/js/pages/Client/Reservations/Index.vue`
- `resources/js/pages/Client/Reservations/Show.vue`
- `resources/js/pages/Client/Reservations/MyReservations.vue`
- `resources/js/components/StripePaymentElement.vue`

## Quick Try

```bash
php artisan serve
npm run dev
```

Open:

- `http://localhost:8000/reservations`

## Testing

A feature test suite was added:

- `tests/Feature/Client/ReservationFlowTest.php`

Run:

```bash
php artisan test --filter=ReservationFlowTest
```

If you see `could not find driver` for sqlite in tests, install/enable `pdo_sqlite` for your PHP runtime or switch tests to another DB connection.

