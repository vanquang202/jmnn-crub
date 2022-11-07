<?php

namespace Jmnn\Crub\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CrubRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rule = [];
        $controller = $this->route()->getController();
        if(method_exists($controller, 'getRules'))
            $rule = $controller->getRules($this->method(),$this->route()->id ?? 0);
        return $rule;
    }
}
