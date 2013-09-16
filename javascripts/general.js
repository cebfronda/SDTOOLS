function user(userid) {
    $.get(getLocation()+"accounts/profile/"+userid+"/ajax", function(data) {
        $('#content-area').hide();
	$('#content-area').html(data);
        $('#content-area').show('slide', 1000).fadeIn();
    });
}

function getLocation() {
    return location.protocol+"//"+location.host+"/waqa/";
}