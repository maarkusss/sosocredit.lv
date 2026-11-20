<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * @property $name
 * @property $email
 * @property $message
 * @property $request
 */
class ContactForm extends Model
{
    protected $table = 'contact_forms';

    protected $fillable = [
        'name',
        'email',
        'message',
        'request',
    ];

    public $timestamps = true;

    protected $casts = [
        'request' => 'array',
    ];

    public static function storeFromRequest(Request $request): void
    {
        $contact_form = new ContactForm();
        $contact_form->name = $request->name;
        $contact_form->email = $request->email;
        $contact_form->message = $request->question;
        $contact_form->request = $request->toArray();
        $contact_form->save();
    }
}
