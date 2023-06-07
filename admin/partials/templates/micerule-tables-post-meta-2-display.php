<div class="judges">
<h1>Judges</h1>

<!-----------------------Judge 1--------------------------->
<strong>Judge 1</strong><br>
<select name= "micerule_table_data_tmp[judges][]">
	<option value=''>Please Select</option>
  <?php

              foreach($posts as $value){ ?>
            <option value="<?php echo $value->display_name;?>" <?php if(isset($meta['judges'][0])){ echo ($value->display_name == $meta['judges'][0]) ? ' selected="selected"' : ''; }?>><?php echo $value->display_name;?></option>

        <?php
    }
            ?>
        </select>
        
<input type="checkbox" id="Selfs1" name="micerule_table_data_tmp[classes][0][]" value="Selfs" 
<?php if(isset ($meta['classes'][0])){ foreach($meta['classes'][0] as $value){ 
if($value == 'Selfs'){
echo 'checked';
	}
} }?> >
<label for="Selfs1">Selfs</label>

<input type="checkbox" id="Tans1" name="micerule_table_data_tmp[classes][0][]" value="Tans" 
<?php if(isset ($meta['classes'][0])){foreach($meta['classes'][0] as $value){ 
if($value == 'Tans'){
echo 'checked';
	}
}}?> >
<label for="Tans1">Tans</label>

<input type="checkbox" id="Satins1" name="micerule_table_data_tmp[classes][0][]" value="Satins"
<?php if(isset ($meta['classes'][0])){ foreach($meta['classes'][0] as $value){ 
if($value == 'Satins'){
echo 'checked';
	}
} }?> >
<label for="Satins1">Satins</label>

<input type="checkbox" id="Marked1" name="micerule_table_data_tmp[classes][0][]" value="Marked"
<?php if(isset ($meta['classes'][0])){ foreach($meta['classes'][0] as $value){ 
if($value == 'Marked'){
echo 'checked';
	}
} }?> >
<label for="Marked1">Marked</label>

<input type="checkbox" id="AOVs1" name="micerule_table_data_tmp[classes][0][]" value="AOVs" 
<?php if(isset ($meta['classes'][0])){ foreach($meta['classes'][0] as $value){ 
if($value == 'AOVs'){
echo 'checked';
	}
} }?> >
<label for="AOVs1">AOVs</label>

<input type="checkbox" id="BIS1" name="micerule_table_data_tmp[classes][0][]" value="BIS" 
<?php if(isset ($meta['classes'][0])){ foreach($meta['classes'][0] as $value){ 
if($value == 'BIS'){
echo 'checked';
	}
} }?> >
<label for="BIS1">BIS</label>
<br>
<div class="partnership-judge">
<input type="checkbox" id="partnership1" class="pCheck" 
<?php if(isset ($meta['pShip'][0])){
if($meta['pShip'][0] != ''){
echo 'checked';
	}
} ?> >
<label for="partnership1">Partnership</label>

<!--------------NameSelect for Partner----------->
<select name= "micerule_table_data_tmp[pShip][]" id="selectPShip1" style="display:none">
	<option value=''>Please Select</option>
  <?php

              foreach($posts as $value){ ?>
            <option value="<?php echo $value->display_name;?>" <?php if(isset($meta['pShip'][0])){ echo ($value->display_name == $meta['pShip'][0]) ? ' selected="selected"' : ''; }?>><?php echo $value->display_name;?></option>

        <?php
    }
            ?>
        </select>
</div>        

        <!-----------------------Judge 2--------------------------->
        <strong>Judge 2</strong><br>
        <td><select name= "micerule_table_data_tmp[judges][]">
        	<option value=''>None</option>
          <?php

                      foreach($posts as $value){ ?>
            <option value="<?php echo $value->display_name;?>" <?php if(isset($meta['judges'][1])){ echo ($value->display_name == $meta['judges'][1]) ? ' selected="selected"' : ''; }?>><?php echo $value->display_name;?></option>

                <?php
            }
                    ?>
                </select></td>
		
        <input type="checkbox" id="Selfs2"  name="micerule_table_data_tmp[classes][1][]" value="Selfs"
        <?php if(isset ($meta['classes'][0])){
        if(isset($meta['classes'][1])){
        foreach($meta['classes'][1] as $value){ 
if($value == 'Selfs'){
echo 'checked';
	}
}}
}?> >
		<label for="Selfs2">Selfs</label>

		<input type="checkbox" id="Tans2" name="micerule_table_data_tmp[classes][1][]" value="Tans"
        <?php if(isset ($meta['classes'][0])){
        if(isset($meta['classes'][1])){
        foreach($meta['classes'][1] as $value){ 
if($value == 'Tans'){
echo 'checked';
	}
} }
}?> >
		<label for="Tans2">Tans</label>

		<input type="checkbox" id="Satins2" name="micerule_table_data_tmp[classes][1][]" value="Satins" 
        <?php if(isset ($meta['classes'][0])){
        if(isset($meta['classes'][1])){
        foreach($meta['classes'][1] as $value){ 
if($value == 'Satins'){
echo 'checked';
	}
} }
}?> >
		<label for="Satins2">Satins</label>

		<input type="checkbox" id="Marked2" name="micerule_table_data_tmp[classes][1][]" value="Marked"
         <?php if(isset ($meta['classes'][0])){
         if(isset($meta['classes'][1])){
         foreach($meta['classes'][1] as $value){ 
if($value == 'Marked'){
echo 'checked';
	}
}}
}?> >
		<label for="Marked2">Marked</label>

		<input type="checkbox" id="AOVs2" name="micerule_table_data_tmp[classes][1][]" value="AOVs"
        <?php if(isset ($meta['classes'][0])){
        if(isset($meta['classes'][1])){
        foreach($meta['classes'][1] as $value){ 
if($value == 'AOVs'){
echo 'checked';
	}
} }
}?> >
		<label for="AOVs2">AOVs</label>

		<input type="checkbox" id="BIS2" name="micerule_table_data_tmp[classes][1][]" value="BIS"
        <?php if(isset ($meta['classes'][0])){
        if(isset($meta['classes'][1])){
        foreach($meta['classes'][1] as $value){ 
if($value == 'BIS'){
echo 'checked';
	}
} }
}?> >
		<label for="BIS2">BIS</label>
        
