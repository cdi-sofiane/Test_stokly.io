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
        <link rel="stylesheet" type="text/css"  href='assets/pages_stock.css'>
    </head>



    <body>
        <?php
        $URl_option = '?&js_data=true';
        $URl_pages = '2';
        $URI[0] = array('Men' => 'https://www.hhv.de/shop/en/sneakers-men/p:9hB7tT', 'Women' => 'https://www.hhv.de/shop/en/sneakers-women/p:DHdi5Y');
        $data = array();
//        $URI[1] = array('Men' => 'https://www.hhv.de/shop/en/shoes-men/p:i9nIrJ', 'Women' => 'https://www.hhv.de/shop/en/shoes-women/p:57ZJ0z');

        multiple_addr($URI, $URl_option, $URl_pages);

        function multiple_addr($URI, $URl_option, $URl_pages) {
            foreach ($URI as $key => $URI) {
                addr_selection($URI, $URl_option, $URl_pages);
            }
        }

        function addr_selection($URI, $URl_option, $URl_pages) {
            $URl_page = $URl_pages == '1' ? $URl_pages == '' : $URl_pages;
            $base_hhv = "https://www.hhv.de";
            foreach ($URI as $genre => $value) {

                $url_page_sup = $URl_page != '' ? recuperation_pagination($value, $URl_page, $URl_option) : '';

                $shoe = file_get_contents($url_page_sup == '' ? $value . $URl_option : $base_hhv . $url_page_sup . $URl_option);

                $shoes[$genre] = json_decode($shoe);
            }
            return stock_out($shoes, $URl_page);
        }

        function stock_out(array $shoes, $URl_pages) {

            $pages = ($URl_pages == "") ? 'Page = ' . '1' : 'Page = ' . $URl_pages;


            foreach ($shoes as $genre => $v) {
                echo '<div class="pages"><br><a > ' . $pages . '</a></div><br>';
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
            echo '<table class="Stocks">';
            echo '<tr data-gender="' . $genre . '">';
            echo '<th  class="top"><div>';
            echo $value->config->seo->headline;
            echo '</div>';
            echo '<td><div>';
            echo 'Image';
            echo '</div></td>';
            echo '<td><div>';
            echo 'id';
            echo '</div></td>';
            echo '<td><div>';
            echo 'marque';
            echo '</div></td>';
            echo '<td><div>';
            echo 'pointure en stock';
            echo '</div></td></th>';
            foreach ($item_par_pages as $key => $nbr_item) {



                for ($i = 0; $i < $nbr_item; $i++) {
                    echo '<tr><th>' . $i . '<div>';
                    echo '<td><div ><img src="' . $value->config->items[$i]->images[0]->big . '" style=""/></div></td>';
                    echo '<td><div >[' . $value->config->items[$i]->id . ']</div ></td>';
                    echo '<td><div>' . $value->config->items[$i]->artist . '</div ></td>';

                    if (isset($value->config->items[$i]->sizeVariants)) {
                        echo '<td>';
                        $sizeVariante = $value->config->items[$i];
                        echo sizeVariante($sizeVariante, $genre);

                        echo '</td>';
                    }

                    echo '</div></th>';
                }
//               echo'</tr>';
            }
            echo '</tr></table>';
            $data['item_par_page'] = $nbr_item;
        }

        function sizeVariante($sizeVariante, $genre) {
            foreach ($sizeVariante->sizeVariants as $k => $v) {
                if ($v->soldOut) {


                    echo '<div id="' . $v->id . '" class="soldout">';
                    echo $v->id . ':<br>';
                    echo $v->name;
                    echo '</div>';
                    $data[][$genre][$sizeVariante->id][$v->id] = array('marque' => $sizeVariante->artist, 'pointure' => $v->name, 'rupture' => $v->soldOut);
                } else {

                    echo '<div id="' . $v->id . '" class="stock">';
                    echo $v->id . ': <br>' . $v->name;
//                    echo ;
                    echo '</div>';
                    $data[][$genre][$sizeVariante->id][$v->id] = array('marque' => $sizeVariante->artist, 'pointure' => $v->name, 'stock' => $v->soldOut);
                }
            }
//            echo print_r(json_encode($data));
        }
        ?>
    </body>
</html>
