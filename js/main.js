
function trackChanges(form) {
	var post = {};
	post["type"] = "isdifferent";
	form.find("input[type='text'], input[type='hidden'], textarea").each(function(){
		$that = $(this);
		post[$that.attr("name")] = $that.val();
	});
	console.log(post);
	var request = $.ajax({
		type: "POST",
		url: "dostuff.php",
		data: post
	});
	request.done(function(msg) {
		$(form.find("div.msg")[0]).html(msg);
		console.log("It's different");
	});
	request.fail(function(jqXHR, textStatus) {
		// console.log( " Failed: " + textStatus );
	});
}

function progressHandlingFunction() {
	return;
}

function putMessage(e, that){
	e.preventDefault();
	$this = $(that), $parent = $this.parent(), from = $this.data("from"), to = $this.data("to"), msg = tinymce.activeEditor.getContent(), fromname = $this.data("fromname"), published = $this.data("publish"), $form = $parent.parent().parent()[0], fd = new FormData($form);
	// console.log("Parent: ", $parent);
	var files = Array(), data = Object();

	data["type"] = "sendmsg", data["from"] = from, data["to"] = to, data["msg"] = msg, data["published"] = published;

	// { type: "sendmsg", from: from, to: to, msg: msg, published: published }

	$parent.find("input[type='hidden']").each(function(k, v){
		files[k] = $(v).attr("value");
	});


	$.each(files, function(k,v){
		var index = k+1;
		data["img"+index] = v;
	});

	// var json = JSON.stringify(data); 

	fd.append("published",published);
	if (msg != "") {
		var request = $.ajax({
			type: "POST",
			url: "dostuff.php",
			data: data
			// data: { type: "sendmsg", from: from, to: to, msg: msg, published: published }
		});
		request.done(function(returned) {
			if (returned != "Didn't work.") {
				if (published == 1) {
					$pparent = $parent.parent();
					$("div.message:last").after('<div class="message"><h3 class="from">'+fromname+'</h3><span class="timestamp">'+returned+'</span>'+msg+'</div>');					
				}
				else {
					window.open("preview.php?id="+returned, "_blank");
				}
				
			}
		});
		request.fail(function(jqXHR, textStatus) {
		});
	}
}

var numfiles = 0, imagefiles = Array();

