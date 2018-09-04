<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <style>
        div{
            padding-left: 1px;
            padding-right: 1px;
        }
        div.stock {
    /*border: dotted;*/
    font-family: sans-serif;
    background-color: lightgreen;
}
   div.soldout {
    /*border: double;*/
    background-color: lightcoral;
    font-family: fantasy;
}
    </style>
    <body>
        <?php
        $URl_option = '?&js_data=true';
        $URl_pages = '3';
        $URI = array('men' => 'https://www.hhv.de/shop/en/sneakers-men/p:9hB7tT', 'women' => 'https://www.hhv.de/shop/en/sneakers-women/p:DHdi5Y');

//        $URI = array('men' => 'https://www.hhv.de/shop/en/shoes-men/p:i9nIrJ', 'women' => 'https://www.hhv.de/shop/en/shoes-women/p:57ZJ0z');

        function addr_selection($URI, $URl_option, $URl_pages) {
            $URl_page = $URl_pages == '1' ? $URl_pages == '' : $URl_pages;
            $base_hhv = "https://www.hhv.de";
            foreach ($URI as $genre => $value) {

                $url_page_sup = $URl_page != '' ? recuperation_pagination($value, $URl_page, $URl_option) : '';
//                print_r($url_page_sup).die();
                $shoe = file_get_contents($url_page_sup == '' ? $value . $URl_option : $base_hhv . $url_page_sup . $URl_option);

                $shoes[$genre] = json_decode($shoe);
            }
            return stock_out($shoes, $URl_page);
        }

        function stock_out(array $shoes, $URl_pages) {

            $pages = ($URl_pages == "") ? 'Page = ' . '1' : 'Page = ' . $URl_pages;
//            var_dump($pages);
            $data = array();
            foreach ($shoes as $genre => $v) {
                echo '<div style=""><br><a> Genre => ' . $genre . '</a><a> ' . $pages . '</a></div><br>';
                foreach ($v->page->content as $key => $value) {
                    if ($key == 1) {

                        $item_par_pages = [$genre => $value->config->parameters->per];

                        nombres_de_shoes_par_pages($genre, $value, $item_par_pages);
                    }
                }
            }
        }

        function recuperation_pagination($value, $URl_pages, $URl_option) {
            $url_return = file_get_contents($value . '?&page=' . $URl_pages . $URl_option);
            $return = json_decode($url_return);

            return $return->to;
        }

        function nombres_de_shoes_par_pages($genre, $value, $item_par_pages) {
            echo '<table style="align-center">';
            foreach ($item_par_pages as $key => $nbr_item) {
                echo '<table>';

                echo '<tr ">';
                echo '<th><div>';
                echo $value->config->seo->headline;
                echo '</div>';
                for ($i = 0; $i < $nbr_item; $i++) {

                    echo '<tr><th><div ><img src="' . $value->config->items[$i]->images[0]->big . '" style="width:60px;height:60px;vertical-align: middle;"/></div></th>';
                    echo '<th>[' . $value->config->items[$i]->id . ']' . ($value->config->items[$i]->artist) . '';
                    if (isset($value->config->items[$i]->sizeVariants)) {
                        foreach ($value->config->items[$i]->sizeVariants as $k => $v) {
                            if ($v->soldOut) {
                                $data [$genre][$value->config->items[$i]->id][$v->id] = array('marque' => $value->config->items[$i]->artist, 'pointure' => $v->name, 'rupture' => $v->soldOut);

                                echo '<td><div class="soldout">';
                                  echo'<span>'.($v->id).':<br></span>';
                                echo'<span>'.($v->name).'</span>';
                                echo '</div><br></td>';
                            }else{
                              echo '<td><div class="stock">';
                                echo'<span>'.($v->id).':</span>';
                                echo'<span>'.($v->name).'</span>';
                                echo '</div></td>';
                            }
                        }
                    }
                     echo '</div></th>';
                }
               echo'</th></tr>';
            }
            echo '</table>';
            $data['item_par_page'] = $nbr_item;
//            header('location : index.php ',$data);
            echo print_r(json_encode($data));
        }

        addr_selection($URI, $URl_option, $URl_pages);
        ?>
    </body>
</html>
