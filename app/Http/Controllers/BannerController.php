<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;


class BannerController extends Controller
{
    public function getBanners()
    {
        $banners = Banner::get();

        return response()->json([
            "banners" => $banners
        ]);
    }
}
