<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php echo $this->Html->charset(); ?>
    </head>
    <style>
        body{
            font-family: arial;
            font-size: 12px;
        }
        .content{
            width: 700px;
        }
        .titulo{
            font-size: 14px;
        }
        .numero{
            font-size: 18px;
            font-weight: bold;
        }
        hr{
            height: 2px;
            color: #000;
            background-color: #000;
        }
        table.info td{
            padding: 3px;
            font-size: 14px;
        }
        table.top_info td{
            padding: 2px;
            font-size: 14px;
        }
    </style>
    <body>
        <?php echo $this->fetch('content'); ?>
    </body>
</html>