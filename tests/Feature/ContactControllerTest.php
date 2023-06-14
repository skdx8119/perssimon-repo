<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
public function it_can_store_a_new_contact()
{
    // Arrange: テストに必要なデータを準備する
    $contactData = [
        'title' => 'Test Title',
        'email' => 'test@example.com',
        'body' => 'Test body',
    ];

    // Act: テスト対象の機能を実行する
    $response = $this->post(route('contact.store'), $contactData);

    // Assert: 結果が期待通りであることを確認する
    $response->assertStatus(302);
    $response->assertSessionHas('message', 'メールを送信しました。');
    $this->assertDatabaseHas('contacts', $contactData);
}
/** @test */
public function it_sends_an_email_after_contact_is_created()
{
    // Arrange
    $contactData = [
        'title' => 'Test Title',
        'email' => 'test@example.com',
        'body' => 'Test body',
    ];

    // メール送信をフェイク
    Mail::fake();

    // Act
    $response = $this->post(route('contact.store'), $contactData);

    // Assert
    // 管理者にメールが送信されたことを確認
    Mail::assertSent(ContactForm::class, function ($mail) use ($contactData) {
        return $mail->hasTo(config('mail.admin')) &&
               $mail->contactData['title'] === $contactData['title'] &&
               $mail->contactData['email'] === $contactData['email'] &&
               $mail->contactData['body'] === $contactData['body'];
    });

    // 送信者自身にメールが送信されたことを確認
    Mail::assertSent(ContactForm::class, function ($mail) use ($contactData) {
        return $mail->hasTo($contactData['email']) &&
               $mail->contactData['title'] === $contactData['title'] &&
               $mail->contactData['email'] === $contactData['email'] &&
               $mail->contactData['body'] === $contactData['body'];
    });
}

}
