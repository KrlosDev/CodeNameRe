<?php

namespace App\Http\Controllers;

use App\Models\Licencia;
use App\User;
use Carbon\Carbon;
use DateTimeZone;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class LoginController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param Request $params
     *
     * @return $this|LoginController|\Illuminate\Http\RedirectResponse
     */
    public function authenticate(Request $params)
    {
        $this->validate($params, [
            'name' => 'required', 'password' => 'required',
        ]);
        if (Auth::attempt(['name' => $params['name'], 'password' => $params['password'], 'estado' => User::ACTIVO])) {
            Auth::guard();
            
            $now = Carbon::now(new DateTimeZone("America/Panama"));
            /**
             * @var User $user
             */
            $user = Auth::user();
            $constructora = $user->getConstructora();

            if ($constructora !== true) {
                $licencia = $constructora->licencia;
                if (! $licencia) {
                    return $this->logout()
                        ->withInput($params->only('name', 'remember'))
                        ->withErrors([
                            'name' => 'Su licencia se encuentra vencida.',
                        ]);
                }
                $datetime = new Carbon($licencia->fecha_suspension);

                if ($datetime->lessThanOrEqualTo($now)) {
                    return $this->logout()
                        ->withInput($params->only('name', 'remember'))
                        ->withErrors([
                            'name' => 'Su licencia se encuentra vencida.',
                        ]);
                }
            }
            // Authentication passed...
            return redirect()->intended('reporte');
        }
        return $this->sendFailedLoginResponse($params);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|Response
     */
    public function login(Request $request)
    {
        if (Auth::user()) {
            return redirect()->route('reporte');
        }

        return $this->authenticate($request);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getViewLogin()
    {
        if (Auth::user()) {
            return redirect()->route('reporte');
        }

        return view('auth.login');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function sendFailedLoginResponse(Request $request)
    {
        return redirect()->back()
            ->withInput($request->only('name', 'remember'))
            ->withErrors([
                'name' => Lang::get('auth.failed'),
            ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
                ?: redirect()->intended($this->redirectPath());
    }
}
