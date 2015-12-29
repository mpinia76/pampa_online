// funcion para crear window
function createWindow(id,titulo,url,w,h) {

	xpos = xpos+20;
	ypos = ypos+20;
	
	if(ypos>200){
		ypos = 5;
	}
	
	if(xpos>300){
		xpos = 50;
	}
	
	w1 = dhxWins.createWindow(id, xpos, ypos, w, h);
    w1.setText(titulo);
	w1.attachURL(url);
	w1.attachEvent('onClose', Refresh);
}

function Refresh(w1){
	if (window.location!=null)
		window.location.reload();
	return true;
	
}