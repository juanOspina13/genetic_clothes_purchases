<?php

interface interfaseBase
{
  public function __construct( $descripcion , $ganancia, $costo );
  public function getDescripcion();
  public function getGanancia();
  public function getCosto();

}
