


<div class="container-fluid">
    <nav class="pull-left">
        <ul>
            <li>
                <a href="#">
                    Home
                </a>
            </li>

        </ul>
    </nav>
    <p class="copyright pull-right">
        &copy; 2016 
    </p>
</div>


    <script src="{{url('assets/js/jquery-1.10.2.js')}}" type="text/javascript"></script>
    <!--script src="{{url('dpd/jquery-2.1.4.min.js')}}" type="text/javascript"></script-->
    <script src="{{url('assets/js/bootstrap.min.js')}}" type="text/javascript"></script>

    <!--  Checkbox, Radio & Switch Plugins -->
    <script src="{{url('assets/js/bootstrap-checkbox-radio-switch.js')}}"></script>

    <!--  Charts Plugin -->
    <script src="{{url('assets/js/chartist.min.js')}}"></script>

    <!--  Notifications Plugin    -->
    <script src="{{url('assets/js/bootstrap-notify.js')}}"></script>

    <!--  Google Maps Plugin    -->
    <!--script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script-->

    <!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
    <script src="{{url('assets/js/light-bootstrap-dashboard.js')}}"></script>

    <!-- Light Bootstrap Table DEMO methods, don't include it in your project! -->
    <!--script src="{{url('assets/js/demo.js')}}"></script-->

@if(Auth::user()->can('porLeer',\App\Models\Mensaje::class))
    <script>
        $(document).ready(function() {
            @if(Auth::user()->isBroker())
                var burl= '{{url('brokers/mensajes/porLeer')}}';
            @elseif(Auth::user()->isEjVentas())
                var burl= '{{url('ejecutivo_ventas/mensajes/porLeer')}}';
            @endif

            $('#nmsjs').html( {{Auth::user()->msjPorLeer()}});

            $("#mensaje-modal").on("hidden.bs.modal", function () {
                    $.get(burl,function(data,status){
                     data=JSON.parse(data);
                     $('#nmsjs').html(data);
                });
                    
             
            });
        });
        
    </script>
@endif
    @stack('JS')

