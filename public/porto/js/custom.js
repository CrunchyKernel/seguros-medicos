var isMobile;

const regex = /Mobi|Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i;
isMobile = regex.test(navigator.userAgent);

if(!isMobile){
	isMobile = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
}

var date = new Date();
date.setTime(date.getTime()+(365*24*60*60*1000));
document.cookie = 'isMobile=' + isMobile + '; expires=' + date.toGMTString() + '; path:/';