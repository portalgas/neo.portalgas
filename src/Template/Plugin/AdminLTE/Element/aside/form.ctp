<form action="<?php echo $this->Url->build([
    "controller" => "Searchs",
    "action" => "index",
]);?>" method="post" class="sidebar-form">
    
    <div class="input-group">
        <input type="text" name="q" class="form-control" placeholder="Ricerca per codice...">
        <span class="input-group-btn">
            <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
            </button>
        </span>
    </div>

    <div class="sidebar-form-items sidebar-form-radio">
		  <div class="form-group">
		    <label>
		      <input type="radio" name="w" value="OFFERS" class="minimal" checked> <?php echo __('Offers');?>
		    </label>
		    <label>
		      <input type="radio" name="w" value="QUOTES" class="minimal"> <?php echo __('Quotes');?>
		    </label>
		  </div>
	</div>

</form>