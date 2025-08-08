@extends('layouts.default')

@section('content')

<div class="row">
    <div class="col-md-12 ">
        <div class="card">
            <div class="header">
                <h4 class="title">Configuración</h4>
                <p class="category"></p>
            </div>

            <div class="content">
                <form>
                    <div class="form-group">
                        <div class="col-lg-4 col-sm-6 col-xs-12">
                            <label for="moneda"><i class="fa fa-usd" aria-hidden="true"></i></label>
                            <input type="radio" name="moneda" id="moneda" value="dollar" />
                            <label for="moneda"><i class="fa fa-eur" aria-hidden="true"></i></label>
                            <input type="radio" name="moneda" id="moneda" value="euro" />
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Aceptar</button>
                    <a class="btn btn-default" href="{{url("constructoras")}}">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>

@stop


