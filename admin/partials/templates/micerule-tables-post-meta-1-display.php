<?php

global $wpdb;
global $post;

//Get all user names
$posts = (array) $wpdb->get_results("SELECT display_name FROM " .$wpdb->prefix."users ORDER BY display_name;");

//Get data for Breeds,Age
require_once plugin_dir_path(__FILE__).'micerule-tables-categories-arrays.php';

$breedsOAll=$wpdb->get_results("SELECT option_name FROM ".$wpdb->prefix."options WHERE option_name LIKE 'mrTables%'",ARRAY_A);

$breedsOSelfs=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."options WHERE option_name LIKE 'mrTables%Selfs'",ARRAY_A);

$breedsOTans=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."options WHERE option_name LIKE 'mrTables%Tans'",ARRAY_A);

$breedsOMarked=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."options WHERE option_name LIKE 'mrTables%Marked'",ARRAY_A);

$breedsOSatins=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."options WHERE option_name LIKE 'mrTables%Satins'",ARRAY_A);

$breedsOAOVs=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."options WHERE option_name LIKE 'mrTables%AOVs'",ARRAY_A);

  //wp_nonce_field('micerule_save_metabox_data','micerule_save_nonce_check');

  //Get postmeta for current inputs
  $meta = get_post_meta($post->ID, 'micerule_data_settings',true);
  $scCheck = get_post_meta($post->ID, 'micerule_data_scCheck',true);

?>
<h3 style="display:none;">micerule_tables id="<?php echo $post->ID; ?>"</h3>
<input type="checkbox" id="addShortcode" value="<?php echo $post->ID; ?>"  >
<label for="addShortcode"><strong>Display as event table</strong> (Check this as soon as results have been entered below to have them displayed on the <a href="/show-results">Show Results</a> page)</label>
<br><br>
 <input type="hidden" name="scCheck" id="hValue" value="<?php if(isset($scCheck)){
	echo $scCheck;
    }else{
    echo '0';
    }?>" >
