
<?php
require_once ('camisetas.php');
require_once ('Pantalones.php');
require_once ('Pantaloneta.php');
  class  AlgoritmoGenetico
  {
    protected $poblacionInicial   = array();
    protected $matingPool         = array();
    protected $poblacionMutada    = array();
    protected $poblacionCruzada   = array();
    protected $cromosomaSolucion  = array();
    protected $tamañoMatingPool   = 100 ;
    protected $inversion          = 50000 ;
    protected $camisetas          = null;

    public function __construct()
    {
      $this -> camisetas = new Camisetas( 'camisetas' ,'20000' ,'15000');
      $this -> pantalonetas = new Pantaloneta( 'pantaloneta' ,'15000' ,'15000');
      $this -> pantalones = new Pantalones( 'pantalones' ,'30000' ,'25000');

    }
    public function  generarPoblacionInicial ( $poblacionInicial = null , $tamañoPoblacionInicial = null , $tamañoMatingPool = null , $constanteParada= null )
    {

      for ( $i = 0 ; $i < 1000 ; $i++ )
      {
        array_push ( $this -> poblacionInicial , $this -> devolverPoblacionValida() );
      }


    }

    public function devolverTotalInversion( $cantidadCamisetas , $cantidadPantalonetas , $cantidadPantalones )
    {
         return ($cantidadCamisetas * $this -> camisetas -> getCosto() ) +
          ($cantidadPantalonetas * $this -> pantalonetas -> getCosto() ) +
          ($cantidadPantalones * $this -> pantalones -> getCosto() ) ;

    }

    public function devolverPoblacionValida()
    {
      $totalInversion = 0 ;
      $cantidadCamisetas    = 0 ;
      $cantidadPantalonetas = 0 ;
      $cantidadPantalones   = 0 ;

      while ( $this -> inversion >= $totalInversion)
      {
        $cantidadCamisetas    = rand( 1 , 20) ;
        $cantidadPantalonetas = rand( 1 , 20) ;
        $cantidadPantalones   = rand( 1 , 20) ;
        $totalInversion = $this -> devolverTotalInversion ( $cantidadCamisetas , $cantidadPantalonetas , $cantidadPantalones );
      }
      return array(
        'cantidades' => array(
          'camisetas' => $cantidadCamisetas ,
          'pantalonetas' => $cantidadPantalonetas ,
          'pantalones' => $cantidadPantalones ,
        )
      );

    }

    public function reemplazoInmediato()
    {
        $constanteParada = 100000;
        $this -> cromosomaSolucion = array();
        $this -> poblacionReemplazo = array();
        for ( $i = 0; $i < sizeof ( $this -> matingPool ) ; $i++ )
        {
            $this ->poblacionInicial[ $i ] = $this -> poblacionMutada [ $i ];
        }

        for ($i = 0; $i < sizeof( $this -> poblacionInicial ); $i++)
        {
            if (  $this -> puntuarCromosoma ($this ->poblacionInicial[ $i ] ) > $constanteParada)
            {
               array_push (  $this -> cromosomaSolucion , $this ->poblacionInicial[ $i ] );
            }
        }
    }

    public function devolverMejorCromosoma()
    {
        $valor_maximo =  0;
         foreach ( $this -> cromosomaSolucion as $llave => $cromosoma )
         {
           if ( $valor_maximo < $this -> puntuarCromosoma ( $cromosoma ) )
           {
             $valor_maximo = $cromosoma;
           }
         }
         return $valor_maximo;
    }

    public function algoritmoEvolutivo()
    {
      $generacion           = 0;
      $generacionesMinimas  = 100;
      $this -> generarPoblacionInicial();
      while (
         $generacion < $generacionesMinimas
     ){
        $this -> presionSelectiva();  // Se hace seleccion por torneo
        $this -> reproducirCromosomas();// Se realiza Cruce y Mutacion
        $this -> reemplazoInmediato(); // de los hijos se selecciona un numero  N por torneo y se reemplaza la poblacion inicial en su totalidad
        $generacion ++;
      }
      echo "<pre>" , print_r( $this -> devolverMejorCromosoma() ) , "</pre>";
      echo "ganancia maxima" . $this -> puntuarCromosoma (  $this -> devolverMejorCromosoma()  );
    }

    public function puntuarCromosoma ( $cromosoma )
    {
      $puntos = 0 ;
     $gananciaCamisetas = $cromosoma['cantidades'] ['camisetas'] * $this -> camisetas -> getGanancia();
     $gananciaPantalonetas = $cromosoma['cantidades'] ['pantalonetas'] * $this -> camisetas -> getGanancia();
     $gananciaPantalones = $cromosoma['cantidades'] ['pantalones']* $this -> camisetas -> getGanancia();
     return $gananciaCamisetas + $gananciaPantalones + $gananciaPantalonetas;
    }
    public function presionSelectiva()
    {
        $this -> matingPool = array();
        for ( $i = 0 ; $i < $this -> tamañoMatingPool ; $i++ )
        {
            $indice     = round ( sizeof( $this -> poblacionInicial )* ( rand(0, 100) )/ 100 );
            $indice2    = round ( sizeof( $this -> poblacionInicial )* ( rand(0, 100) )/ 100 );
            if ( $indice == sizeof( $this -> poblacionInicial ) )
            {
              $indice = sizeof( $this -> poblacionInicial ) -1 ;
            }
            if ( $indice2 == sizeof( $this -> poblacionInicial ) )
            {
              $indice2 = sizeof( $this -> poblacionInicial ) -1 ;
            }

            $cromosoma1 = $this -> poblacionInicial [ $indice ];
            $cromosoma2 = $this -> poblacionInicial [ $indice2 ];
            if (   $this -> puntuarCromosoma  ( $cromosoma1 ) <=   $this -> puntuarCromosoma  ( $cromosoma2 ) )
            {
              array_push ( $this -> matingPool , $cromosoma1);
            }else
            {
              array_push ( $this -> matingPool , $cromosoma2);

            }
        }
    }

    public function reproducirCromosomas()
    {
        $this -> poblacionCruzada  = array();
        // Hacemos primo el cruce
        $numeroCruces =  sizeof ($this -> matingPool ) ;

        for ( $i = 0; $i < $numeroCruces; $i++)
        {
            $punto1 = round( ( rand(0,100)/100 ) * (sizeof ( $this -> matingPool ) ) );
            $punto2 = 0;
            if ( $punto1 == sizeof ( $this -> matingPool ) )
            {
              $punto1 = $punto1 -1 ;
            }
            do {
                $punto2 = round( (rand( 0 , 100 ) / 100) * sizeof ( $this -> matingPool ) );
                if ( $punto2 == sizeof ( $this -> matingPool ) )
                {
                  $punto2 = $punto2 -1 ;
                }
            } while ($punto1 == $punto2);

            $this -> cruce( $this -> matingPool [$punto1 ], $this -> matingPool [$punto2 ] );
        }

        // Hacemos la mutación a cada cromosoma
        for ($i = 0; $i < sizeof( $this -> poblacionCruzada ); $i++)
        {
            $probabilidad =  rand ( 0 , 100 );

            if ($probabilidad < 10)
            {
                $cromosomaMutado = $this -> mutar ( $this -> poblacionCruzada [ $i ] );
                array_push ($this -> poblacionMutada , $cromosomaMutado );
            } else
            {
              array_push ($this -> poblacionMutada , $this -> poblacionCruzada [ $i ] );
            }
        }
    }
    public function cruce( $padre , $madre )
    {
      $hijo = array (
        'cantidades' => array
          (
              'camisetas' => round (  ($padre['cantidades'][ 'camisetas' ] + $madre ['cantidades']['camisetas' ] ) / 2 ),
              'pantalonetas' => round (  ($padre['cantidades'][ 'pantalonetas' ] + $madre ['cantidades']['pantalonetas' ] ) / 2 ),
              'pantalones' => round (  ($padre['cantidades'][ 'pantalones' ] + $madre ['cantidades']['pantalones' ] ) / 2 ),
          )
      );
      array_push ( $this -> poblacionCruzada ,  $hijo );
    }
    public function mutar( $cromosoma )
    {
      foreach ( $cromosoma [ 'cantidades'] as $llave => $valor )
      {
        $mutacion = rand ( 1 , 3 );
        $cromosoma [ 'cantidades'] [ $llave ] = $valor - $mutacion;

      }
      return $cromosoma;
    }
}

$algoritmoGenetico = new AlgoritmoGenetico();
$algoritmoGenetico -> algoritmoEvolutivo();
