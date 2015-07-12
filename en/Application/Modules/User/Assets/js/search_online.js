$(function(){
	hoverItem();
	//Bottom paging
	$(window).scroll(function(){
		if ($(window).scrollTop() == $(document).height() - $(window).height()){
			loadNewPage();
		}
	}); 
});
function hoverItem(){
	$('.avatar-wrapper').mouseover(function(){
		$(this).find('.avatar').addClass('hover');
	});
	$('.avatar-wrapper').mouseleave(function(){
		$(this).find('.avatar').removeClass('hover');
	});
}
function loadNewPage() 
{
	if($('#endpage').val() == '1')
		return false;

	$('.paging-loading').show();
	$.ajax({
		url: $('#pagingUrl').val(),
		data: {
			seeking_gender: $('#seeking_gender').val(),
			page: parseInt($('#page').val()) + 1,
			minage: $('#minage').val(),
			maxage: $('#maxage').val(),
			province: $('#province').val(),
			avatar: $('#avatar').val()
		},
		type: 'POST',
		success: function(htmlData){
			$('.paging-loading').hide();
			if($.trim(htmlData) == "")
			{
				$('#endpage').val(1);
				return false;
			}
			else
			{
				$('.list-search-user').append(htmlData);
				hoverItem();
				//tang page them 1
				$('#page').val(parseInt($('#page').val()) + 1); 
			}
		}
	});
}

function seekingGender()
{
	if(window.location.href.indexOf('seeking_gender=') >= 0)
	{
		window.location.href = window.location.href.replace(/seeking_gender=([-]*\d)/, 'seeking_gender=' + $('#seeking_gender').val());
	}
	else
	{
		if(window.location.href.indexOf('?') >= 0)
			window.location.href = '/tim-kiem.html' + '&seeking_gender=' + $('#seeking_gender').val();
		else
			window.location.href = '/tim-kiem.html' + '?seeking_gender=' + $('#seeking_gender').val();
	}
}
function minAge()
{
	if(window.location.href.indexOf('minage=') >= 0)
	{
		window.location.href = window.location.href.replace(/minage=(\d+)/, 'minage=' + $('#minage').val());
	}
	else
	{
		if(window.location.href.indexOf('?') >= 0)
			window.location.href = '/tim-kiem.html' + '&minage=' + $('#minage').val();
		else
			window.location.href = '/tim-kiem.html' + '?minage=' + $('#minage').val();
	}
}
function maxAge()
{
	if(window.location.href.indexOf('maxage=') >= 0)
	{
		window.location.href = window.location.href.replace(/maxage=(\d+)/, 'maxage=' + $('#maxage').val());
	}
	else
	{
		if(window.location.href.indexOf('?') >= 0)
			window.location.href = '/tim-kiem.html' + '&maxage=' + $('#maxage').val();
		else
			window.location.href = '/tim-kiem.html' + '?maxage=' + $('#maxage').val();
	}
}
function province()
{
	if(window.location.href.indexOf('province=') >= 0)
	{
		window.location.href = window.location.href.replace(/province=(\d+)/, 'province=' + $('#province').val());
	}
	else
	{
		if(window.location.href.indexOf('?') >= 0)
			window.location.href = '/tim-kiem.html' + '&province=' + $('#province').val();
		else
			window.location.href = '/tim-kiem.html' + '?province=' + $('#province').val();
	}
}
function onlineFilter()
{
	if($('#online').attr('checked'))
	{
		if(window.location.href.indexOf('online=') >= 0)
		{
			window.location.href = window.location.href.replace(/online=(\d)/, 'online=1');
		}
		else
			window.location.href = '/tim-kiem.html' + '&online=1';
	}
	else
	{
		if(window.location.href.indexOf('online=') >= 0)
		{
			window.location.href = window.location.href.replace(/online=(\d)/, 'online=0');
		}
		else
			window.location.href = window.location.href.replace('&online=1', '');
	}
	return false;
}