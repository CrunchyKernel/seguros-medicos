<?php
namespace Backend;


class RecargosController extends \BaseController{
    protected $layout = 'backend.layout.master';
    public $ruta = '';
    public $scripts = array();
    
    public function  __construct()
    {

    }

    public function recargos(){
        $aseguradoras = \Aseguradora::all();
        $recargos = array();
        foreach($aseguradoras as $aseguradora){
            $aseguradora->logo = ucfirst(explode( ".", $aseguradora->logo )[0]);
            array_push($recargos, json_decode($aseguradora->configuracion));
        }
        foreach ($recargos as $recargo){
            $recargo->logo = ucfirst(explode( ".", $recargo->logo )[0]);
            if($recargo->logo == "Planseguro"){
                $recargo->logo = "Plan seguro";
            }
        }
        
        $this->scripts[] = array("tipo" => "js", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min");
        $this->scripts[] = array("tipo" => "css", "archivo" => "backend/js/x-editable/dist/bootstrap3-editable/css/bootstrap-editable.min");
        $this->scripts[] = array("tipo" => "js", "archivo" =>"backend/js/helpers/aseguradora/recargos");
        \View::share('scripts', $this->scripts);
        \View::share('recargos', $recargos);
        $this->layout->content = \View::make('backend.aseguradora.recargos');
    }
    
    public function actualizarInteres()
    {
        $campos = \Input::all();
        $aseguradora = \Aseguradora::where("nombre", $campos['campo'])->get()[0];

        $configuracion = json_decode($aseguradora->configuracion);
        $configuracion->{$campos['ciclo']} = (float) $campos['value'];

        $aseguradora->configuracion = json_encode($configuracion);
        //dd($aseguradora);
        if($aseguradora->save()){
            return json_encode(array("success" => "true"));
        }
        return json_encode(array("success" => "false"));
    }
}