<?php

class parameters{
    const file_name = 'D:\Laragon\www\Tesi_TA\Populasi\produk.txt';
    const columns = ['makanan', 'protein', 'lemak', 'karbohidrat', 'serat'];
    const population = 10;
    const total_kebutuhan = 800;
    const STOPPING_VALUE = 50;
}

class cataloge
{
    function createProductColumn($listOfRawProduct){
        foreach(array_keys($listOfRawProduct) as $listOfRawProductKey){
            $listOfRawProduct[parameters::columns[$listOfRawProductKey]] = $listOfRawProduct[$listOfRawProductKey];
            unset($listOfRawProduct[$listOfRawProductKey]);
        }
        return $listOfRawProduct;
    }

    function product(){
        $collectionOfListProduct = [];

        $raw_data = file(parameters::file_name);
        foreach($raw_data as $listOfRawProduct){
            $collectionOfListProduct[] = $this->createProductColumn(explode(",",$listOfRawProduct));    
        }
        return $collectionOfListProduct;
    }
}

class individu{
    function countnumberofgen(){
        $c = new cataloge;
        return count($c->product());
    }

    function createrandomindividu(){ 
        // echo $this->countnumberofgen();
        $jumlah = $this->countnumberofgen();
        for ($i=0;$i<$jumlah;$i++){
            $ret[] = mt_rand(0,1);
        }
        return $ret;
    }
}

class population{

    function createrandompopulation(){
        $ind = new individu;
        for($i=0;$i<parameters::population;$i++){
            $ret[]=$ind->createrandomindividu();
        }
        return $ret;
    }
}

class fitnes{
    function selectingitem($individu){
        $ca = new cataloge;
        foreach($individu as $individukey=>$binaryGen){
            // print_r($individukey);
            // echo '<br>';
            // print_r($binaryGen);
            if($binaryGen == 1){
                $ret[] = [
                    'selectedKey' => $individukey,
                    'selectedProtein' => $ca->product()[$individukey]['protein'],
                    'selectedLemak' => $ca->product()[$individukey]['lemak'],
                    'selectedKarbohidrat' => $ca->product()[$individukey]['karbohidrat'],
                    'selectedSerat' => $ca->product()[$individukey]['serat'],
                ];    
            }
        }
        //exit();
        return $ret;
        
    }

    function calculateFitnessValue($individu){
        $lemak = array_sum(array_column($this->selectingitem($individu), 'selectedLemak'));
        $protein = array_sum(array_column($this->selectingitem($individu), 'selectedProtein'));
        $karbohidrat = array_sum(array_column($this->selectingitem($individu), 'selectedKarbohidrat'));
        $serat = array_sum(array_column($this->selectingitem($individu), 'selectedSerat'));
        $total = $lemak + $protein + $karbohidrat + $serat;
        
        return $total;
        //print_r($this->selectingitem($individu));
        //exit();        
    }

    function isFit($fitnessValue){
        if($fitnessValue<=parameters::total_kebutuhan){
            return True;
        }
    }

    function countSelectedItems($individu){
        return count($this->selectingitem($individu));
    }

    function searchBestIndividu($fits, $maxItem, $numberOfIndividuMaxItem){
        if($numberOfIndividuMaxItem==1){
            $index = array_search($maxItem, array_column($fits, 'numberOfSelectedItem'));
            return $fits[$index];
            // $out = $fits[$index];
            // print_r('Nilainya: ');
            // print_r($out);
        }else{
            foreach($fits as $key => $value){
                if($value['numberOfSelectedItem']==$maxItem){
                    echo $key.' '.$value['fitnessValue'].'<br>';
                    $ret[] = [
                        'individuKey' => $key,
                        'fitnessValue' => $value['fitnessValue']
                    ];
                }
            }
            if(count(array_unique(array_column($ret, 'fitnessValue')))==1){
                $index = rand(0, count($ret));
            } else{
                $max = max(array_column($ret, 'fitnessValue'));
                $index = array_search($max, array_column($ret, 'fitnessValue'));
            }
            //echo 'Hasilnya: ';
            //echo 'Best Fitness Value Jika Memiliki Lebih dari 1 Individu dan Terbaik:';    
            return $ret[$index];
        }
    }

    function isFound($fits){
        $countedMaxItems= array_count_values(array_column($fits, 'numberOfSelectedItem'));
        print_r($countedMaxItems);
        echo '<br>';
        $maxItem = max(array_keys($countedMaxItems));
        echo 'Menu Maksimal yang Dipilih: '.$maxItem;
        echo '<br>';
        $numberOfIndividuMaxItem = $countedMaxItems[$maxItem];
        echo 'Jumlah Individu: '.$numberOfIndividuMaxItem;
        echo '<br>';
        
        $besFitnesValue = $this->searchBestIndividu($fits, $maxItem, $numberOfIndividuMaxItem)['fitnessValue'];
        echo '<br> Best Fitness Value: '.$besFitnesValue;

        $residual = parameters::total_kebutuhan - $besFitnesValue;
        echo '<br> Residual: '.$residual;

        if($residual <= parameters::STOPPING_VALUE && $residual >0){
            return True;
        }
    }

    function fitnessevaluation($population){
        $ca = new cataloge;
        foreach($population as $listofindividukey=>$listofindividu){
            echo 'Individu- '. $listofindividukey. '<br>';
            foreach($listofindividu as $individukey=>$binaryGen){
                echo $binaryGen.'&nbsp;&nbsp;';
                print_r($ca->product()[$individukey]);
                echo '<br>';
            }
            $fitnessValue = $this->calculateFitnessValue($listofindividu);
            $numofselecteditem = $this->countselecteditems($listofindividu);
            echo 'Menu Makanan Berjumlah: '. $numofselecteditem;
            echo '<br>';
            echo 'Fitness Value: '. $fitnessValue;
            if($this->isFit($fitnessValue)){
                echo ' (Fit)';
                $fits[] = [
                    'selectedIndividuKey' => $listofindividukey,        
                    'numberOfSelectedItem' => $numofselecteditem, 
                    'fitnessValue' => $fitnessValue    
                ];
                print_r($fits);
            } else{
                echo ' (Not Fit)';
            }
            echo '<br><br>';
        }
        if($this->isFound($fits)){
            echo ' Found';
        } else{
            echo ' >> Next Generation'; 
        }
    }
}

// $katalog = new cataloge;
// print_r($katalog->product());

$p = new population;
$InitialPopulation = $p->createrandompopulation();

$f = new fitnes;
$f->fitnessevaluation($InitialPopulation);
// $in = new individu;
// print_r($in->createrandomindividu()); 
?>

