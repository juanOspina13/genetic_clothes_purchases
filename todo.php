<?php
  class  AlgoritmoGenetico
  {
    protected $poblacionInicial   = array();
    protected $matingPool         = array();
    protected $poblacionMutada    = array();
    protected $poblacionCruzada   = array();
    protected $cromosomaSolucion = array();
    protected $tamañoMatingPool = 10 ;

    public function  generarPoblacionInicial ( $poblacionInicial = null , $tamañoPoblacionInicial = null , $tamañoMatingPool = null , $constanteParada= null )
    {
      for ( $i = 0 ;  $i < 50 ; $i++)
      {
        array_push ( $this -> poblacionInicial , rand(0 , 1000 ) );
      }
    }

    public function reemplazoInmediato()
    {
        $constanteParada = 100;
        $this -> cromosomaSolucion = array();
        $this -> poblacionReemplazo = array();
        for ( $i = 0; $i < sizeof ( $this -> matingPool ) ; $i++ )
        {
            $this ->poblacionInicial[ $i ] = $this -> poblacionMutada [ $i ];
        }

        for ($i = 0; $i < sizeof( $this -> poblacionInicial ); $i++)
        {
            if (  $this ->poblacionInicial[ $i ] > $constanteParada)
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
           if ( $valor_maximo < $cromosoma )
           {
             $valor_maximo = $cromosoma;
           }
         }
         return $valor_maximo;
    }

    public function algoritmoEvolutivo()
    {
      $generacion           = 0;
      $generacionesMinimas  = 2000;
      $this -> generarPoblacionInicial();
      while (
         $generacion < $generacionesMinimas
     ){
        $this -> presionSelectiva();  // Se hace seleccion por torneo
       $this -> reproducirCromosomas();// Se realiza Cruce y Mutacion
        $this -> reemplazoInmediato(); // de los hijos se selecciona un numero  N por torneo y se reemplaza la poblacion inicial en su totalidad
        $generacion ++;
      }

      echo $this -> devolverMejorCromosoma() ;
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
            if ( $cromosoma1 <= $cromosoma2 )
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
        array_push ( $this -> poblacionCruzada ,  round ( ( $padre + $madre )  / 2 ) );
    }
    public function mutar( $cromosoma )
    {
        $mutacion = rand ( 1 , 50 );
        return $cromosoma - $mutacion ;
    }
}

$algoritmoGenetico = new AlgoritmoGenetico();
$algoritmoGenetico -> algoritmoEvolutivo();
