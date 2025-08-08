<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentosForm;
use App\User;
use App\Models\Cliente;
use App\Models\ClienteCodeudor;
use App\Models\DocumentoCliente;
use App\Models\DocumentoCodeudor;
use App\Models\Funciones;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    /**
     * @param $id
     * @return string
     */
    public function getbyID($id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('get', DocumentoCliente::class)) {
            return json_encode([]);
        }
        
        $documento = DocumentoCliente::find($id);
        return json_encode($documento);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatebyID(Request $request, $id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('get', DocumentoCliente::class)) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar actualizar", "Operación errónea. Usuario no autorizado."));
        }

        /**
         * @var DocumentoCliente $documento
         */
        $documento = DocumentoCliente::find($id);
        if (! $documento) {
            return redirect()->back()->with(['alert' => Funciones::getAlert("danger", "Error al intentar actualizar", "Operación errónea. El documento ingresado no existe.")]);
        }
        
        $documento->fecha_expiracion = $request->fecha_expiracion;
        if (! $documento->save()) {
            return redirect()->back()->with(['alert' => Funciones::getAlert("danger", "Error al intentar actualizar", "Operación errónea. Ha ocurrido un problema actualiznado el documento.")]);
        }
        return redirect()->back()->with(['alert' => Funciones::getAlert("success", "Operación exitosa", "El documento se ha actualizado correctamente.")]);
    }

    /**
     * @param $id
     * @return string
     */
    public function getbyIDCodeudor($id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('get', DocumentoCliente::class)) {
            return json_encode([]);
        }
        
        $documento = DocumentoCodeudor::find($id);
        return json_encode($documento);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatebyIDCodeudor(Request $request, $id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('get', DocumentoCliente::class)) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar actualizar", "Operación errónea. Usuario no autorizado."));
        }
        /**
         * @var DocumentoCodeudor $documento
         */
        $documento = DocumentoCodeudor::find($id);
        if (! $documento) {
            return redirect()->back()->with(['alert' => Funciones::getAlert("danger", "Error al intentar actualizar", "Operación errónea. El documento ingresado no existe.")]);
        }
        
        $documento->fecha_expiracion = $request->fecha_expiracion;
        if (! $documento->save()) {
            return redirect()->back()->with(['alert' => Funciones::getAlert("danger", "Error al intentar actualizar", "Operación errónea. Ha ocurrido un problema actualiznado el documento.")]);
        }
        return redirect()->back()->with(['alert' => Funciones::getAlert("success", "Operación exitosa", "El documento se ha actualizado correctamente.")]);
    }
    
    public function listDocsCliente($id)
    {
        /**
         * @var Cliente $cliente
         */
        $cliente=Cliente::find($id);
        $hoy = new DateTime("now");
        $fecha = $hoy->format("Y-m-d");

        if (! $cliente == null) {
            $docs =  $cliente->documentoCliente()->paginate(10);
        } else {
            $docs=[];
        }

        $codeudor=$cliente->codeudor()->first();
        if (! $codeudor == null) {
            $codocs=$codeudor->documentoCodeudor()->paginate(10);
        } else {
            $codocs=[];
        }
       
        return view('pages.documento.index')->with('docucliente', $docs)
            ->with('cliente', $cliente->user->name)
            ->with('docucodeudor', $codocs)
            ->with('hoy', $fecha)
            ->with('client', $cliente)
            ->with('tipos', DocumentoCliente::$tipos);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  DocumentosForm  $request
     * @return \Illuminate\Http\Response
     */
    public function storeClienteDoc(DocumentosForm $request)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();

        if ($user->cannot('store', DocumentoCliente::class)) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Usuario no autorizado."));
        }

        $base_path = explode('/', base_path());
        array_pop($base_path);
        $path = implode('/', $base_path);
        $path = "$path/public_html/uploads/documentos/";

        $tmpFile=tempnam($path, "");
        $tmpInfo= pathinfo($tmpFile);
        $tmpName= $tmpInfo['filename'];

        $info = pathinfo($request->file('imagen')->getClientOriginalName());
        $extension = $info['extension'];
        $docTempName=$tmpName.".".$extension;
        unlink($tmpFile);

        /**
         * @var DocumentoCliente|DocumentoCodeudor $doc
         */
        $doc = null;
        if ($request->usuario == 1) {
            $doc = new DocumentoCliente;
            $doc->id_cliente=$request->clienteD;
            $doc->src = $docTempName;
            $doc->tipo_documento = $request->id_documento;
            $doc->fecha_expiracion = $request->fecha_expiracion;
        } elseif ($request->usuario == 2) {
            /**
             * @var ClienteCodeudor $codeudor
             */
            $codeudor= ClienteCodeudor::where("clientes_codeudores.id_cliente", $request->clienteD)->first();
            if (! $codeudor) {
                return redirect()->back()->withInput()
                    ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Este cliente todavía no tiene un codeudor registrado."));
            }
            $doc = new DocumentoCodeudor;
            $doc->id_co_deudores=$codeudor->id;
            $doc->src = $docTempName;
            $doc->tipo_documento    = $request->id_documento;
            $doc->fecha_expiracion = $request->fecha_expiracion;
        } else {
            return redirect()->back()->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Parece que has omitido un dato."));
        }

        $doc->id_pais= $request->get('id_pais_identificacion');
        $request->file('imagen')->move($path, $docTempName);

        if (! $doc->save()) {
            return redirect()->back()->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando el Documento."));
        }

        return redirect()->back()
            ->with("alert", Funciones::getAlert("success", "Documento agregado exitosamente", "Operación exitosa."));
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function get($id)
    {
        $base_path = explode('/', base_path());
        array_pop($base_path);
        $path = implode('/', $base_path);
        $enlace = "$path/public_html/uploads/documentos/$id";
        header("Content-Disposition: attachment; filename=".$id." ");
        header("Content-Type: application/octet-stream");
        header("Content-Length: ".filesize($enlace));
        readfile($enlace);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Exception
     */
    public function deleteDocCliente($id)
    {
        /**
         * @var DocumentoCliente $documento
         */
        $documento = DocumentoCliente::find($id);
        if ($documento == null) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar eliminar", "Operación errónea. Documento no encontrado."));
        }

        $base_path = explode('/', base_path());
        array_pop($base_path);
        $path = implode('/', $base_path);
        $path = "$path/public_html/uploads/documentos/{$documento->src}";
         
        if ($documento->delete()) {
            if (file_exists($path)) {
                unlink($path);
            }
               
            return redirect()->back()->with('alert', Funciones::getAlert("success", "Eliminado exitosamente", "Operación exitosa."));
        } else {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar eliminar", "Operación errónea."));
        }
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Exception
     */
    public function deleteDocCodeudor($id)
    {
        /**
         * @var DocumentoCodeudor $documento
         */
        $documento = DocumentoCodeudor::find($id);
        
        if ($documento == null) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar eliminar", "Operación errónea. Documento no encontrado."));
        }

        $base_path = explode('/', base_path());
        array_pop($base_path);
        $path = implode('/', $base_path);
        $path = "$path/public_html/uploads/documentos/{$documento->src}";
         
        if ($documento->delete()) {
            if (file_exists($path)) {
                unlink($path);
            }
               
            return redirect()->back()->with('alert', Funciones::getAlert("success", "Eliminado exitosamente", "Operación exitosa."));
        } else {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar eliminar", "Operación errónea."));
        }
    }

    /**
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function getAll()
    {
        /**
         * @var User $user
         */
        $user = Auth::user();
        $hoy = Carbon::now();
        
        if ($user->cannot('store', DocumentoCliente::class)) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al cargar documentos", "Operación errónea. Usuario no autorizado."));
        }

        /**
         * @var array $brokers
         */
        $brokers = [];
        if ($user->isConstructora()) {
            $brokers = $user->constructora->brokers->map(function ($o) {
                return $o->id;
            })->all();
        } elseif ($user->isBroker()) {
            $brokers[] = $user->broker->id;
        } elseif ($user->isEjVentas()) {
            $brokers[] = $user->ejecutivoVentas->broker->id;
        }

        $documentos= DocumentoCliente::join('clientes', 'documentos_clientes.id_cliente', '=', 'clientes.id')
            ->join('telefonos_clientes', 'documentos_clientes.id_cliente', '=', 'telefonos_clientes.id_cliente')
            ->select('documentos_clientes.*', 'clientes.nombre', 'clientes.identificacion', 'clientes.apellido', 'telefonos_clientes.telefono')
            ->whereIn('clientes.id_broker', $brokers)
            ->where('documentos_clientes.fecha_expiracion', '<', $hoy)
            ->paginate(10);

        return view('pages.documento.vencidos')->with('docucliente', $documentos);
    }
}
