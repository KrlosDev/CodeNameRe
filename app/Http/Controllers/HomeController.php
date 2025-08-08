<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * HomeController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->isAdministrador()) {
            return redirect()->route("constructoras");
        } elseif ($user->isConstructora()) {
            return redirect()->route("proyectos");
        } elseif ($user->isBroker()) {
            return redirect()->route("ejecutivo_ventas");
        } elseif ($user->isEjVentas()) {
            return redirect()->route("clientes");
        } elseif ($user->isEjBancos()) {
            return redirect()->route("clientes");
        } elseif ($user->isCliente()) {
            return redirect()->route("reporte");
        }
    }
}
