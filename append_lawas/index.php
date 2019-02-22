<?php include('../../../config/connect.php');
 ?> 
<tr id="colom_tabel_lama_terima_uang">
 
				        	<td> 
			        			 <div class="row">
			        				<div class="col-md-11"> 
			        					 <select id="akun_append" class="form-control akun_append" > 
			        					 	<option id="tambah_baru_append_akun"><i class="fa fa-plus"></i>&nbsp;Ketuk untuk menambahkan akun</option>
											<?php
												$sql0 = "SELECT * FROM akun";
												$result0 = mysqli_query($connect, $sql0);
												$jumlah=0;
												$kurangi=0;
												$total=0;
												while($data0 = mysqli_fetch_array($result0))
												{
													?>
														<option value="<?php echo $data0['nama_akun'].',';?>" id="akun_append_isi" class="akun_append_isi"><?php echo $data0['kode_akun']." | ".$data0['nama_akun']; ?></option>
													<?php
												}
											?>
											<option id="coba_append"></option>
										</select>
			        				</div>
			        			</div>
				        	</td>
				        	<td>
				        		<div class="row">
					        		<div class="col-md-11">
				        				<input type="text" class="form-control deskripsi">	
				        			</div>
				        		</div>
				        	</td>
				        	<td>
				        		<div class="row">
					        		<div class="col-md-11">
				        				 <select id="pajak_append_select" class="form-control pajak_append_select" data-pajak="0" data-uang_pajak="">
			        					 	<option id="pajak_append_baru"><i class="fa fa-plus" ></i>&nbsp;Ketuk untuk menambahkan pajak</option>
											<?php
												$sql0 = "SELECT * FROM pajak";
												$result0 = mysqli_query($connect, $sql0);
												$jumlah=0;
												$kurangi=0;
												$total=0;
												while($data0 = mysqli_fetch_array($result0))
												{
													?>
														<option value="<?php echo $data0['nama_pajak'].',';?>"  id="pajak_append_isi" class="pajak_append_isi"  data-value="<?php echo $data0['nama_pajak'].'|'.$data0['berapa_persen'];?>"><?php echo $data0['nama_pajak']." | ".$data0['berapa_persen']."%"; ?></option>
													<?php
												}
											?> 
											<!-- <option id="coba"></option> -->
										</select>	
				        			</div>
				        		</div>
				        	</td>
				        	<td>
				        		<div class="row">
					        		<div class="col-md-11">
				        				<input type="text" class="form-control uang" data-uang="0">		
				        			</div>
				        		</div>
				        	</td>
				        	<td>
								<a class="remove_fields dynamic kurangi" href="#"><i class="fa fa-minus"></i></a>
				        	</td>
							
				        </tr>