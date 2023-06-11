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

        $inputs=request()->validate([
            'title'=>'required|max:255',
            'email'=>'required|email|max:255',
            'body'=>'required|max:1000',
        ]);
        Contact::create($inputs);

        $email = new Mail();
        $email->setFrom("from@example.com", "Example User");
        $email->setSubject($inputs['title']);
        $email->addTo(config('mail.admin'), "Admin User");
        $email->addContent("text/plain", $inputs['body']);
        $email->addContent(
            "text/html", "<strong>".$inputs['body']."</strong>"
        );
        $sendgrid = new SendGrid(env('SENDGRID_API_KEY'));

        try {
            $response = $sendgrid->send($email);
            $email->setSubject($inputs['title']);
            $email->addTo($inputs['email'], "User");
            $email->addContent("text/plain", $inputs['body']);
            $email->addContent(
                "text/html", "<strong>".$inputs['body']."</strong>"
            );
            $response = $sendgrid->send($email);
            return back()->with('message', 'メールを送信したのでご確認ください');
        } catch (Exception $e) {
            return back()->with('message', 'Failed to send mail: '.$e->getMessage());
        }
    }
}
