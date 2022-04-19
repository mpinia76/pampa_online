INSTALACION
===========

1. copiar/crear los dos siguientes archivos y completar con los datos de base de datos

cp /config/db.php.default /config/db.php

cp /v2/app/Config/database.php.default /v2/app/Config/database.php

2. permisos de escritura los siguientes directorios

$ chown -R www-data:www-data /v2/app/tmp/

$ chown -R www-data:www-data /v2/vendors/Mpdf/ttfontdata

$ chown -R www-data:www-data /v2/vendors/Mpdf/tmp

$ chown -R www-data:www-data /v2/vendors/Mpdf/graph_cache