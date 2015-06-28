
function sendFileToUploadController(formData,status)
{
	
	var uploadURL =jQuery('#uploadForm').attr('action');
	uploadURL = uploadURL.replace("format=html", "format=json");
    var jqXHR=jQuery.ajax({
         xhr: function() {
            var xhrobj = jQuery.ajaxSettings.xhr();
            if (xhrobj.upload) {
                    xhrobj.upload.addEventListener('progress', function(event) {
                        var percent = 0;
                        var position = event.loaded || event.position;
                        var total = event.total;
                        if (event.lengthComputable) {
                            percent = Math.ceil(position / total * 100);
                        }
                        //Set progress
                        status.setProgress(percent);
                    }, false);
                }
            return xhrobj;
        },
        url: uploadURL,
        type: "POST",
        contentType:false,
        processData: false,
        cache: false,
        data: formData,
        success: function(data){

        	if(data.success)
        	{
        		status.setProgress(100);
        	}
        	else
        	{
        		status.setProgressError(data.message);
        	}
        	
        }
    }); 
 
    status.setAbort(jqXHR);
}
 

function createStatusbar(obj)
{
     this.statusbar = jQuery("<tr></tr>");
     this.filename = jQuery("<td style='width: 20%;'><div class='filename'></div></td>").appendTo(this.statusbar);
     this.size = jQuery("<td style='width: 20%;'><div class='filesize'></div></td>").appendTo(this.statusbar);
     this.progressBar = jQuery("<td style='width: 40%;'><div class='progress'><div class='bar'></div></div></td>").appendTo(this.statusbar);
     this.abort = jQuery("<td><span class='badge badge-important'>&times;</span></td>").appendTo(this.statusbar);

     jQuery("#upload-container").prepend(this.statusbar);
 
    this.setFileNameSize = function(name,size)
    {
        var sizeStr="";
        var sizeKB = size/1024;
        if(parseInt(sizeKB) > 1024)
        {
            var sizeMB = sizeKB/1024;
            sizeStr = sizeMB.toFixed(2)+" MB";
        }
        else
        {
            sizeStr = sizeKB.toFixed(2)+" KB";
        }

        var shortName = name.slice(0,28);

        this.filename.html(shortName);
        this.size.html(sizeStr);
    }
    this.setProgress = function(progress)
    {       
        var progressBarWidth =progress*this.progressBar.width()/ 100;  
        this.progressBar.find('.bar').animate({ width: progressBarWidth }, 10).html(progress + "% ");
        if(parseInt(progress) >= 100)
        {
         	this.abort.find('span').addClass('badge-success').removeClass('badge-important');
            this.abort.find('span').html('OK');
        }
    }
    this.setProgressError = function(error)
    {
    	this.progressBar.find('div:first').addClass('progress progress-danger').removeClass('progress');
    	this.progressBar.find('.bar').animate({ width: this.progressBar.width() }, 10).html("<div style='color: white;'>" + error + "</div>");
    	this.abort.find('span').addClass('badge-important').removeClass('badge-success').html ("error");
    }
    this.setAbort = function(jqxhr)
    {
        var sb = this.statusbar;
        this.abort.click(function()
        {
       	    jqxhr.abort();
            sb.hide();
        });
    }
}
function handleFileUpload(files,obj)
{
   for (var i = 0; i < files.length; i++) 
   {
        var fd = new FormData();
        fd.append('Filedata[]', files[i]);
        fd.append('folder', document.getElementById('folder').value);
        fd.append(document.getElementById('form-token').value, '1');
        
        var status = new createStatusbar(obj); //To set progress.
     
        status.setFileNameSize(files[i].name,files[i].size);
        sendFileToUploadController(fd,status);
   }
   
   
  
}
jQuery(function()
{
var obj = jQuery("#dragandrophandler");

obj.on('dragenter', function (e) 
{
    e.stopPropagation();
    e.preventDefault();
    jQuery(this).css('border', '2px solid #0B85A1');
});
obj.on('dragover', function (e) 
{
     e.stopPropagation();
     e.preventDefault();
});
obj.on('drop', function (e) 
{
 
     jQuery(this).css('border', '2px dotted #0B85A1');
     e.preventDefault();
     var files = e.originalEvent.dataTransfer.files;
 
     //We need to send dropped files to Server
     handleFileUpload(files,obj);
});
jQuery(document).on('dragenter', function (e) 
{
    e.stopPropagation();
    e.preventDefault();
});
jQuery(document).on('dragover', function (e) 
{
  e.stopPropagation();
  e.preventDefault();
  obj.css('border', '2px dotted #0B85A1');
});
jQuery(document).on('drop', function (e) 
{
    e.stopPropagation();
    e.preventDefault();
});

// Reload folder iFrame when exit
jQuery('#uploadModal').on('hide', function () {
	jQuery('#folderframe').attr('src', function (i, val) { 
		// Setting folder name in iFrame url
		return val.replace(/&folder=.*&/,"&folder="+document.getElementById('folder').value+"&") ;
	});
});

});

