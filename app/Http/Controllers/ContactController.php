<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use SendGrid\Mail\Mail;
use SendGrid;

class ContactController extends Controller
{
    public function create()
    {
        return view('contact.create');
    }

    public function store(Request $request)
    {

        $inputs = $request->validate([
            'title' => 'required|max:255',
            'email' => 'required|email|max:255',
            'body' => 'required|max:1000',
        ]);

        Contact::create($inputs);

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("sugurukakizaki119_0511@yahoo.co.jp", "Sns-app")
            ->setTo("sugurukakizaki119_0511@yahoo.co.jp", "Suguru Kakizaki")
            ->setSubject($inputs['title'])
            ->addContent("text/plain", $inputs['body']);
        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
            Log::info('SendGrid response status: ' . $response->statusCode());
        } catch (\Exception $e) {
            Log::error('Caught exception: '. $e->getMessage());
        }

        return back()->with('message', 'メールを送信したのでご確認ください');
    }
}