function bindEditStuff(numberOfEntries) {

	var uploader = Array();

	// for (var i = 0; i < numberOfEntries; i++) {

	//////////////////////////////////////////////
	// Hiding the viewed and replied messages  //
	//////////////////////////////////////////////

	$("a.hideviewed").on("click", function(e){
		e.preventDefault();
		var $this = $(this), id = $this.parent().parent().parent().attr("id");
		var request = $.ajax({
			type: "POST",
			url: "dostuff.php",
			data: { id: id, type: "hideviewed" }
		});
		request.done(function(msg) {
			$this.parent().html("");
		});
		request.fail(function(jqXHR, textStatus) {
		});
	});

	$("a.hidereplied").on("click", function(e){
		e.preventDefault();
		var $this = $(this), id = $this.parent().parent().parent().attr("id");
		var request = $.ajax({
			type: "POST",
			url: "dostuff.php",
			data: { id: id, type: "hidereplied" }
		});
		request.done(function(msg) {
			$this.parent().html("");
		});
		request.fail(function(jqXHR, textStatus) {

		});
	});

	$(".filecontainer").each(function(k,v){
		var $v = $(v), id = $v.attr("id"), filelist = $v.find(".filelist"), uploadfiles = $v.find(".uploadfiles");
		uploader[k] = new plupload.Uploader({
			runtimes : 'html5,html4',
			browse_button : 'pickfiles'+id,
			container: id,
			max_file_size : '10mb',
			url : 'dostuff.php',
			resize : {width : 320, height : 240, quality : 90},
			filters : [
				{title : "Image files", extensions : "jpg,gif,png"},
			],
			multipart : true
		});

		uploader[k].bind('Init', function(up, params) {
			filelist.html("");
		});

		uploader[k].init();

		uploader[k].bind('FilesAdded', function(up, files) {
			$.each(files, function(k, v){
				filelist.append('<div id="' + v.id + '">' + files[k].name + ' (' + plupload.formatSize(files[k].size) + ') <b></b></div>');
			});
		});

		uploader[k].bind('UploadProgress', function(up, file) {
			$("#"+file.id).find('b').html('<span>' + file.percent + "%</span>");
			if (file.percent == 100) {
				if (imagefiles.indexOf(file.name) == -1) {
					var $par = $("#"+file.id).parent().parent().parent();
					$par.append("<input type='hidden' value='"+file.name+"' name='file"+numfiles+"' />");
					imagefiles.push(file.name);
					numfiles++;
				}
			}
		});

		// console.log("Find uploadfiles: ", $v.find(".uploadfiles"));

		uploadfiles.on("click", function(e){
			e.preventDefault();

			uploader[k].start();
			return false;
		});
	});
	
	

	//////////////////////////////////////
	// Bind the "back to list" button  //
	//////////////////////////////////////

	$("a.backtolist").on("click", function(e){
		e.preventDefault();
		$("input[type='text'], input[type='textarea']").attr("value", "");
		$(".edit").slideUp();
	});	

	////////////////////////////
	// Subtopic click stuff  //
	////////////////////////////

	$("a.subtopic").on("click", function(e){
		e.preventDefault();
		$this = $(this);
		$this.parent().parent().find("li").slideToggle();
		if ($this.css("background-image").indexOf("arrow.png") != -1) {
			$this.css({"background-image":"url(./img/arrow_down.png)"});
		}
		else {
			$this.css({"background-image":"url(./img/arrow.png)"});
		}


	});

	///////////////////////////////////////
	// Bind the clicking of a checkbox  //
	///////////////////////////////////////

	$("input[type='checkbox']").on("click", function(e){
		// e.preventDefault();
		$this = $(this);
		$form = $($this.closest("form")[0]);
		$hidden = $($form.find("input[name='categories']")[0]);
		var cats = "";
		$form.find("input[type='checkbox']").each(function(){
			$that = $(this);
			if ($that.is(":checked")) cats += $that.attr("value") + ",";
		});
		cats = cats.substring(0, cats.length - 1);
		$hidden.attr("value", cats);
		trackChanges($form);
	});

	///////////////////////////////
	// Bind the username click  //
	///////////////////////////////

	$("li a.user").on("click", function(e){
		e.preventDefault();
		that = this;
		$("form").each(function(e){

			if ($(this).parent()[0] != $(that).parent()[0]) $(this).slideUp;

		});
		var $this = $(this), id = $this.data().id, type = $this.data.type;
		$form = $($this.parent().find("form")[0]);
		$form.slideToggle();
	});

	//////////////////////////////
	// Bind the submit button  //
	//////////////////////////////

	$("input[type='submit'].submit").on("click", function(e){
		e.preventDefault();
		$this = $(this);
		$form = $($this.parent()[0]);
		
		var post = {};
		$form.find("input[type='text'], input[type='hidden'], textarea").each(function(){
			$that = $(this);
			post[$that.attr("name")] = $that.val();
		});

		var request = $.ajax({
			type: "POST",
			url: "dostuff.php",
			data: { type: "update", id: post.id, first: post.first, last: post.last, email: post.email, categories: post.categories, individuals: post.individuals, sidebar: post.sidebar }
		});
		request.done(function(msg) {
			$($form.find("div.msg")[0]).html(msg);
		});
		request.fail(function(jqXHR, textStatus) {

		});
	});

	$("a.close").on("click", function(e){
		e.preventDefault();
		$parent = $(this).parent();
		$parent.slideUp();
	});

	///////////////////////////////////////////
	// Bind the message send submit button  //
	///////////////////////////////////////////

	$("input.sendmsg").on("click", function(e){ putMessage(e, this); });

	///////////////////////////////
	// Bind the preview button  //
	///////////////////////////////

	$("a.preview").on("click", function(e){ putMessage(e, this); });

	/////////////////////////////////
	// Check for unsaved changes  //
	/////////////////////////////////

	$("input[type='text'], textarea").on("keyup", function(){
		$form = $(this).parent().parent();
		trackChanges($form);
	});

	//////////////////////////
	// Bind the dropdowns  //
	//////////////////////////

	$("select.individual").on("change", function(e){
		e.preventDefault();
		var csv = "", $this = $(this), $parent = $this.parent(), $li = $parent.parent();
		$parent.find("select.individual").each(function(k,v){
			if ($(v).val() != "") csv += $(v).val() + ",";
			console.log("V: ", $(v).val());
		});
		csv = csv.substring(0, csv.length - 1);
		$($parent.find("input[type='hidden'][name='individuals']")[0]).val(csv);
		console.log("LI: ", $li);
		trackChanges($li);

	});
}

$(document).ready(function(){
	bindEditStuff(3);
	$(".messages").animate({ scrollTop: $(this).height() }, 0);
	$("form").hide();
	tinymce.init({
	    selector: "textarea.sendmsg",
	    plugins: [
	    "lists link print anchor template",
	    "searchreplace visualblocks code fullscreen",
	    "insertdatetime media table contextmenu paste"
	  ],
	  toolbar: "undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link",
	 });

});
