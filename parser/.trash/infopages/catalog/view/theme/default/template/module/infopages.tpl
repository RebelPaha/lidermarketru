<script type="text/javascript">
  function goto_path(path){
    if(path!=''){
      window.location = "<?php echo $href?>"+path;
    }
  }
</script>

<div class="box">
  <div class="box-heading"><?php echo $heading_title; ?></div>
  <div class="box-content">
    <div class="box-category">     
      <br />
      <ul><select name='infopages' onchange="goto_path(this.value)" style="width:162px;">
      <option value=""> -- Выбрать -- </option>
      <option value=""></option>
         <?php foreach ($informations as $information) { ?>
        <li>
          <option value="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></option>
        </li>
        <?php } ?>
        </select>
      </ul>
    </div>
  </div>
</div>
