<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'cc_name'      => 'required_if:pay_by,==,card',
            'cc_number'    => 'required_if:pay_by,==,card',
            'expiry_month' => 'required_if:pay_by,==,card',
            'expiry_year'  => 'required_if:pay_by,==,card',
            'cvv'          => 'required_if:pay_by,==,card',
        ];
    }

    public function attributes()
    {
        return [
            'cc_name'   => 'card holder name',
            'cc_number' => 'card number',
        ];
    }
}