<br>
<div class="partnership-judge">
<input type="checkbox" id="partnership2" class="pCheck"  
<?php if(isset ($meta['pShip'][1])){
if($meta['pShip'][1] != ''){
echo 'checked';
	}
} ?> >
<label for="partnership2">Partnership</label>

<!--------------NameSelect for Partner----------->
<select name= "micerule_table_data_tmp[pShip][]" id="selectPShip2" style="display:none">
	<option value=''>Please Select</option>
  <?php

              foreach($posts as $value){ ?>
            <option value="<?php echo $value->display_name;?>" <?php if(isset($meta['pShip'][1])){ echo ($value->display_name == $meta['pShip'][1]) ? ' selected="selected"' : ''; }?>><?php echo $value->display_name;?></option>

        <?php
    }
            ?>
        </select>
</div>
<!-----------------------Judge 3--------------------------->
<strong>Judge 3</strong><br>
<td><select name= "micerule_table_data_tmp[judges][]">
	<option value=''>None</option>	
<?php

              foreach($posts as $value){ ?>
            <option value="<?php echo $value->display_name;?>" <?php if(isset($meta['judges'][2])){ echo ($value->display_name == $meta['judges'][2]) ? ' selected="selected"' : ''; }?>><?php echo $value->display_name;?></option>

            <?php
        }
                ?>
              </select></td>
              
        <input type="checkbox" id="Selfs3" name="micerule_table_data_tmp[classes][2][]" value="Selfs"
        <?php if(isset ($meta['classes'][2])){ foreach($meta['classes'][2] as $value){ 
if($value == 'Selfs'){
echo 'checked';
	}
} }?> >
		<label for="Selfs3">Selfs</label>

		<input type="checkbox" id="Tans3" name="micerule_table_data_tmp[classes][2][]" value="Tans"
        <?php if(isset ($meta['classes'][2])){ foreach($meta['classes'][2] as $value){ 
if($value == 'Tans'){
echo 'checked';
	}
}}?> >
		<label for="Tans3">Tans</label>

		<input type="checkbox" id="Satins3" name="micerule_table_data_tmp[classes][2][]" value="Satins"
        <?php if(isset ($meta['classes'][2])){ foreach($meta['classes'][2] as $value){ 
if($value == 'Satins'){
echo 'checked';
	}
} }?> >
		<label for="Satins3">Satins</label>

		<input type="checkbox" id="Marked3" name="micerule_table_data_tmp[classes][2][]" value="Marked"
        <?php if(isset ($meta['classes'][2])){ foreach($meta['classes'][2] as $value){ 
if($value == 'Marked'){
echo 'checked';
	}
} }?> >
		<label for="Marked3">Marked</label>

		<input type="checkbox" id="AOVs3" name="micerule_table_data_tmp[classes][2][]" value="AOVs"
        <?php  if(isset ($meta['classes'][2])){ foreach($meta['classes'][2] as $value){ 
if($value == 'AOVs'){
echo 'checked';
	}
} }?> >
		<label for="AOVs3">AOVs</label>

		<input type="checkbox" id="BIS3" name="micerule_table_data_tmp[classes][2][]" value="BIS"
        <?php if(isset ($meta['classes'][2])){ foreach($meta['classes'][2] as $value){ 
if($value == 'BIS'){
echo 'checked';
	}
}}?> >
		<label for="BIS3">BIS</label>
<br>
<div class="partnership-judge">
<input type="checkbox" id="partnership3" class="pCheck" 
<?php if(isset ($meta['pShip'][2])){
if($meta['pShip'][2] != ''){
echo 'checked';
	}
} ?> >
<label for="partnership3">Partnership</label>

<!--------------NameSelect for Partner----------->
<select name= "micerule_table_data_tmp[pShip][]" id="selectPShip3"  style="display:none">
	<option value=''>Please Select</option>
  <?php

              foreach($posts as $value){ ?>
            <option value="<?php echo $value->display_name;?>" <?php if(isset($meta['pShip'][2])){ echo ($value->display_name == $meta['pShip'][2]) ? ' selected="selected"' : ''; }?>><?php echo $value->display_name;?></option>

        <?php
    }
            ?>
        </select>
</div>
<br><br>
</div>
