<section id="widget-grid" class="">
	{if isset($fields) && $fields|@count > 0}
		<form action="" method="post" class="" enctype="multipart/form-data" >

			<div class="row">
				<article class="col-sm-12 col-md-12 col-lg-12">
					<div class="jarviswidget {if isset($fields.id.value) && $fields.id.value > 0}arviswidget-collapsed{/if}" id="wid-id-0" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-custombutton="false">
						{if isset($fields.id.value) && $fields.id.value > 0}
							<header>
								<span class="widget-icon"><i class="fa fa-edit"></i></span>
								<h2>{$formTitle} : <i>{$fields.title.value}</i></h2>
							</header>
						{else}
							<header>
								<span class="widget-icon"><i class="fa fa-edit"></i></span>
								<h2>{$formTitle}</h2>
							</header>
						{/if}
						<div>
							<div class="jarviswidget-editbox"></div>
							<div class="widget-body smart-form {*no-padding*}">
								{assign var='FieldCustomize' value = array('title','alias','category','source',"imgthumb",'tags','content','status','ordering')}
								<fieldset style="padding-bottom: 0">
									<div class="row">
										<div class="col-md-5">
											<div class="br-dashed br-grey" >
												{if $fields.imgthumb.value|count_characters > 0 }
													{img file=$fields.imgthumb.value class="img-responsive upload-imgthumb" dir="uploads/article"}
												{else}
													{img file="svg/image/500x200.svg" class="img-responsive upload-imgthumb"}
												{/if}

												<input type="file" name="imgthumbUpload" accept="image/*" class="hidden">
												{input_hidden name="imgthumb" value=$fields.imgthumb.value  }
											</div>
										</div>
										<div class="col-md-7">
											{inputs name="title" field=$fields.title}
											{inputs name="alias" field=$fields.alias}
											{inputs name="category" field=$fields.category}
											{inputs name="source" field=$fields.source}
											{inputs name="tags"}
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">{inputs name="ordering"}</div>
										<div class="col-md-6">{inputs name="status"}</div>
									</div>
								</fieldset>
								<fieldset style="padding-top: 15px;">
									{foreach $fields AS $name=>$field }
										{if !$name|in_array:$FieldCustomize}
											{inputs name=$name field=$field}
										{/if}
									{/foreach}
								</fieldset>
							</div>
						</div>
					</div>
				</article>

				<article class="col-sm-12 col-md-12 col-lg-12">
					<div class="jarviswidget" id="wid-id-1" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-custombutton="false">
						<header>
							<span class="widget-icon"><i class="fa fa-book"></i></span>
							<h2>{lang txt='Article Content'}</h2>
						</header>
						<div>
							<div class="jarviswidget-editbox"></div>
							<div class="widget-body  no-padding">
								<div class="smart-form smart-form-editor">
									<fieldset class="no-padding ">
										{inputs name="content" label=false row=false}
									</fieldset>

									<footer class="smart-form" >
										<button class="btn btn-primary" type="submit" name="back" value="1">
											<i class="fa fa-save"></i>
											Submit
										</button>
										<button class="btn btn-primary" type="submit" name="save" value="1">
											<i class="fa fa-save"></i>
											Save Form
										</button>
										<button class="btn btn-default" type="submit" name="cancel" value="cancel">
											Cancel
										</button>
									</footer>
								</div>
							</div>
						</div>
					</div>
				</article>
			</div>
		</form>
	{/if}
</section>