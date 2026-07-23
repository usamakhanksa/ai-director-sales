<div class="form-group">
                      <label for="default_date_format"><?php echo mlx_get_lang('Date Format'); ?></label>
                      <select class="form-control select2_elem" name="options[default_date_format]" id="default_date_format" >
						<option value="mm/dd/yyyy" 
						<?php if(isset($default_date_format) && 'mm/dd/yyyy' == $default_date_format) echo "selected=selected";?>>MM/DD/YYYY</option>
						<option value="dd/mm/yyyy" 
						<?php if(isset($default_date_format) && 'dd/mm/yyyy' == $default_date_format) echo "selected=selected";?>>DD/MM/YYYY</option>
					  </select>
					</div>
					
					<div class="form-group">
                      <label for="skins"><?php echo mlx_get_lang('Admin Skins'); ?></label>
					  <input type="hidden" name="options[skin]" class="option_skin" value="<?php if(isset($skin)) echo $skin; ?>">
                      <div class="skin-container row">
						 <ul class="list-unstyled clearfix ">
							<li class="col-md-2">
								<a href="javascript:void(0);" data-skin="skin-blue" 
								style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" 
								class="clearfix <?php if((!isset($skin)) || (isset($skin) && $skin == 'skin-blue')) echo ''; else echo 'full-opacity-hover'; ?>">
									<div>
										<span style="display:block; width: 20%; float: left; height: 7px; background: #367fa9;"></span>
										<span class="bg-light-blue" style="display:block; width: 80%; float: left; height: 7px;"></span>
									</div>
									<div>
										<span style="display:block; width: 20%; float: left; height: 20px; background: #222d32;"></span>
										<span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span>
									</div>
								</a>
								<p class="text-center"><?php echo mlx_get_lang('Blue'); ?></p>
							</li>
							<li class="col-md-2">
								<a href="javascript:void(0);" data-skin="skin-black" 
								style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix <?php if(isset($skin) && $skin == 'skin-black') echo ''; else echo 'full-opacity-hover'; ?>">
									<div style="box-shadow: 0 0 2px rgba(0,0,0,0.1)" class="clearfix">
										<span style="display:block; width: 20%; float: left; height: 7px; background: #fefefe;"></span>
										<span style="display:block; width: 80%; float: left; height: 7px; background: #fefefe;"></span>
									</div>
									<div>
										<span style="display:block; width: 20%; float: left; height: 20px; background: #222;"></span>
										<span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span>
									</div>
								</a>
								<p class="text-center"><?php echo mlx_get_lang('Black'); ?></p>
							</li>
							<li class="col-md-2">
								<a href="javascript:void(0);" data-skin="skin-purple" 
								style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix <?php if(isset($skin) && $skin == 'skin-purple') echo ''; else echo 'full-opacity-hover'; ?>">
									<div>
										<span style="display:block; width: 20%; float: left; height: 7px;" class="bg-purple-active"></span>
										<span class="bg-purple" style="display:block; width: 80%; float: left; height: 7px;"></span>
									</div>
									<div>
										<span style="display:block; width: 20%; float: left; height: 20px; background: #222d32;"></span>
										<span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span>
									</div>
								</a>
								<p class="text-center"><?php echo mlx_get_lang('Purple'); ?></p>
							</li>
							<li class="col-md-2">
								<a href="javascript:void(0);" data-skin="skin-green" 
								style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix <?php if(isset($skin) && $skin == 'skin-green') echo ''; else echo 'full-opacity-hover'; ?>">
									<div>
										<span style="display:block; width: 20%; float: left; height: 7px;" class="bg-green-active"></span>
										<span class="bg-green" style="display:block; width: 80%; float: left; height: 7px;"></span>
									</div>
									<div>
										<span style="display:block; width: 20%; float: left; height: 20px; background: #222d32;"></span>
										<span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span>
									</div>
								</a>
								<p class="text-center"><?php echo mlx_get_lang('Green'); ?></p>
							</li>
							<li class="col-md-2">
								<a href="javascript:void(0);" data-skin="skin-red" 
								style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix <?php if(isset($skin) && $skin == 'skin-red') echo ''; else echo 'full-opacity-hover'; ?>">
									<div>
										<span style="display:block; width: 20%; float: left; height: 7px;" class="bg-red-active"></span>
										<span class="bg-red" style="display:block; width: 80%; float: left; height: 7px;"></span>
									</div>
									<div>
										<span style="display:block; width: 20%; float: left; height: 20px; background: #222d32;"></span>
										<span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span>
									</div>
								</a>
								<p class="text-center"><?php echo mlx_get_lang('Red'); ?></p>
							</li>
							<li class="col-md-2">
								<a href="javascript:void(0);" data-skin="skin-yellow" 
								style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix <?php if(isset($skin) && $skin == 'skin-yellow') echo ''; else echo 'full-opacity-hover'; ?>">
									<div>
										<span style="display:block; width: 20%; float: left; height: 7px;" class="bg-yellow-active"></span>
										<span class="bg-yellow" style="display:block; width: 80%; float: left; height: 7px;"></span>
									</div>
									<div>
									    <span style="display:block; width: 20%; float: left; height: 20px; background: #222d32;"></span>
										<span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span>
									</div>
								</a>
								<p class="text-center"><?php echo mlx_get_lang('Yellow'); ?></p>
							</li>
							<li class="col-md-2">
								<a href="javascript:void(0);" data-skin="skin-blue-light" 
								style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix <?php if(isset($skin) && $skin == 'skin-blue-light') echo ''; else echo 'full-opacity-hover'; ?>">
									<div>
										<span style="display:block; width: 20%; float: left; height: 7px; background: #367fa9;"></span>
										<span class="bg-light-blue" style="display:block; width: 80%; float: left; height: 7px;"></span>
									</div>
									<div>
										<span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc;"></span>
										<span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span>
									</div>
								</a>
								<p class="text-center no-margin" ><?php echo mlx_get_lang('Blue Light'); ?></p>
							</li>
							<li class="col-md-2">
								<a href="javascript:void(0);" data-skin="skin-black-light" 
								style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix <?php if(isset($skin) && $skin == 'skin-black-light') echo ''; else echo 'full-opacity-hover'; ?>">
									<div style="box-shadow: 0 0 2px rgba(0,0,0,0.1)" class="clearfix">
										<span style="display:block; width: 20%; float: left; height: 7px; background: #fefefe;"></span>
										<span style="display:block; width: 80%; float: left; height: 7px; background: #fefefe;"></span>
									</div>
									<div>
										<span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc;"></span>
										<span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span>
									</div>
								 </a>
								 <p class="text-center no-margin" ><?php echo mlx_get_lang('Black Light'); ?></p>
								</li>
								<li class="col-md-2">
									<a href="javascript:void(0);" data-skin="skin-purple-light" 
									style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix <?php if(isset($skin) && $skin == 'skin-purple-light') echo ''; else echo 'full-opacity-hover'; ?>">
										<div>
											<span style="display:block; width: 20%; float: left; height: 7px;" class="bg-purple-active"></span>
											<span class="bg-purple" style="display:block; width: 80%; float: left; height: 7px;"></span>
										</div>
										<div>
											<span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc;"></span>
											<span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span>
										</div>
									 </a>
									 <p class="text-center no-margin" ><?php echo mlx_get_lang('Purple Light'); ?></p>
								</li>
								<li class="col-md-2">
									<a href="javascript:void(0);" data-skin="skin-green-light" 
									style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix <?php if(isset($skin) && $skin == 'skin-green-light') echo ''; else echo 'full-opacity-hover'; ?>">
										<div>
											<span style="display:block; width: 20%; float: left; height: 7px;" class="bg-green-active"></span>
											<span class="bg-green" style="display:block; width: 80%; float: left; height: 7px;"></span>
										</div>
										<div>
											<span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc;"></span>
											<span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span>
										</div>
									</a>
									<p class="text-center no-margin" ><?php echo mlx_get_lang('Green Light'); ?></p>
								</li>
								<li class="col-md-2">
									<a href="javascript:void(0);" data-skin="skin-red-light" 
									style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix <?php if(isset($skin) && $skin == 'skin-red-light') echo ''; else echo 'full-opacity-hover'; ?>">
										<div>
											<span style="display:block; width: 20%; float: left; height: 7px;" class="bg-red-active"></span>
											<span class="bg-red" style="display:block; width: 80%; float: left; height: 7px;"></span>
										</div>
										<div>
											<span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc;"></span>
											<span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span>
										</div>
									</a>
									<p class="text-center no-margin" ><?php echo mlx_get_lang('Red Light'); ?></p>
								</li>
								<li class="col-md-2">
									<a href="javascript:void(0);" data-skin="skin-yellow-light" 
									style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix <?php if(isset($skin) && $skin == 'skin-yellow-light') echo ''; else echo 'full-opacity-hover'; ?>">
										<div>
											<span style="display:block; width: 20%; float: left; height: 7px;" class="bg-yellow-active"></span>
											<span class="bg-yellow" style="display:block; width: 80%; float: left; height: 7px;"></span>
										</div>
										<div>
											<span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc;"></span>
											<span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span>
										</div>
									</a>
									<p class="text-center no-margin" ><?php echo mlx_get_lang('Yellow Light'); ?></p>
								</li>
							</ul>
					  </div>
					</div>