<button type="button" id="addRowU" class="addRow">Add/Hide Unstandardised Row</button>
<button type="button" id="addRowJ" class="addRow">Add/Hide Juvenile Row</button>
<br><br>
<table style="width:100%">
  <tr style="text-align:left">
    <th>Award</th>
    <th>Fancier</th>
    <th>Variety</th>
    <th>Age</th>
    <th>Points</th>
  </tr>
  <tr>
  <!--------------------------Anfang Awards----------------->
    <td name="micerule_table_data[awards][]"><input type="hidden" name="micerule_table_data[awards][]" value="Best in Show">Best in Show</td>
   <!--------------------------Ende Awards----------------->

   <!--------------------------Anfang Name----------------->

    <td><select name= "micerule_table_data[name][]">
    	<option value=''>Please Select</option>
    	<?php

                	foreach($posts as $value){ ?>
            <option value="<?php echo $value->display_name;?>" <?php if(isset($meta['name'][0])){ echo ($value->display_name == $meta['name'][0]) ? ' selected="selected"' : ''; }?>><?php echo $value->display_name;?></option>

            <?php
				}
                ?>
            </select></td>
    <!--------------------------Ende Name----------------->

    <!--------------------------Anfang Breed----------------->
    <td><select name= "micerule_table_data[breeds][]" style="width:200px">
    	<option value=''>No Record</option>
    	<?php

                	foreach($breedsOAll as $value){ ?>
            <option value="<?php echo get_option($value['option_name'])['id'];?>" <?php if(isset($meta['breeds'][0])){ echo (get_option($value['option_name'])['id'] == $meta['breeds'][0]) ? ' selected="selected"' : '';}?>><?php echo get_option($value['option_name'])['name'];?></option>

            <?php
				}
                ?>
            </select></td>
      <!--------------------------Ende Breed----------------->

      <!--------------------------Anfang Age----------------->
    <td><select id="ageBIS1" name= "micerule_table_data[age][]">
    	<?php

                	foreach($age as $value){ ?>
            <option value="<?php echo $value;?>" <?php if(isset($meta['age'][0])){ echo ($value == $meta['age'][0]) ? ' selected="selected"' : ''; }?>><?php echo $value;?></option>

            <?php
				}
                ?>
            </select></td>
    <!--------------------------Ende Age----------------->

    <!--------------------------Anfang Points----------------->
    <td><input type="hidden" name="micerule_table_data[points][]" value="4">4</td>
    <!--------------------------Ende Points----------------->
  </tr>

  <tr>
  <!--------------------------Anfang Awards----------------->
    <td name="micerule_table_data[awards][]"><input type="hidden" name="micerule_table_data[awards][]" value="Best Opposite Age in Show">Best Opposite Age in Show</td>
   <!--------------------------Ende Awards----------------->

   <!--------------------------Anfang Name----------------->
    <td><select name= "micerule_table_data[name][]">
      	<option value=''>Please Select</option>
    	<?php

                	foreach($posts as $value){ ?>
            <option value="<?php echo $value->display_name;?>" <?php if(isset($meta['name'][1])){ echo ($value->display_name == $meta['name'][1]) ? ' selected="selected"' : ''; }?>><?php echo $value->display_name;?></option>

            <?php
				}
                ?>
            </select></td>
    <!--------------------------Ende Name----------------->

    <!--------------------------Anfang Breed----------------->
    <td><select name= "micerule_table_data[breeds][]" style="width:200px">
    	<option value=''>No Record</option>
    	<?php

                	foreach($breedsOAll as $value){ ?>
            <option value="<?php echo get_option($value['option_name'])['id'];?>" <?php if(isset($meta['breeds'][1])){ echo (get_option($value['option_name'])['id'] == $meta['breeds'][1]) ? ' selected="selected"' : '';}?>><?php echo get_option($value['option_name'])['name'];?></option>

            <?php
				}
                ?>
            </select></td>
    <!--------------------------Ende Breed----------------->

    <!--------------------------Anfang Age----------------->
    <td id="tdBIS" ><input type="hidden" name="micerule_table_data[age][]" <?php if(isset($meta['age'][1])){echo 'value="'.$meta['age'][1].'"';}else{echo 'value="U8"';} ?> ><?php if(isset($meta['age'][1])){echo $meta['age'][1];}else{echo 'U8';} ?> </td>
    <!--------------------------Ende Age----------------->

    <!--------------------------Anfang Points----------------->
    <td name="micerule_table_data[points][]"><input type="hidden" name="micerule_table_data[points][]" value="3">3</td>
    <!--------------------------Ende Points----------------->
  </tr>

  <tr>
  <!--------------------------Anfang Awards----------------->
    <td name="micerule_table_data[awards][]"><input type="hidden" name="micerule_table_data[awards][]" value="Best Self">Best Self</td>
   <!--------------------------Ende Awards----------------->

   <!--------------------------Anfang Name----------------->
    <td><select name= "micerule_table_data[name][]">
    	<option value=''>Please Select</option>
    	<?php

                	foreach($posts as $value){ ?>
            <option value="<?php echo $value->display_name;?>" <?php if(isset($meta['name'][2])){ echo ($value->display_name == $meta['name'][2]) ? ' selected="selected"' : ''; }?>><?php echo $value->display_name;?></option>

            <?php
				}
                ?>
            </select></td>
    <!--------------------------Ende Name----------------->

    <!--------------------------Anfang Breed----------------->
    <td><select name= "micerule_table_data[breeds][]" style="width:200px">
    	<option value=''>No Record</option>
    	<?php

                	foreach($breedsOSelfs as $value){ ?>
            <option value="<?php echo get_option($value['option_name'])['id'];?>" <?php if(isset($meta['breeds'][2])){ echo (get_option($value['option_name'])['id'] == $meta['breeds'][2]) ? ' selected="selected"' : '';}?>><?php echo get_option($value['option_name'])['name'];?></option>

            <?php
				}
                ?>
            </select></td>
    <!--------------------------Ende Breed----------------->

    <!--------------------------Anfang Age----------------->
    <td><select id="ageBSelf1" name= "micerule_table_data[age][]">
    	<?php

                	foreach($age as $value){ ?>
            <option value="<?php echo $value;?>" <?php if(isset($meta['age'][2])){ echo ($value == $meta['age'][2]) ? ' selected="selected"' : ''; }?>><?php echo $value;?></option>

            <?php
				}
                ?>
            </select></td>
    <!--------------------------Ende Age----------------->

    <!--------------------------Anfang Points----------------->
    <td name="micerule_table_data[points][]"><input type="hidden" name="micerule_table_data[points][]" value="2">2</td>
    <!--------------------------Ende Points----------------->
  </tr>

  <tr>
  <!--------------------------Anfang Awards----------------->
    <td name="micerule_table_data[awards][]"><input type="hidden" name="micerule_table_data[awards][]" value="Best Opposite Age Self">Best Opposite Age Self</td>
   <!--------------------------Ende Awards----------------->

   <!--------------------------Anfang Name----------------->
    <td><select name= "micerule_table_data[name][]">
    	<option value=''>Please Select</option>
      <?php

                  foreach($posts as $value){ ?>
            <option value="<?php echo $value->display_name;?>" <?php if(isset($meta['name'][3])){ echo ($value->display_name == $meta['name'][3]) ? ' selected="selected"' : ''; }?>><?php echo $value->display_name;?></option>

            <?php
        }
                ?>
            </select></td>
    <!--------------------------Ende Name----------------->

    <!--------------------------Anfang Breed----------------->
    <td><select name= "micerule_table_data[breeds][]" style="width:200px">
    	<option value=''>No Record</option>
      <?php
			foreach($breedsOSelfs as $value){ ?>
                  <option value="<?php echo get_option($value['option_name'])['id'];?>" <?php if(isset($meta['breeds'][3])){ echo (get_option($value['option_name'])['id'] == $meta['breeds'][3]) ? ' selected="selected"' : '';}?>><?php echo get_option($value['option_name'])['name'];?></option>

            <?php
        }
                ?>
            </select></td>
    <!--------------------------Ende Breed----------------->

    <!--------------------------Anfang Age----------------->
    <td id="tdBSelf"><input type="hidden" name="micerule_table_data[age][]" <?php if(isset($meta['age'][3])){echo 'value="'.$meta['age'][3].'"';}else{echo 'value="U8"';} ?> ><?php if(isset($meta['age'][3])){echo $meta['age'][3];}else{echo 'U8';} ?> </td>
    <!--------------------------Ende Age----------------->

    <!--------------------------Anfang Points----------------->
    <td name="micerule_table_data[points][]"><input type="hidden" name="micerule_table_data[points][]" value="1">1</td>
    <!--------------------------Ende Points----------------->
  </tr>

  <tr>
  <!--------------------------Anfang Awards----------------->
    <td name="micerule_table_data[awards][]"><input type="hidden" name="micerule_table_data[awards][]" value="Best Marked">Best Marked</td>
   <!--------------------------Ende Awards----------------->

   <!--------------------------Anfang Name----------------->
    <td><select name= "micerule_table_data[name][]">
    	<option value=''>Please Select</option>
      <?php

                  foreach($posts as $value){ ?>
            <option value="<?php echo $value->display_name;?>" <?php if(isset($meta['name'][4])){ echo ($value->display_name == $meta['name'][4]) ? ' selected="selected"' : ''; }?>><?php echo $value->display_name;?></option>

            <?php
        }
                ?>
            </select></td>
    <!--------------------------Ende Name----------------->

    <!--------------------------Anfang Breed----------------->
    <td><select name= "micerule_table_data[breeds][]" style="width:200px">
    	<option value=''>No Record</option>
      <?php

                  foreach($breedsOMarked as $value){ ?>
            <option value="<?php echo get_option($value['option_name'])['id'];?>" <?php if(isset($meta['breeds'][4])){ echo (get_option($value['option_name'])['id'] == $meta['breeds'][4]) ? ' selected="selected"' : '';}?>><?php echo get_option($value['option_name'])['name'];?></option>

            <?php
        }
                ?>
            </select></td>
    <!--------------------------Ende Breed----------------->

    <!--------------------------Anfang Age----------------->
    <td><select id="ageBM1" name= "micerule_table_data[age][]">
      <?php

                  foreach($age as $value){ ?>
            <option value="<?php echo $value;?>" <?php if(isset($meta['age'][4])){ echo ($value == $meta['age'][4]) ? ' selected="selected"' : ''; }?>><?php echo $value;?></option>

            <?php
        }
                ?>
            </select></td>
    <!--------------------------Ende Age----------------->

    <!--------------------------Anfang Points----------------->
    <td name="micerule_table_data[points][]"><input type="hidden" name="micerule_table_data[points][]" value="2">2</td>
    <!--------------------------Ende Points----------------->
  </tr>

  <tr>
  <!--------------------------Anfang Awards----------------->
    <td name="micerule_table_data[awards][]"><input type="hidden" name="micerule_table_data[awards][]" value="Best Opposite Age Marked">Best Opposite Age Marked</td>
   <!--------------------------Ende Awards----------------->

   <!--------------------------Anfang Name----------------->
    <td><select name= "micerule_table_data[name][]">
    	<option value=''>Please Select</option>
      <?php

                  foreach($posts as $value){ ?>
            <option value="<?php echo $value->display_name;?>" <?php if(isset($meta['name'][5])){ echo ($value->display_name == $meta['name'][5]) ? ' selected="selected"' : ''; }?>><?php echo $value->display_name;?></option>

            <?php
        }
                ?>
            </select></td>
    <!--------------------------Ende Name----------------->

    <!--------------------------Anfang Breed----------------->
    <td><select name= "micerule_table_data[breeds][]" style="width:200px">
    	<option value=''>No Record</option>
      <?php

                  foreach($breedsOMarked as $value){ ?>
            <option value="<?php echo get_option($value['option_name'])['id'];?>" <?php if(isset($meta['breeds'][5])){ echo (get_option($value['option_name'])['id'] == $meta['breeds'][5]) ? ' selected="selected"' : '';}?>><?php echo get_option($value['option_name'])['name'];?></option>

            <?php
        }
                ?>
            </select></td>
    <!--------------------------Ende Breed----------------->

    <!--------------------------Anfang Age----------------->
    <td id="tdBM" ><input type="hidden" name="micerule_table_data[age][]" <?php if(isset($meta['age'][5])){echo 'value="'.$meta['age'][5].'"';}else{echo 'value="U8"';} ?> ><?php if(isset($meta['age'][5])){echo $meta['age'][5];}else{echo 'U8';} ?> </td>
    <!--------------------------Ende Age----------------->

    <!--------------------------Anfang Points----------------->
    <td name="micerule_table_data[points][]"><input type="hidden" name="micerule_table_data[points][]" value="1">1</td>
    <!--------------------------Ende Points----------------->
  </tr>

  <tr>
  <!--------------------------Anfang Awards----------------->
    <td name="micerule_table_data[awards][]"><input type="hidden" name="micerule_table_data[awards][]" value="Best Tan">Best Tan</td>
   <!--------------------------Ende Awards----------------->

   <!--------------------------Anfang Name----------------->
    <td><select name= "micerule_table_data[name][]">
    	<option value=''>Please Select</option>
      <?php

                  foreach($posts as $value){ ?>
            <option value="<?php echo $value->display_name;?>" <?php if(isset($meta['name'][6])){ echo ($value->display_name == $meta['name'][6]) ? ' selected="selected"' : ''; }?>><?php echo $value->display_name;?></option>

            <?php
        }
                ?>
            </select></td>
    <!--------------------------Ende Name----------------->

    <!--------------------------Anfang Breed----------------->
    <td><select name= "micerule_table_data[breeds][]" style="width:200px">
    	<option value=''>No Record</option>
      <?php

                  foreach($breedsOTans as $value){ ?>
            <option value="<?php echo get_option($value['option_name'])['id'];?>" <?php if(isset($meta['breeds'][6])){ echo (get_option($value['option_name'])['id'] == $meta['breeds'][6]) ? ' selected="selected"' : '';}?>><?php echo get_option($value['option_name'])['name'];?></option>

            <?php
        }
                ?>
            </select></td>
    <!--------------------------Ende Breed----------------->

    <!--------------------------Anfang Age----------------->
    <td><select id="ageBT1" name= "micerule_table_data[age][]">
      <?php

                  foreach($age as $value){ ?>
            <option value="<?php echo $value;?>" <?php if(isset($meta['age'][6])){ echo ($value == $meta['age'][6]) ? ' selected="selected"' : ''; }?>><?php echo $value;?></option>

            <?php
        }
                ?>
            </select></td>
    <!--------------------------Ende Age----------------->

    <!--------------------------Anfang Points----------------->
    <td name="micerule_table_data[points][]"><input type="hidden" name="micerule_table_data[points][]" value="2">2</td>
    <!--------------------------Ende Points----------------->
  </tr>

  <tr>
  <!--------------------------Anfang Awards----------------->
    <td name="micerule_table_data[awards][]"><input type="hidden" name="micerule_table_data[awards][]" value="Best Opposite Age Tan">Best Opposite Age Tan</td>
   <!--------------------------Ende Awards----------------->

   <!--------------------------Anfang Name----------------->
    <td><select name= "micerule_table_data[name][]">
    	<option value=''>Please Select</option>
      <?php

                  foreach($posts as $value){ ?>
            <option value="<?php echo $value->display_name;?>" <?php if(isset($meta['name'][7])){ echo ($value->display_name == $meta['name'][7]) ? ' selected="selected"' : ''; }?>><?php echo $value->display_name;?></option>

            <?php
        }
                ?>
            </select></td>
    <!--------------------------Ende Name----------------->

    <!--------------------------Anfang Breed----------------->
    <td><select name= "micerule_table_data[breeds][]" style="width:200px">
    	<option value=''>No Record</option>
      <?php

                  foreach($breedsOTans as $value){ ?>
            <option value="<?php echo get_option($value['option_name'])['id'];?>" <?php if(isset($meta['breeds'][7])){ echo (get_option($value['option_name'])['id'] == $meta['breeds'][7]) ? ' selected="selected"' : '';}?>><?php echo get_option($value['option_name'])['name'];?></option>

            <?php
        }
                ?>
            </select></td>
    <!--------------------------Ende Breed----------------->

    <!--------------------------Anfang Age----------------->
    <td id="tdBT"><input type="hidden" name="micerule_table_data[age][]" <?php if(isset($meta['age'][7])){echo 'value="'.$meta['age'][7].'"';}else{echo 'value="U8"';} ?> ><?php if(isset($meta['age'][7])){echo $meta['age'][7];}else{echo 'U8';} ?> </td>
    <!--------------------------Ende Age----------------->

    <!--------------------------Anfang Points----------------->
    <td name="micerule_table_data[points][]"><input type="hidden" name="micerule_table_data[points][]" value="1">1</td>
    <!--------------------------Ende Points----------------->
  </tr>

  <tr>
  <!--------------------------Anfang Awards----------------->
    <td name="micerule_table_data[awards][]"><input type="hidden" name="micerule_table_data[awards][]" value="Best Satin">Best Satin</td>
   <!--------------------------Ende Awards----------------->

   <!--------------------------Anfang Name----------------->
    <td><select name= "micerule_table_data[name][]">
    	<option value=''>Please Select</option>
      <?php

                  foreach($posts as $value){ ?>
            <option value="<?php echo $value->display_name;?>" <?php if(isset($meta['name'][8])){ echo ($value->display_name == $meta['name'][8]) ? ' selected="selected"' : ''; }?>><?php echo $value->display_name;?></option>

            <?php
        }
                ?>
            </select></td>
    <!--------------------------Ende Name----------------->

    <!--------------------------Anfang Breed----------------->
    <td><select name= "micerule_table_data[breeds][]" style="width:200px">
    	<option value=''>No Record</option>
      <?php

                  foreach($breedsOSatins as $value){ ?>
            <option value="<?php echo get_option($value['option_name'])['id'];?>" <?php if(isset($meta['breeds'][8])){ echo (get_option($value['option_name'])['id'] == $meta['breeds'][8]) ? ' selected="selected"' : '';}?>><?php echo get_option($value['option_name'])['name'];?></option>

            <?php
        }
                ?>
            </select></td>
    <!--------------------------Ende Breed----------------->

    <!--------------------------Anfang Age----------------->
    <td><select id="ageBS1" name= "micerule_table_data[age][]">
      <?php

                  foreach($age as $value){ ?>
            <option value="<?php echo $value;?>" <?php if(isset($meta['age'][8])){ echo ($value == $meta['age'][8]) ? ' selected="selected"' : ''; }?>><?php echo $value;?></option>

            <?php
        }
                ?>
            </select></td>
    <!--------------------------Ende Age----------------->

    <!--------------------------Anfang Points----------------->
    <td name="micerule_table_data[points][]"><input type="hidden" name="micerule_table_data[points][]" value="2">2</td>
    <!--------------------------Ende Points----------------->
  </tr>

  <tr>
  <!--------------------------Anfang Awards----------------->
    <td name="micerule_table_data[awards][]"><input type="hidden" name="micerule_table_data[awards][]" value="Best Opposite Age Satin">Best Opposite Satin</td>
   <!--------------------------Ende Awards----------------->

   <!--------------------------Anfang Name----------------->
    <td><select name= "micerule_table_data[name][]">
    	<option value=''>Please Select</option>
      <?php

                  foreach($posts as $value){ ?>
            <option value="<?php echo $value->display_name;?>" <?php if(isset($meta['name'][9])){ echo ($value->display_name == $meta['name'][9]) ? ' selected="selected"' : ''; }?>><?php echo $value->display_name;?></option>

            <?php
        }
                ?>
            </select></td>
    <!--------------------------Ende Name----------------->

    <!--------------------------Anfang Breed----------------->
    <td><select name= "micerule_table_data[breeds][]" style="width:200px">
    	<option value=''>No Record</option>
      <?php

                  foreach($breedsOSatins as $value){ ?>
            <option value="<?php echo get_option($value['option_name'])['id'];?>" <?php if(isset($meta['breeds'][9])){ echo (get_option($value['option_name'])['id'] == $meta['breeds'][9]) ? ' selected="selected"' : '';}?>><?php echo get_option($value['option_name'])['name'];?></option>

            <?php
        }
                ?>
            </select></td>
    <!--------------------------Ende Breed----------------->

    <!--------------------------Anfang Age----------------->
    <td id="tdBS"><input type="hidden" name="micerule_table_data[age][]" <?php if(isset($meta['age'][9])){echo 'value="'.$meta['age'][9].'"';}else{echo 'value="U8"';} ?> ><?php if(isset($meta['age'][9])){echo $meta['age'][9];}else{echo 'U8';} ?> </td>
    <!--------------------------Ende Age----------------->

    <!--------------------------Anfang Points----------------->
    <td name="micerule_table_data[points][]"><input type="hidden" name="micerule_table_data[points][]" value="1">1</td>
    <!--------------------------Ende Points----------------->
  </tr>

  <tr>
  <!--------------------------Anfang Awards----------------->
    <td name="micerule_table_data[awards][]"><input type="hidden" name="micerule_table_data[awards][]" value="Best AOV">Best AOV</td>
   <!--------------------------Ende Awards----------------->

   <!--------------------------Anfang Name----------------->
    <td><select name= "micerule_table_data[name][]">
    	<option value=''>Please Select</option>
      <?php

                  foreach($posts as $value){ ?>
            <option value="<?php echo $value->display_name;?>" <?php if(isset($meta['name'][10])){ echo ($value->display_name == $meta['name'][10]) ? ' selected="selected"' : ''; }?>><?php echo $value->display_name;?></option>

            <?php
        }
                ?>
            </select></td>
    <!--------------------------Ende Name----------------->

    <!--------------------------Anfang Breed----------------->
    <td><select name= "micerule_table_data[breeds][]" style="width:200px">
    	<option value=''>No Record</option>
      <?php

                  foreach($breedsOAOVs as $value){ ?>
            <option value="<?php echo get_option($value['option_name'])['id'];?>" <?php if(isset($meta['breeds'][10])){ echo (get_option($value['option_name'])['id'] == $meta['breeds'][10]) ? ' selected="selected"' : '';}?>><?php echo get_option($value['option_name'])['name'];?></option>

            <?php
        }
                ?>
            </select></td>
    <!--------------------------Ende Breed----------------->

    <!--------------------------Anfang Age----------------->
    <td><select id="ageBAOV1" name= "micerule_table_data[age][]">
      <?php

                  foreach($age as $value){ ?>
            <option value="<?php echo $value;?>" <?php if(isset($meta['age'][10])){ echo ($value == $meta['age'][10]) ? ' selected="selected"' : ''; }?>><?php echo $value;?></option>

            <?php
        }
                ?>
            </select></td>
    <!--------------------------Ende Age----------------->

    <!--------------------------Anfang Points----------------->
    <td name="micerule_table_data[points][]"><input type="hidden" name="micerule_table_data[points][]" value="2">2</td>
    <!--------------------------Ende Points----------------->
  </tr>

  <tr>
  <!--------------------------Anfang Awards----------------->
    <td name="micerule_table_data[awards][]"><input type="hidden" name="micerule_table_data[awards][]" value="Best Opposite Age AOV">Best Opposite Age AOV</td>
   <!--------------------------Ende Awards----------------->

   <!--------------------------Anfang Name----------------->
    <td><select name= "micerule_table_data[name][]">
    	<option value=''>Please Select</option>
      <?php

                  foreach($posts as $value){ ?>
            <option value="<?php echo $value->display_name;?>" <?php if(isset($meta['name'][11])){ echo ($value->display_name == $meta['name'][11]) ? ' selected="selected"' : ''; }?>><?php echo $value->display_name;?></option>

            <?php
        }
                ?>
            </select></td>
    <!--------------------------Ende Name----------------->

    <!--------------------------Anfang Breed----------------->
    <td><select name= "micerule_table_data[breeds][]" style="width:200px">
    	<option value=''>No Record</option>
      <?php

                  foreach($breedsOAOVs as $value){ ?>
            <option value="<?php echo get_option($value['option_name'])['id'];?>" <?php if(isset($meta['breeds'][11])){ echo (get_option($value['option_name'])['id'] == $meta['breeds'][11]) ? ' selected="selected"' : '';}?>><?php echo get_option($value['option_name'])['name'];?></option>

            <?php
        }
                ?>
            </select></td>
    <!--------------------------Ende Breed----------------->

    <!--------------------------Anfang Age----------------->
    <td id="tdBAOV"><input type="hidden" name="micerule_table_data[age][]" <?php if(isset($meta['age'][11])){echo 'value="'.$meta['age'][11].'"';}else{echo 'value="U8"';} ?> ><?php if(isset($meta['age'][11])){echo $meta['age'][11];}else{echo 'U8';} ?> </td>
    <!--------------------------Ende Age----------------->

    <!--------------------------Anfang Points----------------->
    <td name="micerule_table_data[points][]"><input type="hidden" name="micerule_table_data[points][]" value="1">1</td>
    <!--------------------------Ende Points----------------->
  </tr>


  <!------------Unstandardised------------->
  <tr id="unstandardisedRow" <?php if(isset($meta['breeds'][12])==false){echo' style="display:none"';} ?> >
  <!--------------------------Anfang Awards----------------->
    <td name="micerule_table_data[awards][]"><input type="hidden" id="uAward"<?php if(isset($meta['breeds'][12])){echo 'name="micerule_table_data[awards][]"';} ?> value="Best Unstandardised"  >Best Unstandardised</td>
   <!--------------------------Ende Awards----------------->

   <!--------------------------Anfang Name----------------->

    <td><select id="uName" <?php if(isset($meta['name'][12])){echo  'name= "micerule_table_data[name][]"';} ?> >
    	<option value=''>Please Select</option>
      <?php

                  foreach($posts as $value){ ?>
            <option value="<?php echo $value->display_name;?>" <?php if(isset($meta['name'][12])){ echo ($value->display_name == $meta['name'][12]) ? ' selected="selected"' : ''; }?>><?php echo $value->display_name;?></option>

            <?php
        }
                ?>
            </select></td>
    <!--------------------------Ende Name----------------->

    <!--------------------------Anfang Breed----------------->
    <td><input type="text" id="uBreed" style="width:200px"<?php if(isset($meta['breeds'][12])){echo 'name= "micerule_table_data[breeds][]"';} ?> value=" <?php if(isset($meta['breeds'][12])){ echo $meta['breeds'][12];} ?>"></td>
      <!--------------------------Ende Breed----------------->

      <!--------------------------Anfang Age----------------->
    <td></td>
    <!--------------------------Ende Age----------------->

    <!--------------------------Anfang Points----------------->
    <td><input type="hidden" id="uPoints" <?php if(isset($meta['breeds'][12])){echo 'name="micerule_table_data[points][]"';} ?> value="0">0</td>
    <!--------------------------Ende Points----------------->

  </tr>

  <!------------Juvenile------------->
  <tr id="juvenileRow" <?php if(isset($meta['breeds'][13])==false){echo' style="display:none"';} ?> >
  <!--------------------------Anfang Awards----------------->
    <td name="micerule_table_data[awards][]"><input type="hidden" id="jAward" <?php if(isset($meta['breeds'][13])){echo 'name="micerule_table_data[awards][]"';} ?> value="Best Juvenile"  >Best Juvenile</td>
   <!--------------------------Ende Awards----------------->

   <!--------------------------Anfang Name----------------->

    <td><select id="jName" <?php if(isset($meta['name'][13])){echo  'name= "micerule_table_data[name][]"';} ?> >
    	<option value=''>Please Select</option>
      <?php

                  foreach($posts as $value){ ?>
            <option value="<?php echo $value->display_name;?>" <?php if(isset($meta['name'][13])){ echo ($value->display_name == $meta['name'][13]) ? ' selected="selected"' : ''; }?>><?php echo $value->display_name;?></option>

            <?php
        }
                ?>
            </select></td>
    <!--------------------------Ende Name----------------->

    <!--------------------------Anfang Breed----------------->
    <td><input type="text" id="jBreed" style="width:200px"<?php if(isset($meta['breeds'][13])){echo 'name= "micerule_table_data[breeds][]"';} ?> value=" <?php if(isset($meta['breeds'][13])){ echo $meta['breeds'][13];} ?>"></td>
      <!--------------------------Ende Breed----------------->

      <!--------------------------Anfang Age----------------->
    <td></td>
    <!--------------------------Ende Age----------------->

    <!--------------------------Anfang Points----------------->
    <td><input type="hidden" id="jPoints" <?php if(isset($meta['breeds'][13])){echo 'name="micerule_table_data[points][]"';} ?> value="0">0</td>
    <!--------------------------Ende Points----------------->

  </tr>

</table>


    <?php

    include("micerule-tables-post-meta-2-display.php");
