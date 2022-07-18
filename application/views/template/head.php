<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=isset($this->title) ? $this->title : "서비스명"?></title>
    <?php 
        if(isset($this->css)) {
            foreach($this->css as $css) {
                echo "<link rel='stylesheet' href='/static/css/{$css}.css'>
                ";
            }
        }

        if(isset($this->js)) {
            foreach($this->js as $js) {
                echo "<script defer src='/static/js/{$js}.js'></script>
                ";
            }
        }
    ?>
</head>