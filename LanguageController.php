<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    public function switchLang(Request $request)
    {
        $locale = $request->locale;
        session()->put('locale', $locale);
        App::setLocale($locale);
        
        return redirect()->back();
    }
}
