var dataArray = {};

function handleDragOver(event)
{
	event.stopPropagation();
    event.preventDefault();
}

function handleFileDrop(event, uploaderId, url, recipient, displayToolbar)
{

	event.stopPropagation();
	event.preventDefault();

	var files = event.originalEvent.dataTransfer.files;

	renderUploadedFilesPreview(files, uploaderId, url, recipient, displayToolbar);
	
	if ( typeof dataArray[uploaderId] == 'undefined' )
	{
		dataArray[uploaderId] = [];
	}
	for ( var i = 0;  i < files.length; i++)
	{
		dataArray[uploaderId].push(files[i]);	
	}
}

function handleFileSelect(event, uploaderId, url, recipient, displayToolbar)
{
	event.stopPropagation();
	event.preventDefault();
	var files = event.target.files;

	renderUploadedFilesPreview(files, uploaderId, url, recipient, displayToolbar);
	
	if ( typeof dataArray[uploaderId] == 'undefined' )
	{
		dataArray[uploaderId] = [];
	}
	for ( var i = 0;  i < files.length; i++)
	{
		dataArray[uploaderId].push(files[i]);	
	}
}

function launchUpload(url, uploaderId, recipient)
{
	xhr = new XMLHttpRequest();
	xhr.open('POST', url);
	var formData  = new FormData();
	var files = dataArray[uploaderId];
	for ( var i = 0; i < files.length; i++)
	{
		formData.append('file[]', files[i]);
	}
	xhr.onload = function() {
		delete dataArray[uploaderId];
		$('#' + recipient).html('Upload terminé');
	}

	xhr.upload.onprogress = function (event) {
		if ( event.lengthComputable )
		{
			var complete = (event.loaded / event.total * 100 | 0);
			$('#' + recipient + ' .progress').css('display', 'block');
			$('#' + recipient + ' .bar').css('width', complete + '%');
		}
	}

	xhr.send(formData);
}

function renderUploadedFilesPreview(files, uploaderId, url, recipient, displayToolbar)
{
	if ( displayToolbar )
	{

	}
	//var toolbar = '<button type="button" class="btn btn-primary" onclick="launchUpload(\'/admin/image-upload/\', \'' + uploaderId + '\',\'' . $this->recipientDiv . '\'); return false">"><i class="icon-upload"></i> Uploader</button> ';
	//toolbar += '<button type="button" class="btn btn-warning"><i class="icon-trash"></i> Retirer les images</button>';

//	$('#' + recipient).html(toolbar);

	for ( var i = 0; i < files.length; i++ )
	{
		var reader = new FileReader();
		reader.onload = (function(theFile) {
				return function (e) {
					var uploadedElementInfo = $('<div>');
					uploadedElementInfo.attr('class', 'uploaded-item-preview');

					var img = $('<img>');
					img.attr('src', e.target.result);
				
					img.appendTo(uploadedElementInfo);

					var info = $('<div>');
					info.attr('class', 'item-info');
					info.html('<span class="title"><b>Nom :</b>' + escape(theFile.name) + '</span><span class="type"><b>Type : </b>' + escape(theFile.type) + '</span><span class="size"><b>Taille : </b>' + escape(theFile.size) + ' octets</span><div class="breaker"></div>');

					info.appendTo(uploadedElementInfo);			
					uploadedElementInfo.appendTo('#' + recipient);
	            };
	        })(files[i]);
		reader.readAsDataURL(files[i]);
	}
}

