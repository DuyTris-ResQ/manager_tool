<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Change session locale.
     */
    public function changeLanguage($locale)
    {
        if (in_array($locale, ['en', 'vi'])) {
            session(['locale' => $locale]);
        }
        return redirect()->back();
    }
}
