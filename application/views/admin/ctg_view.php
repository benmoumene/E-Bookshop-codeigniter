<br>
<div class="container">
	<div class="row">
		<div class="col-sm-6 col-xs-7">
			<div id="table-header">Category Detail</div><br>
			<h4>Category Name: <?= strip_tags($ctg_detail->category) ?></h4>
			<p><?= strip_tags($ctg_detail->description) ?></p>
			<hr>
		  <div><h5>Action</h5></div>
	      <?php print '<td>';
	        print '<a href= "'.base_url().'admin/ctg_edit/'.$ctg_detail->id.'" title= "Edit" class="btn btn-success btn-sm"> <i class= "fas fa-wrench"></i></a>&nbsp';
	        print '<a href= "'.base_url().'admin/ctg_delete/'.$ctg_detail->id.'" title= "Delete" class="btn btn-danger btn-sm"> <i class= "fas fa-trash"></i></a>&nbsp';

	        print '</td>'; 
	      ?>
		</div>
		<div class="col-sm-6 col-xs-5">
			
		</div>
	</div>
</div>