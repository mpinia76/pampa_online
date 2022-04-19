
<strong>&nbsp;&nbsp;Apartamentos&nbsp;</strong><select name="InformeApartamentoId[]" multiple="multiple" style="height:40px; width:200px; margin:2px 0px" id="InformeApartamentoId">
	
	
	<?php 
	
	foreach($apartamentos as $apartamento){ ?>
	<option value="<?php echo $apartamento['Apartamento']['id']?>"><?php echo $apartamento['Apartamento']['apartamento']?></option>
    <?php } ?>
</select>
