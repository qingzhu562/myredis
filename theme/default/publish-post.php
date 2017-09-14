<?php include('header.php'); ?>
	<div class="container">
		<div class="row">
			<div class="col-sm-10 col-sm-offset-1 col">
			<form method="post" role="form" action="<?php echo $redis->get('site_url'); ?>/do/pubform-post.php">
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="publish-step-1">
							<div class="form-group">
								<label>
									标题
								</label>
								<input id="pub-title" type="text" name="page[title]" class="form-control" value="<?php if($id) : echo $redis->hget('post:'.$id,'title'); else : echo $redis->hget('user_draft_post:'.$user_id,'title'); endif; ?>">
							</div>
							<div class="form-group">
								<label>
									分类
								</label>
								<div class="clearfix"></div>
								<?php foreach($redis->zrange('term:post',0,-1) as $title) : ?>
								<label class="radio-inline">
									<input type="radio" name="page[term]" value="<?php echo $redis->zscore('term:post',$title); ?>" <?php if($redis->hget('post:'.$id,'term')==$redis->zscore('term:post',$title)) : ?>checked<?php endif; ?>> <?php echo $title; ?>
								</label>
								<?php endforeach; ?>
							</div>
							<div class="form-group">
								<label>
									封面图片
								</label>
								<div class="clearfix"></div>
								<?php
									if($id) :
										$fmimg_full = $redis->hget('post:'.$id,'fmimg');
									else :
										$fmimg_full = $redis->hget('user_draft_post:'.$user_id,'fmimg');
									endif;
									if($fmimg_full=='') :
										$fmimg_full = $redis->get('site_url').'/public/img/upload.jpg';
									endif;
								?>
								<img id="default-img1" class="mb-10 pull-left mr-20" src="<?php echo $fmimg_full; ?>" width="300">
								<div class="pub-imgadd pull-left">
									<button type="button" class="btn btn-default btn-lg">上传图片</button>
									<input type="file" class="picfile" onchange="readFile(this,1)" />
								</div>
								<div class="clearfix"></div>
								<textarea name="page[fmimg]" rows="1" class="form-control" id="pub-input1" placeholder="http://"><?php if($id) : if($redis->hget('post:'.$id,'fmimg')) : echo $fmimg_full; endif; else : if($redis->hget('user_draft_post:'.$user_id,'fmimg')) : echo $fmimg_full; endif; endif; ?></textarea>
								<script>
								function readFile(obj,id){
											$('#default-img'+id).attr('src','<?php echo $redis->get('site_url'); ?>/public/img/loading.gif');
											var file = obj.files[0];
											//判断类型是不是图片
											if(!/image\/\w+/.test(file.type)){
															alert("请确保文件为图像类型");
															return false;
											}

											data = new FormData();
    									data.append("file", file);
											$.ajax({
													data: data,
													type: "POST",
													url: "<?php echo $redis->get('site_url'); ?>/do/imgupload-sm.php",
													cache: false,
													contentType: false,
        									processData: false,
													success: function(url) {
														$('#default-img'+id).attr('src',url);
														$('#pub-input'+id).html(url);
													},
													error : function(data) {
														alert('上传失败');
                                                        console.log(data);
														$('#default-img'+id).attr('src','<?php echo $redis->get('site_url'); ?>/public/img/upload.jpg');
													}
											});
							}
								</script>
							</div>
							<link href="<?php echo $redis->get('site_url'); ?>/public/sn/summernote.css" rel="stylesheet">
							<script src="<?php echo $redis->get('site_url'); ?>/public/sn/summernote.js"></script>
							<script src="<?php echo $redis->get('site_url'); ?>/public/sn/lang/summernote-zh-CN.min.js"></script>
							<div class="form-group">
								<label>
									内容
								</label>
								<div id="summernote"><?php if($id) : echo $redis->hget('post:'.$id,'content'); else : echo $redis->hget('user_draft_post:'.$user_id,'content'); endif; ?></div>
								<textarea name="page[content]" id="post-content-textarea" class="hidden"><?php if($id) : echo $redis->hget('post:'.$id,'content'); else : echo $redis->hget('user_draft_post:'.$user_id,'content'); endif; ?></textarea>
							</div>
							<div class="form-group">
								<label>
									标签 多个标签以空格隔开
								</label>
								<input id="pub-tags" type="text" name="page[tags]" class="form-control" value="<?php if($id) : echo $redis->hget('post:'.$id,'tags'); else : echo $redis->hget('user_draft_post:'.$user_id,'tags'); endif; ?>">
							</div>
                            <?php if($redis->hget('user:'.maoo_user_id(),'user_level')==10) : ?>
							<div class="form-group">
								<label>
									评级 数值越高排名越靠前
								</label>
								<input type="text" name="page[rank]" class="form-control" value="<?php if($id) : echo $redis->hget('post:'.$id,'rank');  endif; ?>">
							</div>
                            <?php endif; ?>
							<div class="form-group">
								<label>
									隐藏内容 需支付给你一定积分才可以查看
								</label>
								<div id="summernote2"><?php if($id) : echo $redis->hget('post:'.$id,'content2'); else : echo $redis->hget('user_draft_post:'.$user_id,'content2'); endif; ?></div>
								<textarea name="page[content2]" id="post-content-textarea2" class="hidden"><?php if($id) : echo $redis->hget('post:'.$id,'content2'); else : echo $redis->hget('user_draft_post:'.$user_id,'content2'); endif; ?></textarea>
							</div>
							<div class="form-group">
								<label>
									查看隐藏内容需要支付的积分
								</label>
								<input type="text" name="page[coins]" class="form-control" value="<?php if($id) : echo $redis->hget('post:'.$id,'coins'); endif; ?>">
							</div>
							<div class="submit-btn-hidden" style="display:none">
								<button type="button" class="btn btn-block btn-default">正在提交...</button>
							</div>
							<div class="submit-btn-show">
							<?php if($redis->hget('post:'.$id,'permission')==3) : ?>
							<div class="row">
								<div class="col-xs-6 col">
									<a class="btn btn-default btn-block" href="#">
										退回投稿
									</a>
								</div>
								<div class="col-xs-6 col">
									<button type="submit" class="btn btn-block btn-primary" id="publish-btn-submit">
										通过审核
									</button>
								</div>
							</div>
							<?php else : ?>
							<div class="row publist-btns">
								<div class="col-xs-6 col">
									<button type="submit" class="btn btn-block btn-default" id="publish-btn-draft">
										保存草稿
									</button>
								</div>
								<div class="col-xs-6 col">
									<button type="submit" class="btn btn-block btn-primary" id="publish-btn-submit">
										发布文章
									</button>
								</div>
							</div>
							<?php endif; ?>
							</div>
						</div>
					</div>
					<?php if($id) : ?>
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<?php endif; ?>
					<input type="hidden" name="draft" value="0" id="publish-input-draft">
				</form>
			</div>
		</div>
	</div>
	<script src="<?php echo $redis->get('site_url'); ?>/public/js/jquery.pin.min.js"></script>
				<script>
				$(document).ready(function() {
        $('#summernote').summernote({
					fontNames: ['Microsoft Yahei','Helvetica Neue', 'Arial', 'Arial Black' ],
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['fontname', ['fontname']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture'<?php if($redis->hget('user:'.maoo_user_id(),'user_level')>7) : ?>,'video'<?php endif; ?>]],
                        ['view', ['codeview']]
                    ],
					lang: 'zh-CN',
					callbacks: {
				    onImageUpload: function(files) {
							var file = files[0];
					 	  if(!/image\/\w+/.test(file.type)){
					 	   	return;
					 	  }

							var dataNumber = Math.floor(Math.random()*999+1);
							$('#summernote').summernote('insertImage', '<?php echo $redis->get('site_url'); ?>/public/img/loading.gif', function ($image) {
								$image.addClass('imgplaceholder'+dataNumber);
							});

							data = new FormData();
							data.append("file", file);
							$.ajax({
									data: data,
									type: "POST",
									url: "<?php echo $redis->get('site_url'); ?>/do/imgupload.php",
									cache: false,
									contentType: false,
									processData: false,
									success: function(url) {
										$('#summernote').summernote('insertImage', url);
										$('.note-editable img.imgplaceholder'+dataNumber).remove();
									},
									error : function(data) {
										alert('上传失败');
										$('.note-editable img.imgplaceholder'+dataNumber).remove();
									}
							});
							/*
						 	var reader = new FileReader();
						 	reader.readAsDataURL(file);
						 	reader.onload = function(e){
								var dataNumber = Math.floor(Math.random()*999+1);
								$('#summernote').summernote('insertImage', '<?php echo $redis->get('site_url'); ?>/public/img/loading.gif', function ($image) {
								  $image.addClass('imgplaceholder'+dataNumber);
								});
								$.ajax({
						        data: {
											image: this.result
										},
						        type: "POST",
						        url: "<?php echo $redis->get('site_url'); ?>/do/imgupload.php",
						        cache: false,
						        success: function(url) {
						            $('#summernote').summernote('insertImage', url);
												$('.note-editable img.imgplaceholder'+dataNumber).remove();
						        }
						    });

						 	}
							*/
				    },
						onChange: function(contents, $editable) {
							$('#post-content-textarea').val(contents);
							$(".note-toolbar").pin({containerSelector: ".note-editor"});
				    },
						onInit: function() {
				      $(".note-toolbar").pin({containerSelector: ".note-editor"});
				    }
				  }
				});
				$('#summernote2').summernote({
					fontNames: ['Microsoft Yahei','Helvetica Neue', 'Arial', 'Arial Black' ],
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['fontname', ['fontname']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture']],
                        ['view', ['codeview']]
                    ],
					lang: 'zh-CN',
					callbacks: {
				    onImageUpload: function(files) {
							var file = files[0];
					 	  if(!/image\/\w+/.test(file.type)){
					 	   	return;
					 	  }
							var dataNumber = Math.floor(Math.random()*999+1);
							$('#summernote2').summernote('insertImage', '<?php echo $redis->get('site_url'); ?>/public/img/loading.gif', function ($image) {
								$image.addClass('imgplaceholder'+dataNumber);
							});

							data = new FormData();
							data.append("file", file);
							$.ajax({
									data: data,
									type: "POST",
									url: "<?php echo $redis->get('site_url'); ?>/do/imgupload.php",
									cache: false,
									contentType: false,
									processData: false,
									success: function(url) {
										$('#summernote2').summernote('insertImage', url);
										$('.note-editable img.imgplaceholder'+dataNumber).remove();
									},
									error : function(data) {
										alert('上传失败');
										$('.note-editable img.imgplaceholder'+dataNumber).remove();
									}
							});
				    },
						onChange: function(contents, $editable) {
							$('#post-content-textarea2').val(contents);
							$(".note-toolbar").pin({containerSelector: ".note-editor"});
				    },
						onInit: function() {
				      $(".note-toolbar").pin({containerSelector: ".note-editor"});
				    }
				  }
				});
    });
		$('#publish-btn-draft').hover(function(){
			$('#publish-input-draft').val(1);
		});
		$('#publish-btn-submit').hover(function(){
			$('#publish-input-draft').val(0);
			var title = $('#pub-title').val();
			var topic = $('#pub-topic').val();
			var fmimg = $('#pub-input1').val();
			var content = editor.html();
			var tags = $('#pub-tags').val();
			$.ajax({
				type: 'POST',
				url: '<?php echo $redis->get('site_url'); ?>/do/pubform-post.php',
				data: {
					title:title,
					topic:topic,
					fmimg:fmimg,
					content:content,
					tags:tags,
					draft:2
				},
				success: function(data) {

				},
				error: function() {

				}
			});
		});
		$('.btn-block').click(function(){
			$('.submit-btn-show').css('display','none');
			$('.submit-btn-hidden').css('display','block');
		});
	</script>
<?php include('footer.php'); ?>
