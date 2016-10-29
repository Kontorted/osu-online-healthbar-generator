$(document).ready(function() {
    $('#help1').tooltipster({
    	delay: 0,
        content: $('<span class="tooltip">When checked, will resize your avatar maintaining its proportions instead of<br>forcing fixed proportions.<br><br>If used with circular avatar shape, black bars might appear.<br><br>If used with squared avatar shape, will also resize avatar border to fit your avatar.<br><br>I recommend not to check this unless your avatar becomes noticeably distorted.</span>')
    });
});

var submittedImg = $("#submittedImage").val();

if(submittedImg !== "") {
	$("#imageSelectorDivActive").css("display", "none");
	$("#imageSelectorDivInactive").css("display", "inline-block");
}

function resetImgForm() {
	$("#imageSelectorDivActive").css("display", "inline-block");
	$("#imageSelectorDivInactive").css("display", "none");
};

function checkAdvancedOptionsForm() {
	$('.errors').empty();
	
	var overrideImage = $('#imageOverride').is(":checked");
	var noAvatar = $('#noAvatar').is(":checked");
	var file = $('#customImage').prop('files')[0];
	var filename = $('#customImage').val();
	
	if(noAvatar || !overrideImage || (filename === "" && submittedImg !== "")) {
		file = false;
		return true;
	} else {
		if(file === undefined || file === false) {
			$('#errors').append('<p>Please select an image</p>');
			return false;
		}
		
		if(file.size > 1048576) {
			$('#errors').append('<p>You cannot upload files larger than 1MB</p>');
			return false;
		}
		
		var ext = filename.split('.').pop().toLowerCase();
		
		if(['png','gif','jpg','jpeg'].indexOf(ext) === -1) {
			$('#errors').append('<p>Only png, gif and jpg/jpeg files are supported</p>');
			return false;
		}
		
		$("#submittedImage").val(filename);
		
		return true;
	}
}

if(!$('#flagOverride').is(":checked")) {
	$("#flagOverrideData").prop("disabled", true);
	$("#flagOverrideData").css('color', 'gray');
} else {
	$("#flagOverrideData").prop("disabled", false);
	$("#flagOverrideData").css('color', '');
}

if($('#imageOverride').is(":checked")) {
	$("#customImage").prop("disabled", false);
} else {
	$("#customImage").prop("disabled", true);
}

if($('#noAvatar').is(":checked")) {
	$("#customImage").prop("disabled", true);
	$("#imageOverride").prop("disabled", true);
} else {
	$("#imageOverride").prop("disabled", false);
	if($('#imageOverride').is(":checked")) {
		$("#customImage").prop("disabled", false);
	}
}

$('#flagOverride').change(function() {
	if($('#flagOverride').is(":checked")) {
		$("#flagOverrideData").prop("disabled", false);
		$("#flagOverrideData").css('color', '');
	} else {
		$("#flagOverrideData").prop("disabled", true);
		$("#flagOverrideData").css('color', 'gray');
	}
});

$('#imageOverride').change(function() {
	if($('#imageOverride').is(":checked")) {
		$("#customImage").prop("disabled", false);
	} else {
		$("#customImage").prop("disabled", true);
	}
});

$('#noAvatar').change(function() {
	if($('#noAvatar').is(":checked")) {
		$("#customImage").prop("disabled", true);
		$("#imageOverride").prop("disabled", true);
	} else {
		$("#imageOverride").prop("disabled", false);
		if($('#imageOverride').is(":checked")) {
			$("#customImage").prop("disabled", false);
		}
	}
});

var countryOptions = $('#flagOverrideData option');
var countrySelector = $('#flagOverrideData');
var selected = countrySelector.val();

countryOptions.each(function() {
	$(this).text(codes[$(this).val()]);
});

countryOptions.sort(function(a,b) {
	return $(a).text().toUpperCase().localeCompare($(b).text().toUpperCase());
});

countrySelector.html(countryOptions);
countrySelector.val(selected);
