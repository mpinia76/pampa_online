<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php echo $this->Html->charset(); ?>
        <?php echo $this->Html->css(array('jquery-ui','wickedpicker','yaml/core/base.min','form')); ?>
    <?php echo $this->Html->script(array('jquery','jquery-ui','jquery.ui.datepicker-es','wickedpicker.js','dhtml/dhtmlxcommon','dhtml/dhtmlxcontainer','dhtml/dhtmlxwindows')); ?>
    
        <?php echo $this->fetch('script'); ?>
    	<?php echo $this->Js->writeBuffer(); ?>
        <script>
	        var xpos, ypos, dhxWins, position;
		    function createWindow(id,titulo,url,w,h) {
		        xpos = xpos+20;
		        ypos = ypos+20;
		
		        if(ypos>200){ ypos = 5; }
		        if(xpos>300){ xpos = 50; }
		
		        w1 = dhxWins.createWindow(id, xpos, ypos, w, h);
		        w1.setText(titulo);
		        w1.attachURL(url);
		    }
	    </script>
    </head>
    <style>
        body{
            font-family: arial;
            font-size: 12px;
        }
        .content{
            width: 700px;
        }
        .ciudad{
            font-weight: bold;
            text-decoration: underline;
        }
        hr{
            height: 2px;
            color: #000;
            background-color: #000;
        }
        h1{
            font-size: 16px;
        }
        h2{
            font-size: 14px;
            display: block;
            padding:5px;
            background: #ccc;
        }
        table td{
            padding: 3px;
        }
        td.border{
            border-bottom: 1px solid #ccc;
        }
        .guardar {

		    font-size: 16px;
		    color: #fff;
		    background: #589B13;
		
		}
		.boton {

		    -moz-border-radius: 5px;
		    border-radius: 5px;
		    cursor: pointer;
		    font-size: 14px;
		    display: block;
		    padding: 5px;
		    text-align: center;
		    font-weight: bold;
		    margin: 5px;
		
		}
    </style>
    <body>
        <?php echo $this->fetch('content'); ?>
    </body>
</html>