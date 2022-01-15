<?php

class cataloge
{
    function createProductColumn($column, $listOfRawProduct){
        foreach(array_keys($listOfRawProduct) as $listOfRawProductKey){
            $listOfRawProduct[$column[$listOfRawProductKey]] = $listOfRawProduct[$listOfRawProductKey];
            unset($listOfRawProduct[$listOfRawProductKey]);
        }
        return $listOfRawProduct;
    }

    function product($parameter){
        $collectionOfListProduct = [];

        $raw_data = file($parameter['file_name']);
        foreach($raw_data as $listOfRawProduct){
            $collectionOfListProduct[] = $this->createProductColumn($parameter['columns'], explode(",",$listOfRawProduct));    
        }

        return [
            'product'=>$collectionOfListProduct,
            'gen_length'=>count($collectionOfListProduct),
        ];
    }
}

class populationgenerator{
    function createindividu($parameter){
        $Catalok = new cataloge;
        $nilai = $Catalok->product($parameter)['gen_length'];
        for($i=0;$i<=$nilai-1;$i++){
            $ret[] = rand(0,1);
        }
        return $ret;
    }

    function createpopulation($parameter){
        for($i=0;$i<=$parameter['population']-1;$i++){
            $ret[]=$this->createindividu(($parameter));
        }
        foreach($ret as $key=> $val){
            print_r($val);
            echo '<br>';
        }
    }
}

$parameter = [
    'file_name' => 'D:\Laragon\www\Tesi_TA\Populasi\produk.txt',
    'columns' => ['makanan', 'protein', 'lemak', 'karbohidrat', 'serat'],
    'population' => 10
];

$katalog = new cataloge;
$katalog->product($parameter);

$inisial = new populationgenerator;
$inisial->createpopulation($parameter)
?>

