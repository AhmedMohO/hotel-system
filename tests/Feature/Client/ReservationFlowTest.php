<?php

use App\Models\Client;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\StripeReservationPaymentService;
use Mockery;
use Stripe\PaymentIntent;

it('creates a reservation after a successful payment verification', function () {
    $client = Client::factory()->create();
    $room = Room::factory()->create([
        'capacity' => 3,
        'price' => 15000,
    ]);

    $checkIn = now()->addDays(3)->toDateString();
    $checkOut = now()->addDays(5)->toDateString();

    $mock = Mockery::mock(StripeReservationPaymentService::class);
    $mock->shouldReceive('currency')->andReturn('usd');
    $mock->shouldReceive('assertSuccessfulPaymentIntent')
        ->once()
        ->andReturn(new PaymentIntent('pi_successful_test'));
    $mock->shouldReceive('markPaymentIntentAsUsed')->once();

    app()->instance(StripeReservationPaymentService::class, $mock);

    $response = $this->actingAs($client, 'client')->post(route('client.reservations.store', $room), [
        'check_in' => $checkIn,
        'check_out' => $checkOut,
        'accompany_number' => 2,
        'payment_intent_id' => 'pi_successful_test',
    ]);

    $response->assertRedirect(route('client.reservations.my'));

    $this->assertDatabaseHas('reservations', [
        'client_id' => $client->id,
        'room_id' => $room->id,
        'check_in' => $checkIn,
        'check_out' => $checkOut,
        'accompany_number' => 2,
        'paid_price' => 30000,
        'approved_by' => null,
    ]);
});

it('rejects overlapping reservations for the same room', function () {
    $client = Client::factory()->create();
    $room = Room::factory()->create([
        'capacity' => 2,
    ]);

    Reservation::factory()->create([
        'client_id' => $client->id,
        'room_id' => $room->id,
        'check_in' => now()->addDays(10)->toDateString(),
        'check_out' => now()->addDays(12)->toDateString(),
        'accompany_number' => 1,
        'paid_price' => 20000,
        'approved_by' => null,
    ]);

    $mock = Mockery::mock(StripeReservationPaymentService::class);
    $mock->shouldReceive('currency')->never();
    $mock->shouldReceive('assertSuccessfulPaymentIntent')->never();
    $mock->shouldReceive('markPaymentIntentAsUsed')->never();

    app()->instance(StripeReservationPaymentService::class, $mock);

    $response = $this->actingAs($client, 'client')->from(route('client.reservations.show', $room))->post(route('client.reservations.store', $room), [
        'check_in' => now()->addDays(11)->toDateString(),
        'check_out' => now()->addDays(13)->toDateString(),
        'accompany_number' => 1,
        'payment_intent_id' => 'pi_overlap_test',
    ]);

    $response->assertSessionHasErrors('check_in');
});

it('returns authenticated client reservations as json with room info', function () {
    $client = Client::factory()->create();
    $room = Room::factory()->create();

    Reservation::factory()->create([
        'client_id' => $client->id,
        'room_id' => $room->id,
        'check_in' => now()->addDays(2)->toDateString(),
        'check_out' => now()->addDays(4)->toDateString(),
        'accompany_number' => 1,
        'paid_price' => 25000,
        'approved_by' => null,
    ]);

    $response = $this->actingAs($client, 'client')
        ->getJson(route('client.reservations.my'));

    $response
        ->assertOk()
        ->assertJsonPath('data.0.room.id', $room->id)
        ->assertJsonPath('data.0.room.number', $room->number)
        ->assertJsonPath('data.0.paid_price', 25000);
});
