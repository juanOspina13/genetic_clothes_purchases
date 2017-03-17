<?php
  require_once ('interfaseBase.php');
  class  itemAVender
  {
    protected $descripcion;
    protected $ganancia;
    protected $costo;

    public function __construct( $descripcion , $ganancia, $costo )
    {
      $this -> descripcion    = $descripcion ;
      $this -> ganancia       = $ganancia ;
      $this -> costo          = $costo ;

    }
    public function getDescripcion()
    {
      return $this -> descripcion;
    }
    public function getGanancia()
    {
      return $this -> ganancia;

    }
    public function getCosto()
    {
      return $this -> costo;

    }
  }
