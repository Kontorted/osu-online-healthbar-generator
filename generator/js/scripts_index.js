navigator.sayswho= (function(){
    var ua= navigator.userAgent, tem,
    M= ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
    if(/trident/i.test(M[1])){
        tem=  /\brv[ :]+(\d+)/g.exec(ua) || [];
        return 'IE '+(tem[1] || '');
    }
    if(M[1]=== 'Chrome'){
        tem= ua.match(/\b(OPR|Edge)\/(\d+)/);
        if(tem!= null) return tem.slice(1).join(' ').replace('OPR', 'Opera');
    }
    M= M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
    if((tem= ua.match(/version\/(\d+)/i))!= null) M.splice(1, 1, tem[1]);
    return M.join(' ');
})();

var spl = navigator.sayswho.split(" ");
var res = "";
for(var i = 0; i < spl.length - 1; i++) {
	res += spl[i];
	if(i != spl.length - 2) {
		res += " ";
	}
}

if(res.toLowerCase().indexOf('chrome') == -1 && res.toLowerCase().indexOf('firefox') == -1 && res.toLowerCase().indexOf('opera') == -1) {
	$('#brver').append("<p>Hey there! Looks like you're using " + res + "</p>");
	$('#brver').append('<p>Some features, such as the color pickers, might not work properly on this browser.</p>');
	$('#brver').append('<p>If this is the case, please consider using Firefox or Chrome instead.</p>');
}

if($('#useFlag').is(":checked")) {
	$("#oldFlags").prop("disabled", false);
}

$('#useFlag').change(function() {
	if($('#useFlag').is(":checked")) {
		$("#oldFlags").prop("disabled", false);
	} else {
		$("#oldFlags").prop("disabled", true);
	}
});

var options = $('#font option');
var default_text = 'The quick brown fox jumps over the lazy dog';

function setTextInOptions(array, text) {
	array.each(function() {
		$(this).text(text);
	});
}

if($('#text').val() != "") {
	setTextInOptions(options, $('#text').val());
} else if($('#username').val() != "") {
	setTextInOptions(options, $('#username').val());
}

$('#username').keyup(function() {
	if($('#text').val() == "") {
		var text = $('#username').val();
		if(text == "") {
			setTextInOptions(options, default_text);
		} else {
			setTextInOptions(options, text);
		}
	}
});

$('#text').keyup(function() {
	var text = $('#text').val();
	if(text == "" && $('#username').val() == "") {
		setTextInOptions(options, default_text);
	} else if(text == "") {
		setTextInOptions(options, $('#username').val());
	} else {
		setTextInOptions(options, text);
	}
});

$('#font').change(function() {
	$('#font').css('font-family', $('#font').val());
});

$('#colorThemeUpdate').click(function() {
	var theme = $('#colorTheme').val();
	
	switch(theme) {
		case 'Blue':
			var c1 = [43,95,116];
			var c2 = [101,197,206];
			var c3 = [27,103,136];
			var c4 = [45,162,211];
			break;
		case 'Red':
			var c1 = [115,42,44];
			var c2 = [218,119,138];
			var c3 = [137,27,30];
			var c4 = [211,45,49];
			break;			
		case 'Yellow':
			var c1 = [130,126,45];
			var c2 = [220,196,95];
			var c3 = [187,180,7];
			var c4 = [212,207,56];
			break;	
		case 'Green':
			var c1 = [42,115,64];
			var c2 = [87,210,138];
			var c3 = [27,137,61];
			var c4 = [43,209,94];
			break;	
		case 'Orange':
			var c1 = [139,92,36];
			var c2 = [255,147,95];
			var c3 = [226,122,0];
			var c4 = [232,140,32];
			break;	
		case 'Purple':
			var c1 = [91,43,116];
			var c2 = [173,116,204];
			var c3 = [120,13,175];
			var c4 = [154,45,211];
			break;		
		case 'Pink':
			var c1 = [173,36,123];
			var c2 = [230,122,197];
			var c3 = [240,92,200];
			var c4 = [239,53,171];
			break;	
		case 'Brown':
			var c1 = [91,53,38];
			var c2 = [194,85,78];
			var c3 = [152,64,30];
			var c4 = [158,69,34];
			break;
		case 'White':
			var c1 = [203,203,203];
			var c2 = [255,255,255];
			var c3 = [255,255,255];
			var c4 = [255,255,255];
			break;
	}
	
	var c1hex = '#' + x(c1[0].toString(16)) + x(c1[1].toString(16)) + x(c1[2].toString(16));
	var c2hex = '#' + x(c2[0].toString(16)) + x(c2[1].toString(16)) + x(c2[2].toString(16));
	var c3hex = '#' + x(c3[0].toString(16)) + x(c3[1].toString(16)) + x(c3[2].toString(16));
	var c4hex = '#' + x(c4[0].toString(16)) + x(c4[1].toString(16)) + x(c4[2].toString(16));
	
	$('#color1').val(c1hex);
	$('#color2').val(c2hex);
	$('#color3').val(c3hex);
	$('#color4').val(c4hex);
	$('#color5').val("#FFFFFF");
});

function checkForm() {
	
	$('.errors').empty();
	if($('#username').val() == '') {
		$('#errors').append('<p>Please, fill in your username</p>');
		return false;
	} else {
		$('#submit').css('display', 'none');
		$('#loading').css('display', '');
		return true;
	}
}

function x(s) {
	return s.length == 2 ? s : '0'+s;
}