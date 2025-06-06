<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class UserAvatarController extends Controller
{
    /**
     * Pretty basic avatar generator using SVG
     * Avatar style by https://github.com/eddiejibson/avatars
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $avatar = null)
    {
        $user_id = @hex2bin($avatar) ?: null;

        $user = $user_id ? User::find($user_id) : null;

        $initials = Str::of(optional($user)->name ?? ':(')->limit(2, '')->title();

        $background = '#65A30D';
        $foreground = '#fff';
        $font = url('fonts/montserrat-latin.woff2');

        // TODO: Remeber that the used unicode subset is equal to latin

        $svg = <<<"SVG"
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="font-weight:700;" width="500px" height="500px">
            <defs>
                <style type="text/css">
                    @font-face {font-family: "Montserrat";src: url("{$font}") format("woff2");}
                </style>
            </defs>
            <rect x="0" y="0" width="500" height="500" rx="0" style="fill:{$background}"/>
            <text x="50%" y="50%" dy=".1em" fill="{$foreground}" text-anchor="middle" dominant-baseline="middle" style="font-family: &quot;Montserrat&quot;, sans-serif; font-size: 250px; line-height: 1">{$initials}</text>
        </svg>
        SVG;

        return response()->make($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => HeaderUtils::makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, 'user-avatar.svg'),
        ])
            ->setEtag(sha1($svg))
            ->setLastModified();
    }
}
