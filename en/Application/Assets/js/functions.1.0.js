function formatDateTime(time){
    var dt = new Date(time);
    var then  = 'ngày ' + dt.getDate() + ' tháng ' + (dt.getMonth() + 1) + ' ' + dt.getFullYear();
    var hh = dt.getHours();
    var m  = dt.getMinutes();
    var dd = "sáng";
    var h = hh;
    if(h >= 12){
    	h = hh-12;
        dd = "chiều";
    }
    if(h == 0){
        h = 12;
    }
    m = m < 10 ? "0"+m : m;
    h = h < 10 ? "0"+h : h; 
    then += ' lúc ' + h + ':' + m + ' ' + dd;
    return then;
}

function changeAvatar(user){
    if(! user.avatar || user.approval == 0)
    {
    	if(user.sub_avatar && user.sub_avatar != "")
    		user.avatar = user.sub_avatar;
    	else{
    		if(user.gender == 1)
                user.avatar = 'no-avatar-men.jpg';
            else if(user.gender == 0)
                user.avatar = 'no-avatar-women.jpg';
    	}
    }
    return user;
}

function numberFormat(number, decimals, dec_point, thousands_sep){ 
    var n = number, c = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals;
    var d = dec_point == undefined ? "," : dec_point;
    var t = thousands_sep == undefined ? "." : thousands_sep, s = n < 0 ? "-" : "";
    var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
 
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}