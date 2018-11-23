<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Artisan;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about');
    }

    public function admin()
    {
        return view('pages.admin');
    }

    public function adminRun(Request $request)
    {
        $password = config('admin.password');

        if (!$password) {
            return back()->withInput()->withErrors(['password' => 'Admin password not configured.']);
        }

        $this->validate($request, [
            'password' => 'required|string',
            'action' => [
                'required',
                'string',
                Rule::in(['migrate', 'reset']),
            ],
        ]);

        if ($request->input('password') !== $password) {
            return back()->withErrors(['password' => 'Wrong password.']);
        }

        switch ($request->input('action')) {
            case 'migrate':
                $exitCode = Artisan::call('migrate');
                break;
            case 'reset':
                $exitCode = Artisan::call('migrate:reset');
                break;
        }

        if ($exitCode !== 0) {
            return back()->withErrors(['exitCode' => 'Action failed with exit code: '.$exitCode]);
        }

        return back()->withSuccess('Action completed successfully.');
    }
}
