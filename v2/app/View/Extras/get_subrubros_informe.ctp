
<select id="extra_subrubro">
	<option>Seleccionar...</option>
    <?php foreach($extra_subrubros as $id => $rubro){ 
    	
        echo '<option value="'.$id.'">'.$rubro.'</option>';
     } ?>
    
    
    
</select>