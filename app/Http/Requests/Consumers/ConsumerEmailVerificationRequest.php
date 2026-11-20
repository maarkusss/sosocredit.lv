<?php

namespace App\Http\Requests\Consumers;

use App\Models\Consumer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class ConsumerEmailVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $consumer = Consumer::find($this->route('id'));
        /** @var Consumer $consumer */

        if (! hash_equals(
            (string) $this->route('id'),
            (string) $consumer->getKey()
        )) {
            return false;
        }

        if (! hash_equals(
            (string) $this->route('hash'),
            sha1($consumer->getEmailForVerification())
        )) {
            Log::warning('ConsumerVerifyEmailLink: incorrect hash', [
                'id' => $this->route('id'),
                'hash' => $this->route('hash'),
            ]);
            //            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
