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
    </style>
    <body>
        <?php echo $this->fetch('content'); ?>
    </body>
</html>