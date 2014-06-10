<?php

<html>
    <head>
        <title>demo-php-form-upload</title>
    </head>
    <body>
        <form method="post" action="http://up.qiniu.com/">
         enctype="multipart/form-data">
          <input name="key" type="hidden" value="<resource_key>">
          <input name="x:<custom_name>" type="hidden" value="<custom_value>">
          <input name="token" type="hidden" value="<upload_token>">
          <input name="file" type="file" />
        </form>
    </body>
</html>
