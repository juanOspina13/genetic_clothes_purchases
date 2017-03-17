
<?php
require_once ('items/camisetas.php');
require_once ('items/Pantalones.php');
require_once ('items/Pantaloneta.php');
require_once ('items/Sacos.php');
require_once ('items/Polos.php');


  class  AlgoritmoGenetico
  {
    protected $poblacionInicial   = array();
    protected $matingPool         = array();
    protected $poblacionMutada    = array();
    protected $poblacionCruzada   = array();
    protected $cromosomaSolucion  = array();
    protected $tamañoMatingPool   = 100 ;
    protected $inversion          = 500000 ;
    protected $camisetas          = null;
    protected $camisetas_esqueleto          = null;
    protected $pantalonetas       = null;
    protected $sacos              = null;
    protected $polos              = null;

    public $itemsAVender          = array( 'camisetas_esqueleto','camisetas' , 'pantalonetas' , 'sudaderas', 'sacos' , 'polos');

    public function __construct()
    {
      $this -> camisetas              = new Camisetas( 'camisetas'            , '25000' ,'15000');
      $this -> camisetas_esqueleto    = new Camisetas( 'camisetas_esqueleto'  , '20000' ,'15000');
      $this -> pantalonetas           = new Pantaloneta ( 'pantaloneta'       , '20000' ,'15000');
      $this -> sudaderas              = new Pantalones  ( 'sudaderas'         , '30000' ,'30000');
      $this -> sacos                  = new Sacos       ( 'sacos'             , '30000' ,'25000');
      $this -> polos                  = new Sacos       ( 'polos'             , '34000' ,'26000');



    }
    public function  generarPoblacionInicial ( $poblacionInicial = null , $tamañoPoblacionInicial = null , $tamañoMatingPool = null , $constanteParada= null )
    {

      for ( $i = 0 ; $i < 1000 ; $i++ )
      {
        array_push ( $this -> poblacionInicial , $this -> devolverPoblacionValida() );
      }


    }

    public function devolverTotalInversion( $cantidades )
    {
      $total = 0 ;
      foreach ( $this -> itemsAVender as $item )
      {
        $total += $cantidades  [ $item ] * $this -> $item -> getCosto();
      }
      return $total;
    }

    public function devolverPoblacionValida()
    {
      $totalInversion = 0 ;
      $cantidadCamisetas    = 0 ;
      $cantidadPantalonetas = 0 ;
      $cantidadPantalones   = 0 ;
      // el criterio de parada esta mal
      while ( $totalInversion <  $this -> inversion )
      {
        foreach ( $this -> itemsAVender as $item )
        {
          $cantidades [ $item ] = rand ( 1 , 10 );
        }

        $totalInversion = $this -> devolverTotalInversion ($cantidades );
      }
      return array(
        'cantidades' => $cantidades,
        'totalInversion' => $totalInversion
      );

    }

    public function reemplazoInmediato()
    {
        $constanteParada = $this -> inversion;
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
             $valor_maximo[ 'costo'] = $this -> devolverTotalInversion ( $cromosoma ['cantidades'] );
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
      $mejorCromosoma = $this -> devolverMejorCromosoma() ;
      echo "<table>";
      echo "<thead>";
      echo "<tr>";
      foreach ( $this -> itemsAVender as $item )
      {
        echo "<td>";
        echo $item;
        echo "</td>";
      }
      echo "</tr>";

      echo "</thead>";
      echo "<tbody>";
      echo "<tr>";
      foreach ( $this -> itemsAVender as $item )
      {
        echo "<td>";
        echo "".$mejorCromosoma ['cantidades'] [ $item] ."<br>";
        echo "</td>";
      }
      echo "</tr>";
      echo "</tbody>";
      echo "</table>";
      echo "<br>";

      echo "<b>costo inversion </b>:" . $mejorCromosoma [ 'costo']."<br>";
      echo "<b>ganancia maxima </b>:" . $this -> puntuarCromosoma (  $mejorCromosoma )."<br>";

    }

    public function puntuarCromosoma ( $cromosoma )
    {
      $gananciaTotal = 0 ;
      foreach ( $this -> itemsAVender as $item )
      {
        $gananciaTotal += $cromosoma['cantidades'] [$item] * $this -> $item -> getGanancia();
      }
      return $gananciaTotal;
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
          )
      );
      foreach ( $this -> itemsAVender as $item )
      {
        $hijo [ 'cantidades' ] [ $item ] = round (  ($padre['cantidades'][ $item ] + $madre ['cantidades'][ $item ] ) / 2 );
      }
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
