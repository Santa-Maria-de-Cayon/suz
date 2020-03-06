<?php
 $name = '
<iframe id="plyrTable" src="https://stream.1tv.ru/embed" allowfullscreen frameborder="0" width="555" height="312"></iframe>';

  if(isset($_GET['channel'])){
     $ch = $_GET['channel'];
  
     switch ($ch) {
     	  case 'ibizaradio':
     		$name = file_get_contents('radioibiza.js');
        break;

        case 'rusradio':
        $name = file_get_contents('rusradio.js');
        break;  

         case 'spain':
        $name = file_get_contents('spain.js');
        break; 

      default:
        # code...
        break;
      }
     
  };

echo $name;
 
// suzdalenko.com/one?channel=ibizaradio 
 ?>


