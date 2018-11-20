<div id='results'>
    <?php
    if (!empty($_POST)) {
        $geocodingAPI = "http://nominatim.openstreetmap.org/search?q=";
        $_POST = array_map('clean', $_POST);
        extract($_POST);
        $address = str_replace(","," ",$address);
        $qs = $address.",".$city.",".$county.",".$country;
        $qs .= "&format=json&limit=1&polygon=0&addressdetails=0";
        $rs = json_decode(file_get_contents($geocodingAPI.$qs));
        echo "<table>\n";
        echo "<tr> <td>place_id</td> <td>".$rs[0]->place_id."</td> </tr>\n";
        echo "<tr> <td>licence</td> <td>".$rs[0]->licence."</td> </tr>\n";
        echo "<tr> <td>osm_type</td> <td>".$rs[0]->osm_type."</td> </tr>\n";
        echo "<tr> <td>osm_id</td> <td>".$rs[0]->osm_id."</td> </tr>\n";
        echo "<tr> <td>boundingbox</td> <td>".$rs[0]->boundingbox."</td> </tr>\n";
        echo "<tr> <td>lat</td> <td>".$rs[0]->lat."</td> </tr>\n";
        echo "<tr> <td>lon</td> <td>".$rs[0]->lon."</td> </tr>\n";
        echo "<tr> <td>display_name</td> <td>".$rs[0]->display_name."</td> </tr>\n";
        echo "<tr> <td>class</td> <td>".$rs[0]->class."</td> </tr>\n";
        echo "<tr> <td>type</td> <td>".$rs[0]->type."</td> </tr>\n";
        echo "<tr> <td>importance</td> <td>".$rs[0]->importance."</td> </tr>\n";
        echo "</table>\n";
        $linkQs = str_replace("json","html",$qs);
        echo "<p><a href='".$geocodingAPI.$linkQs."'>Vedi il risultato su OpenStreetMap</a></p>";
    }
    function clean($el)	{
        return urlencode(strip_tags(trim($el)));
    }
    ?>
</div>